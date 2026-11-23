<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscription;
use App\Notifications\NewsletterSubscribed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_guest_can_subscribe_with_email_only_and_normalized_email(): void
    {
        $this->postJson('/api/v1/newsletter/subscriptions', ['email' => ' Reader@Example.Test '])
            ->assertStatus(202)->assertJsonPath('data.message', 'Đã ghi nhận đăng ký nhận bản tin.')
            ->assertJsonMissingPath('data.email')->assertJsonMissingPath('data.id');
        $this->assertDatabaseHas('newsletter_subscriptions', ['email' => 'reader@example.test']);
        $this->assertNotNull(NewsletterSubscription::first()->consented_at);
        $this->assertDatabaseCount('users', 0);
        Notification::assertSentOnDemand(NewsletterSubscribed::class, fn ($notification, $channels, $notifiable) => $channels === ['mail'] && $notifiable->routes['mail'] === 'reader@example.test');
    }

    public function test_duplicate_is_idempotent_and_does_not_reveal_existing_subscription(): void
    {
        $body = ['email' => 'reader@example.test'];
        $first = $this->postJson('/api/v1/newsletter/subscriptions', $body)->assertStatus(202)->json('data');
        $consent = NewsletterSubscription::first()->consented_at;
        $this->travel(5)->minutes();
        $again = $this->postJson('/api/v1/newsletter/subscriptions', ['email' => 'READER@EXAMPLE.TEST'])->assertStatus(202)->json('data');
        $this->assertSame($first, $again);
        $this->assertDatabaseCount('newsletter_subscriptions', 1);
        $this->assertTrue(NewsletterSubscription::first()->consented_at->equalTo($consent));
        Notification::assertSentOnDemandTimes(NewsletterSubscribed::class, 1);
    }

    public function test_invalid_or_missing_email_and_unexpected_fields_do_not_write(): void
    {
        foreach ([[], ['email' => 'not-an-email'], ['email' => ['nested@example.test']], ['email' => str_repeat('a', 255).'@example.test'], ['email' => 'reader@example.test', 'consent' => true], ['email' => 'reader@example.test', 'role' => 'admin']] as $body) {
            $this->postJson('/api/v1/newsletter/subscriptions', $body)->assertUnprocessable();
        }
        $this->assertDatabaseCount('newsletter_subscriptions', 0);
        Notification::assertNothingSent();
    }

    public function test_newsletter_is_rate_limited(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/v1/newsletter/subscriptions', ['email' => 'reader@example.test'])->assertStatus(202);
        }
        $this->postJson('/api/v1/newsletter/subscriptions', ['email' => 'reader@example.test'])->assertStatus(429);
        $this->assertDatabaseCount('newsletter_subscriptions', 1);
    }

    public function test_web_csrf_middleware_is_present_and_rejects_a_request_without_token(): void
    {
        // Laravel bypasses CSRF in testing by default; exercise the real guard.
        $this->app['env'] = 'local';
        $this->postJson('/api/v1/newsletter/subscriptions', ['email' => 'reader@example.test'])->assertStatus(419);
        $this->assertDatabaseCount('newsletter_subscriptions', 0);
    }
}
