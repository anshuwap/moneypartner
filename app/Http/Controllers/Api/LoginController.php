<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Validation\LoginRequest as ValidationLoginRequest;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

// use Tymon\JWTAuth\JWTAuth;

class LoginController extends Controller
{

    public function store(ValidationLoginRequest $request)

    {
        try {

            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return $credentials;
            return response()->json([
                'status' => false,
                'message' => 'Could not create token.',
            ], 500);
        }

        //Token created, return with success response and jwt token
        return $this->createNewToken($token);
    }

    protected function createNewToken($token)
    {
        if(!isset(MoneyPartnerOption()->payout_api) || MoneyPartnerOption()->payout_api !=1)
            return response()->json([
                'status' => false,
                'message' => 'You have not Provide this Service.',
            ], 500);

        $user['name'] = auth()->user()->full_name;
        $user['email'] = auth()->user()->email;

        return response()->json([
            'status' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user
        ]);
    }

}
