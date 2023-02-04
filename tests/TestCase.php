<?php

namespace Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function beforeRefreshingDatabase()
    {
        Config::set('database.default', 'sqlite_testing');
    }
}
