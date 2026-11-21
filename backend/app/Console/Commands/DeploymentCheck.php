<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeploymentCheck extends Command
{
    protected $signature = 'app:deployment-check';

    protected $description = 'Fail closed on incomplete production configuration without revealing secrets';

    public function handle(): int
    {
        $https = static function (mixed $value): bool {
            $url = is_string($value) ? parse_url($value) : false;

            return $url && ($url['scheme'] ?? '') === 'https' && ! empty($url['host'])
                && ! isset($url['user']) && ! isset($url['pass']) && ! isset($url['query']) && ! isset($url['fragment']);
        };
        $broadcasting = config('broadcasting.default');
        $pollingOnly = in_array($broadcasting, [null, 'null'], true);
        $pusherReady = $broadcasting === 'pusher'
            && collect(['key', 'secret', 'app_id'])->every(fn ($key) => filled(config('broadcasting.connections.pusher.'.$key)));
        $checks = [
            'Production/staging environment' => in_array(config('app.env'), ['production', 'staging'], true),
            'Debug disabled' => config('app.debug') === false,
            'Application encryption key present' => filled(config('app.key')),
            'HTTPS API and frontend origins' => $https(config('app.url')) && $https(config('app.frontend_url')),
            'Private session storage' => in_array(config('session.driver'), ['database', 'redis'], true),
            'Secure HttpOnly host-only session cookie' => config('session.secure') === true && config('session.http_only') === true && ! config('session.domain') && config('session.same_site') === 'lax',
            'Persistent cache and asynchronous queue' => in_array(config('cache.default'), ['database', 'redis'], true) && in_array(config('queue.default'), ['database', 'redis'], true),
            'Resend configured' => config('mail.default') === 'resend' && filled(config('services.resend.key')) && filter_var(config('mail.from.address'), FILTER_VALIDATE_EMAIL),
            'Order updates: polling-only or configured Pusher' => $pollingOnly || $pusherReady,
            'VNPAY integration enabled and configured' => config('payments.vnpay.deferred') === false && filled(config('payments.vnpay.merchant')) && filled(config('payments.vnpay.secret')) && $https(config('payments.vnpay.return_url')),
        ];
        try {
            $checks['PostgreSQL17+ UTC with search extensions'] = DB::getDriverName() === 'pgsql'
                && (int) DB::selectOne("SELECT current_setting('server_version_num') AS version")->version >= 170000
                && DB::selectOne("SELECT current_setting('TimeZone') AS zone")->zone === 'UTC'
                && (int) DB::selectOne("SELECT COUNT(*) AS total FROM pg_extension WHERE extname IN ('unaccent','pg_trgm')")->total === 2;
        } catch (\Throwable) {
            $checks['PostgreSQL17+ UTC with search extensions'] = false;
        }
        foreach ($checks as $label => $passed) {
            $this->line(($passed ? 'PASS ' : 'FAIL ').$label);
        }
        $this->line('Configuration checks only. Live TLS/firewall, mail, order updates, merchant acceptance and load still require verification.');

        return in_array(false, array_map('boolval', $checks), true) ? self::FAILURE : self::SUCCESS;
    }
}
