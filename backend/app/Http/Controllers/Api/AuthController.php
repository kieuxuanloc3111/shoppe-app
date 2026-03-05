<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\LoginRequest;
use App\Http\Requests\Member\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public $successStatus = 200;
    public function register(RegisterRequest $request)
    {
        $avatarPath = null;

        if ($request->hasFile('avatar')) {

            $file = $request->file('avatar');

            $fileName = time().'_'.$file->getClientOriginalName();

            $file->move(public_path('uploads/avatars'), $fileName);

            $avatarPath = 'uploads/avatars/' . $fileName;
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'avatar'   => $avatarPath,
            'level'    => 0,
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'success',
            'token'   => $token,
            'Auth'    => $user
        ], $this->successStatus);
    }
    public function login(LoginRequest $request)
    {
        $login = [
            'email'    => $request->email,
            'password' => $request->password,
            'level'    => 0,
        ];

        $remember = $request->filled('remember_me');

        if (Auth::attempt($login, $remember)) {

            $user = Auth::user();

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'success' => 'success',
                'token'   => $token,
                'Auth'    => $user
            ], $this->successStatus);

        } else {

            return response()->json([
                'response' => 'error',
                'errors'   => ['errors' => 'invalid email or password'],
            ], $this->successStatus);
        }
    }
}