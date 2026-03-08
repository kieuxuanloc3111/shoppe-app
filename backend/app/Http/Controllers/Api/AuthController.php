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
            'level' => 0,
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