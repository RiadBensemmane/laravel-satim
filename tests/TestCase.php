<?php

namespace LaravelSatim\Tests;

use LaravelSatim\SatimServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('config:clear');
    }

    protected function getPackageProviders($app)
    {
        return [SatimServiceProvider::class,];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('satim.username', 'test_username');
        $app['config']->set('satim.password', 'test_password');
        $app['config']->set('satim.terminal', 'test_terminal');
        $app['config']->set('satim.api_url', 'https://test.satim.dz/payment/rest');
        $app['config']->set('satim.language', 'en');
        $app['config']->set('satim.currency', 'DZD');
    }
}
