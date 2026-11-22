<?php

namespace Deployer;

require 'recipe/laravel.php';

// Run with Deployer 7 from the repository root; see README.md and docs/SECURITY-PERFORMANCE.md.
set('application', 'thu-thanh');
set('repository', getenv('DEPLOY_REPOSITORY') ?: 'git@github.com:DongDao27/Thu-Thanh-bookstore.git');
set('branch', 'main');
set('keep_releases', 3);
set('default_timeout', 900);
set('bin/php', '/usr/bin/php8.4');
set('shared_files', ['backend/.env']);
set('shared_dirs', ['backend/storage']);
set('writable_dirs', ['backend/bootstrap/cache', 'backend/storage']);
set('writable_mode', 'chmod');
set('writable_chmod_mode', '0770');
set('writable_recursive', true);
set('log_files', 'backend/storage/logs/*.log');
set('laravel_version', function () {
    $output = run('{{bin/php}} {{release_or_current_path}}/backend/artisan --version');
    preg_match('/\d+\.\d+\.\d+/', $output, $matches);

    return $matches[0] ?? throw new \RuntimeException('Cannot detect Laravel version.');
});

host('staging')
    ->setHostname(getenv('DEPLOY_HOST') ?: 'staging.example.com')
    ->setRemoteUser('deploy')
    ->set('deploy_path', '/srv/thu-thanh');

desc('Checks operator configuration before preparing a release');
task('app:preflight', function () {
    if (get('hostname') === 'staging.example.com') {
        throw new \RuntimeException('Set DEPLOY_HOST to the actual SSH hostname.');
    }
    if (! test('[ -s {{deploy_path}}/shared/backend/.env ]')) {
        throw new \RuntimeException('Create the private shared/backend/.env first.');
    }
    run('{{bin/php}} -r '.escapeshellarg('exit(PHP_MAJOR_VERSION === 8 && PHP_MINOR_VERSION === 4 ? 0 : 1);'));
    run('composer --version');
    run('{{bin/php}} -r '.escapeshellarg('exit(extension_loaded("pdo_pgsql") ? 0 : 1);'));
});

// The upstream recipe assumes artisan/composer.json live at the repository root.
task('deploy:vendors', function () {
    within('{{release_path}}/backend', function () {
        run('{{bin/composer}} {{composer_action}} {{composer_options}}');
    });
});

foreach ([
    'storage:link', 'config:cache', 'route:cache',
    'event:cache', 'migrate --force', 'queue:restart',
    'app:deployment-check',
] as $command) {
    $name = explode(' ', $command)[0];
    task('artisan:'.$name, function () use ($command) {
        within('{{release_path}}/backend', function () use ($command) {
            run('{{bin/php}} artisan '.$command);
        });
    });
}

desc('Restarts processes using the published current release');
task('app:restart', function () {
    run('sudo /usr/bin/systemctl reload php8.4-fpm');
    within('{{current_path}}/backend', function () {
        run('{{bin/php}} artisan queue:restart');
    });
});

desc('Publishes Laravel API on VPS; frontend is built and hosted by Vercel');
task('deploy', [
    'app:preflight', 'deploy:prepare', 'deploy:vendors',
    'artisan:storage:link', 'artisan:config:cache', 'artisan:route:cache',
    // No tracked resources/views directory remains in this API after scaffold cleanup.
    'artisan:event:cache', 'artisan:migrate', 'artisan:app:deployment-check',
    'deploy:symlink', 'app:restart', 'deploy:unlock', 'deploy:cleanup', 'deploy:success',
]);

after('deploy:failed', 'deploy:unlock');
after('rollback', 'app:restart');
