<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\LoginRequest;
use App\Http\Requests\Api\AuthRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
// use Intervention\Image\Facades\Image;
class AuthController extends Controller
{
    public $successStatus = 200;

    public function register(AuthRequest $request)
    {
        $data = $request->all();

        $avatarName = null;

        if (!empty($data['avatar']) && strpos($data['avatar'], 'base64,')) {

            // tách header và data
            [$meta, $content] = explode(',', $data['avatar']);

            // lấy extension
            preg_match('/data:image\/(\w+);base64/', $meta, $matches);
            $extension = $matches[1] ?? 'png';

            $avatarName = time().'.'.$extension;

            // decode base64
            $imageData = base64_decode($content);

            // đảm bảo folder tồn tại
            $path = public_path('uploads/avatars');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // lưu file
            file_put_contents($path.'/'.$avatarName, $imageData);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'role' => 'user',
            'avatar' => $avatarName
        ]);

        return response()->json([
            'message' => 'success',
            'Auth' => $user
        ]);
    }
    public function login(LoginRequest $request)
    {
        $login = [
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'user',
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

    // client-only reset (React). Admin (level=1) has no self-service reset.
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // only members get a link; admin emails are ignored silently
        $user = User::where('email', $request->email)->first();
        if ($user && $user->role === 'user') {
            Password::sendResetLink($request->only('email'));
        }

        // same response either way — no user/level enumeration
        return response()->json([
            'response' => 'success',
            'message'  => 'If that email is registered, a reset link has been sent.',
        ], $this->successStatus);
    }

    // ported from Frontend\ForgotPasswordController@updatePassword
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return response()->json([
            'response' => $status === Password::PASSWORD_RESET ? 'success' : 'error',
            'message'  => __($status),
        ], $this->successStatus);
    }
}