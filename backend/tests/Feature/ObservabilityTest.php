<?php

namespace Tests\Feature;

use App\Models\OperationalAlert;
use App\Models\User;
use App\Notifications\OperationalAlertNotification;
use App\Services\HostMonitor;
use App\Services\OperationalAlerts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ObservabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_slow_real_postgresql_query_alerts_once_without_sql_or_bindings(): void
    {
        config(['operations.enabled' => true, 'operations.slow_query_ms' => 50]);
        DB::select('SELECT pg_sleep(0.08), ? AS marker', ['private-binding-fixture']);
        DB::select('SELECT pg_sleep(0.08), ? AS marker', ['second-private-binding']);
        $this->assertDatabaseCount('operational_alerts', 1);
        $alert = OperationalAlert::firstOrFail();
        $this->assertSame('slow_query', $alert->code);
        $this->assertGreaterThanOrEqual(50, $alert->context['duration_ms']);
        $this->assertStringNotContainsString('private', $alert->toJson());
        $this->assertArrayNotHasKey('sql', $alert->context);
        $this->assertArrayNotHasKey('bindings', $alert->context);
    }

    public function test_exception_is_recorded_without_message_or_request_secrets(): void
    {
        config(['operations.enabled' => true]);
        Route::get('/api/v1/_ops_failure', fn () => throw new \RuntimeException('private-error-message'));
        $this->getJson('/api/v1/_ops_failure')->assertStatus(500)->assertDontSee('private-error-message');
        $alert = OperationalAlert::where('code', 'exception')->firstOrFail();
        $this->assertSame(['exception_class' => \RuntimeException::class], $alert->context);
    }

    public function test_configured_alert_delivery_is_queued_without_sensitive_context(): void
    {
        config(['operations.enabled' => true, 'operations.alert_email' => 'ops@example.test']);
        Notification::fake();
        app(OperationalAlerts::class)->record('host_memory_percent', ['value' => 98, 'password' => 'private']);
        Notification::assertSentOnDemand(OperationalAlertNotification::class);
        $this->assertArrayNotHasKey('password', OperationalAlert::firstOrFail()->context);
        $notification = new OperationalAlertNotification('host_memory_percent');
        $this->assertTrue($notification->afterCommit);
        $this->assertSame(['mail'], $notification->via(new \stdClass));
        $this->assertStringContainsString('host_memory_percent', $notification->toMail(new \stdClass)->subject);
    }

    public function test_host_collector_reads_real_metrics_and_demo_threshold_alerts(): void
    {
        config(['operations.enabled' => true, 'operations.thresholds.memory_percent' => 0]);
        $metrics = app(HostMonitor::class)->sample();
        foreach (['cpu_percent', 'memory_percent', 'disk_percent'] as $metric) {
            $this->assertGreaterThanOrEqual(0, $metrics[$metric]);
            $this->assertLessThanOrEqual(100, $metrics[$metric]);
        }
        $this->assertGreaterThanOrEqual(0, $metrics['network_bytes_per_second']);
        $this->assertContains('memory_percent', app(HostMonitor::class)->check($metrics));
        $this->assertDatabaseHas('operational_alerts', ['code' => 'host_memory_percent']);
        $this->assertNotNull(Cache::get('ops.host_snapshot'));
        // Zero is an intentional demo threshold, not evidence of a production outage.
    }

    public function test_monitor_command_and_failure_paths_are_observable(): void
    {
        $this->artisan('ops:monitor')->assertFailed();
        config(['operations.enabled' => true]);
        $this->mock(HostMonitor::class, function ($mock) {
            $mock->shouldReceive('sample')->once()->andThrow(new \RuntimeException('private collector details'));
        });
        $this->artisan('ops:monitor')->expectsOutput('Host collection failed; check the Python binary and psutil installation.')->assertFailed();
        $this->assertDatabaseHas('operational_alerts', ['code' => 'monitor_failure']);
    }

    public function test_operations_is_admin_only_and_opt_in_is_safe_before_migration(): void
    {
        $this->getJson('/api/v1/admin/operations')->assertUnauthorized();
        $this->actingAs(User::factory()->create())->getJson('/api/v1/admin/operations')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))->getJson('/api/v1/admin/operations')->assertOk()->assertJsonPath('data.enabled', false)->assertJsonPath('data.alerts', []);
    }
}
