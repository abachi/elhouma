<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['logout']]);
    }

    public function login(Request $request)
    {
        if (!$token = auth()->attempt($request->only('email', 'password'))) {
            return response(null, 401);
        }

        return response()->json(['token' => $token]);
    }

    public function register(Request $request)
    {
        $user = User::create($request->all());
        if ($user) {
            $token = auth()->attempt($request->only('email', 'password'));
            return response()->json([
                'token' => $token,
                'email' => $user->email,
            ], 201);
        }
    }

    public function logout(Request $request)
    {
        auth()->logout();

        return response(200);
    }
}
