<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EnvironmentTest extends TestCase
{
    public function test_health_endpoint_boots_without_exposing_configuration(): void
    {
        $this->get('/up')->assertOk()->assertDontSee('DB_PASSWORD');
    }

    public function test_isolated_postgresql_database_supports_required_constraints(): void
    {
        $this->assertSame('thu_thanh_test', DB::selectOne('SELECT current_database() AS db')->db);
        $this->assertSame('pgsql', DB::connection()->getDriverName());
        $this->assertGreaterThanOrEqual(170000, (int) DB::selectOne("SELECT current_setting('server_version_num') AS version")->version);
        $this->artisan('app:environment-check')->assertSuccessful();
    }
}
