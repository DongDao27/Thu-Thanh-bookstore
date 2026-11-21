<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnvironmentCheck extends Command
{
    protected $signature = 'app:environment-check';

    protected $description = 'Check runtime and PostgreSQL without displaying credentials';

    public function handle(): int
    {
        if (! extension_loaded('pdo_pgsql') || config('database.default') !== 'pgsql') {
            $this->error('PostgreSQL and pdo_pgsql are required (D105).');

            return self::FAILURE;
        }

        try {
            $database = DB::selectOne("SELECT current_setting('server_version_num') AS version, current_database() AS name, current_setting('TimeZone') AS timezone");
        } catch (\Throwable) {
            $this->error('Database connection failed. Check private env configuration.');

            return self::FAILURE;
        }

        if ((int) $database->version < 170000 || $database->timezone !== 'UTC') {
            $this->error('PostgreSQL >=17 and a UTC connection are required.');

            return self::FAILURE;
        }

        $this->info('PHP '.PHP_VERSION.'; Laravel '.app()->version().'; PostgreSQL '.$database->version);
        $this->info('Database '.$database->name.'; connection timezone '.$database->timezone);

        return self::SUCCESS;
    }
}
