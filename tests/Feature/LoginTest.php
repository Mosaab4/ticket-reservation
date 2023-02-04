<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    public function test_login()
    {
        $request = $this->json('post', 'api/v1/login', [
            'email'    => $this->user->email,
            'password' => '123456',
        ]);

        $request->assertOk();

        $request->assertJsonStructure([
            'data' => [
                'id',
                'email',
                'token',
            ],
        ]);
    }

    public function test_login_failed()
    {
        $request = $this->json('post', 'api/v1/login', [
            'email'    => $this->user->email,
            'password' => '1232123',
        ]);

        $request->assertUnauthorized();
    }
}
