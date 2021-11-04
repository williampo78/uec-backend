<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
//use Tymon\JWTAuth\JWTAuth ;
use JWTAuth;
use App\Models\Members;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('laravel:jwt');
    }

    public function login()
    {

        $credentials = request(['mobile', 'password']);
        //dd(JWTAuth) ;
        //dump(JWTAuth::class);
        //exit;
        /*
        return response()->json([
            'success' => true,
            'token' => $credentials,
        ]);
        */
        //Request is validated
        //Crean token
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return $credentials;
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], 500);
        }

        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);


    }

    public function me()
    {
        //
    }

    public function logout()
    {
        //
    }
}
