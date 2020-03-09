<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterUser;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['logout']]);
    }

    public function login(Request $request)
    {
        if (!$token = auth()->attempt($request->only('email', 'password'))) {
            return response([
                'error' => __('Wrong email or password.')
            ], 401);
        }
        return response()->json([
            'user' => $this->publicUserData(auth()->user()),
            'token' => $token]);
    }

    public function register(RegisterUser $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        if ($user) {
            $token = auth()->attempt($request->only('email', 'password'));
            return response()->json([
                'user' => $this->publicUserData(auth()->user()),
                'token' => $token
            ],201);
        }
    }

    public function attempt(Request $request)
    {
        try {

            if (! $user = \JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong please check your token or login'], 401);
        }
    
        // the token is valid and we have found the user via the sub claim
        return response()->json(['user' => $this->publicUserData($user)]);

    }
    public function logout(Request $request)
    {
        auth()->logout();

        return response(200);
    }

    private function publicUserData(User $user)
    {
        return $user->only(['id', 'name', 'email']);
    }
}
