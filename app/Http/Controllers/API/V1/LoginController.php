<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Resources\V1\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request['email'])->first();

        if (!$user) {
            return $this->respondUnauthorized('User does not exist');
        }

        if (!Hash::check($request['password'], $user->password)) {
            return $this->respondUnauthorized('Password mismatch');
        }

        $token = $user->createToken('Password Grant Token');
        $user->token = $token->plainTextToken;

        return $this->respond(new UserResource($user));
    }
}
