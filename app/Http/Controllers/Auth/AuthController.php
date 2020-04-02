<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\SocialUser;
use Illuminate\Http\Request;
use App\Http\Requests\SocialLoginRequest;
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

    public function socialLogin(SocialLoginRequest $request)
    {
        $data = $request->validated();
        $fb = new \Facebook\Facebook([
            'app_id' => '545083476140376',
            'app_secret' => 'e16e71b0237d0b03838c14d70660a45d',
            'default_graph_version' => 'v2.10',
            'default_access_token' => $data['provider_token'], // optional
          ]);
        try {
            $response = $fb->get('/me');
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            return response()->json([
                'status' => $e->getHttpStatusCode(),
                'error' => $e->getMessage(),
            ], $e->getHttpStatusCode());
        }
        $user = User::where(['email'=> $data['email']])->get()->first();
        
        if(!$user){
            $user = User::create($data);
        }

        $data['token'] = \JWTAuth::fromUser($user);
        return response()->json([
            'status' => 201,
            'data' => $data
        ], 201);
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
