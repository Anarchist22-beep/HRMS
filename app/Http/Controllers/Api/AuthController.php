<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class AuthController extends Controller
{
    //

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);


        $token = $user->createToken('UserToken')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'User Registered Successfully'
        ], 200);
    }



    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $credentials['status'] = 1; // only allow active users

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('UserToken')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'message' => 'User Logged in Successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'unauthorized',
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'User logged out successfully'
        ]);
    }
    public function changeName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user(); // Passport user

        $user->name = $request->name;
        $user->save();

        return response()->json([
            'message' => 'Name updated successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 200);
    }

    //change password
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // expects new_password_confirmation
        ]);

        $user = Auth::user();

        // Check if the old password is correct
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'The provided old password is incorrect.'
            ], 400);
        }

        // Update the user's password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully.'
        ], 200);
    }
}
