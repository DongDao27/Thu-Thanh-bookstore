<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeploymentCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_incomplete_settings_never_claim_release_readiness(): void
    {
        $this->artisan('app:deployment-check')->expectsOutput('FAIL Production/staging environment')->assertFailed();
    }

    public function test_complete_configuration_passes_but_sync_queue_fails_without_exposing_secrets(): void
    {
        config([
            'app.env' => 'staging', 'app.debug' => false,
            'app.url' => 'https://api.example.test', 'app.frontend_url' => 'https://books.example.test',
            'session.driver' => 'database', 'session.secure' => true, 'session.http_only' => true,
            'session.domain' => null, 'session.same_site' => 'lax',
            'cache.default' => 'database', 'queue.default' => 'database',
            'mail.default' => 'resend', 'services.resend.key' => 'fixture-private', 'mail.from.address' => 'mail@example.test',
            'broadcasting.default' => 'pusher', 'broadcasting.connections.pusher.key' => 'fixture-key',
            'broadcasting.connections.pusher.secret' => 'fixture-private', 'broadcasting.connections.pusher.app_id' => 'fixture-id',
            'payments.vnpay.deferred' => false, 'payments.vnpay.merchant' => 'fixture',
            'payments.vnpay.secret' => 'fixture-private', 'payments.vnpay.return_url' => 'https://books.example.test/payment/vnpay-return',
        ]);
        $this->artisan('app:deployment-check')->assertSuccessful();
        config(['queue.default' => 'sync']);
        $this->artisan('app:deployment-check')->expectsOutput('FAIL Persistent cache and asynchronous queue')->doesntExpectOutputToContain('fixture-private')->assertFailed();
    }
}
