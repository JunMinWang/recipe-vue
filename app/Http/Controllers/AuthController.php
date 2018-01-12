<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Hash;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api')->only('logout');
    }

    /**
     *  註冊使用者
     */ 
    public function register(Request $request) {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|between:6,25|confirmed'
        ]);
    
        $user = new User($request->all());
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'registered' => true
        ]);
    }

    /**
     *  驗證臉書使用者
     */ 
    public function validateSocialite($service, Request $request) {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'api_token' => 'required|alpha_num'
        ]);

        $uCheck = User::where('email', $request->email)->first();

        if($uCheck) {
            $uCheck->api_token = $request->api_token;
            $uCheck->save();

            return response()->json([
                'status' => 'validated',
                'user_id' => $uCheck->id,
                'api_token' => $uCheck->api_token
            ]);
        } else {
            $user = new User($request->all());
            $user->password = bcrypt(str_random(20));
            $user->save();

            return response()->json([
                'status' => 'validated',
                'user_id' => $user->id,
                'api_token' => $user->api_token
            ]);
        }
    
        
    }
    
    /**
     *  使用者登入
     */ 
    public function login(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|between:6,25'
        ]);

        $user = User::where('email', $request->email)->first();

        // 如果登入成功
        if($user && Hash::check($request->password, $user->password)) {
            $user->api_token = str_random(60);
            $user->save();

            return response()->json([
                'authenticated' => true,
                'api_token' => $user->api_token,
                'user_id' => $user->id
            ]);
        }

        return response()->json([
            'email' => ['無此帳號或密碼錯誤!']
        ], 422);
    }

    /**
     *  使用者登出
     */ 
    public function logout(Request $request) {
        $user = $request->user();
        $user->api_token = null;
        $user->save();

        return response()->json([
            'logged_out' => true
        ]);
    }
}
