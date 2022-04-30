<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|max:255|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response([
            'message' => 'Register success',
            'user'    => $user,
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
    	$request->validate([
    		'email'    => 'required|email',
    		'password' => 'required|string',
    	]);

    	if(! Auth::attempt($request->only('email', 'password'))) {
    		return response([
    			'message' => 'Invalid email or password',
    		], Response::HTTP_UNAUTHORIZED);
    	}

    	$user  = auth()->user();
    	$token = $user->getToken();

    	return response([
    		'message' => 'Logged in',
    		'user'    => $user,
    		'token'   => $token,
    	]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response([
            'message' => 'Logged out',
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string|max:255',
            'password'     => 'required|string|max:255|confirmed',
        ]);

        $user = auth()->user();

        if(! \Hash::check($request->old_password, $user->password)) {
            return response([
                'message' => 'Invalid old password',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return response([
            'message' => 'Password has been changed',
        ]);
    }
}
