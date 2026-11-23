<?php

namespace Tests\Feature;

use App\Notifications\NewsletterSubscribed;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NewsletterQueueTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        config(['queue.default' => 'database', 'mail.default' => 'array']);
    }

    public function test_confirmation_waits_for_commit_and_the_worker_sends_it_once(): void
    {
        Event::fake([NotificationSent::class]);
        DB::beginTransaction();
        $this->postJson('/api/v1/newsletter/subscriptions', ['email' => 'reader@example.test'])->assertAccepted();
        $this->assertDatabaseCount('jobs', 0);
        DB::commit();
        $this->assertDatabaseCount('jobs', 1);
        $payload = json_decode(DB::table('jobs')->value('payload'), true);
        $this->assertStringNotContainsString('reader@example.test', $payload['data']['command']);
        $this->assertNotSame($payload['data']['command'], decrypt($payload['data']['command']));

        $this->postJson('/api/v1/newsletter/subscriptions', ['email' => 'READER@EXAMPLE.TEST'])->assertAccepted();
        $this->assertDatabaseCount('jobs', 1);
        $this->artisan('queue:work', ['--once' => true, '--tries' => 1])->assertSuccessful();
        $this->assertDatabaseCount('jobs', 0);
        $this->assertDatabaseCount('failed_jobs', 0);
        Event::assertDispatchedTimes(NotificationSent::class, 1);
    }

    public function test_rolling_back_a_subscription_does_not_queue_confirmation(): void
    {
        DB::beginTransaction();
        $this->postJson('/api/v1/newsletter/subscriptions', ['email' => 'rollback@example.test'])->assertAccepted();
        DB::rollBack();
        $this->assertDatabaseCount('newsletter_subscriptions', 0);
        $this->assertDatabaseCount('jobs', 0);
    }

    public function test_confirmation_contains_the_requested_message_and_a_trusted_store_link(): void
    {
        config(['app.frontend_url' => 'https://nhasachthuthanh.id.vn']);
        $notification = new NewsletterSubscribed;
        $this->assertInstanceOf(ShouldBeEncrypted::class, $notification);
        $this->assertTrue($notification->afterCommit);
        $this->assertSame(3, $notification->tries);
        $this->assertSame([30, 120], $notification->backoff());
        $mail = $notification->toMail((object) []);
        $this->assertContains('Bạn đã đăng ký nhận bản tin của Nhà sách Thu Thanh thành công.', $mail->introLines);
        $this->assertSame('https://nhasachthuthanh.id.vn', $mail->actionUrl);
    }
}
