<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\RegisterationMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Auth\AuthenticationException;

class AuthController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(RegisterRequest $request){
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        Mail::to($user->email)->send(new RegisterationMail($user));
        return response()->noContent();
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request) {
        if(!Auth::attempt($request->only('email', 'password'))){
            throw new AuthenticationException("Email or password is not valid");
        }
        $token =$request->user()->createToken('user-token');

        return response([
            'message' => 'successfully logged in',
            'token' => $token->plainTextToken
        ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return [
            'message'=>'Successfully Logged out'
        ];
    }
}
