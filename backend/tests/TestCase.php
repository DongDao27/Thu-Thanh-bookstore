<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        if ($app['config']->get('database.default') !== 'pgsql'
            || $app['config']->get('database.connections.pgsql.database') !== 'thu_thanh_test') {
            throw new \RuntimeException('Tests require isolated PostgreSQL database thu_thanh_test.');
        }

        return $app;
    }
}
