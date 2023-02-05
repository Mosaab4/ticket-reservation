<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = create(User::class);
    }

    protected function beforeRefreshingDatabase()
    {
        Config::set('database.default', 'sqlite_testing');
        Config::set('app.env', 'testing');
    }
}
