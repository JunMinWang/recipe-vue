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

        return resopnse()->json([
            'registered' => true
        ]);
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

}
