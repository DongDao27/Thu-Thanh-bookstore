<?php

namespace Tests\Feature;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Mail;
use Resend\Client;
use Resend\Transporters\HttpTransporter;
use Resend\ValueObjects\ApiKey;
use Resend\ValueObjects\Transporter\BaseUri;
use Resend\ValueObjects\Transporter\Headers;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Tests\TestCase;

class ResendMailTest extends TestCase
{
    private function providerResponse(int $status, array $body): void
    {
        $http = new HttpClient(['handler' => HandlerStack::create(new MockHandler([
            new Response($status, ['Content-Type' => 'application/json'], json_encode($body)),
        ]))]);
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
}
