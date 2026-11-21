<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Telescope\Contracts\EntriesRepository;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Tests\TestCase;

class DeveloperToolsTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $before = $_ENV['TELESCOPE_ENABLED'] ?? 'false';
        $_ENV['TELESCOPE_ENABLED'] = $_SERVER['TELESCOPE_ENABLED'] = 'true';
        try {
            return parent::createApplication();
        } finally {
            $_ENV['TELESCOPE_ENABLED'] = $_SERVER['TELESCOPE_ENABLED'] = $before;
        }
    }

    public function test_docs_and_telescope_deny_guest_and_customer_even_in_local_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'local');
        foreach (['/docs/api', '/docs/api.json', '/telescope', '/telescope/telescope-api/requests'] as $path) {
            $this->get($path)->assertForbidden();
        }
        $this->actingAs(User::factory()->create());
        foreach (['/docs/api', '/docs/api.json', '/telescope'] as $path) {
            $this->get($path)->assertForbidden();
        }
    }

    public function test_admin_views_telescope_and_auto_generated_docs_include_scoped_api(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get('/telescope')->assertOk()->assertHeader('Cache-Control', 'no-store, private');
        $response = $this->getJson('/docs/api.json')->assertOk();
        $this->assertArrayHasKey('/auth/tokens', $response->json('paths'));
        $this->assertArrayHasKey('/integrations/products', $response->json('paths'));
        $this->assertArrayHasKey('/admin/operations', $response->json('paths'));
        $this->assertSame([['bearerToken' => []]], $response->json('paths./integrations/products.get.security'));
        $this->assertSame([['sessionCookie' => []]], $response->json('paths./admin/operations.get.security'));
        $this->assertGreaterThanOrEqual(50, count($response->json('paths')));
        $this->get('/docs/api')->assertOk();
    }

    public function test_real_telescope_recording_persists_only_redacted_content(): void
    {
        $user = User::factory()->create(['email' => 'private-user@example.test']);
        $this->actingAs($user);
        Telescope::$entriesQueue = [];
        Telescope::startRecording();
        try {
            Telescope::recordRequest(IncomingEntry::make(['method' => 'POST', 'uri' => '/auth/login?token=private', 'response_status' => 200, 'headers' => ['Authorization' => 'Bearer private-token'], 'payload' => ['password' => 'private-password'], 'response' => ['token' => 'private-response']]));
            Telescope::recordQuery(IncomingEntry::make(['sql' => "SELECT 'private-sql'", 'bindings' => ['private-binding'], 'time' => 125]));
            Telescope::recordException(IncomingEntry::make(['class' => \RuntimeException::class, 'message' => 'private-message', 'trace' => [['args' => ['private-arg']]]]));
            Telescope::store(app(EntriesRepository::class));
            $content = DB::table('telescope_entries')->pluck('content')->join(' ');
            $this->assertStringNotContainsString('private-', $content);
            $this->assertStringContainsString('[redacted]', $content);
            $this->assertGreaterThanOrEqual(3, DB::table('telescope_entries')->count());
        } finally {
            Telescope::stopRecording();
            Telescope::$entriesQueue = [];
        }
    }
}
