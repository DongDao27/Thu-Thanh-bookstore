<?php

namespace Tests\Feature;

use App\Notifications\NewsletterSubscribed;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Resend\Client;
use Resend\Transporters\HttpTransporter;
use Resend\ValueObjects\ApiKey;
use Resend\ValueObjects\Transporter\BaseUri;
use Resend\ValueObjects\Transporter\Headers;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Tests\TestCase;

class ResendMailTest extends TestCase
{
    private array $requests = [];

    private function providerResponse(int $status, array $body): void
    {
        $stack = HandlerStack::create(new MockHandler([
            new Response($status, ['Content-Type' => 'application/json'], json_encode($body)),
        ]));
        $stack->push(Middleware::history($this->requests));
        $http = new HttpClient(['handler' => $stack]);
        $this->app->instance(Client::class, new Client(new HttpTransporter(
            $http, BaseUri::from('api.resend.com'), Headers::withAuthorization(ApiKey::from('fixture-only')),
        )));
        config(['mail.default' => 'resend']);
    }

    public function test_native_mailer_uses_resend_sdk_and_returns_provider_message_id(): void
    {
        $this->providerResponse(200, ['id' => 'fixture-message-id']);
        $sent = Mail::raw('Fixture, never sent to a network.', fn ($message) => $message->to('reader@example.test')->subject('Fixture'));
        $this->assertSame('fixture-message-id', $sent->getOriginalMessage()->getHeaders()->get('X-Resend-Email-ID')->getBodyAsString());
    }

    public function test_provider_rejection_is_a_failure_for_queue_retry_not_success(): void
    {
        $this->providerResponse(429, ['name' => 'rate_limit_exceeded', 'message' => 'Fixture throttled']);
        $this->expectException(TransportExceptionInterface::class);
        Mail::raw('Fixture', fn ($message) => $message->to('reader@example.test')->subject('Fixture'));
    }

    public function test_newsletter_confirmation_is_rendered_and_sent_through_native_resend(): void
    {
        $this->providerResponse(200, ['id' => 'fixture-newsletter-id']);
        config(['app.frontend_url' => 'https://nhasachthuthanh.id.vn']);
        Notification::route('mail', 'reader@example.test')->notifyNow(new NewsletterSubscribed);
        $this->assertCount(1, $this->requests);
        $request = $this->requests[0]['request'];
        $this->assertSame('https://api.resend.com/emails', (string) $request->getUri());
        $payload = json_decode((string) $request->getBody(), true);
        $this->assertStringContainsString('reader@example.test', implode(',', $payload['to']));
        $this->assertStringContainsString('Bạn đã đăng ký nhận bản tin của Nhà sách Thu Thanh thành công.', $payload['text']);
        $this->assertStringContainsString('https://nhasachthuthanh.id.vn', $payload['html']);
    }
}
