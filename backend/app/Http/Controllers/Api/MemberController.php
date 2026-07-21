<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MemberController extends Controller
{
    public $successStatus = 200;

    // $id trong URL BỊ BỎ QUA — chỉ sửa được chính tài khoản đang đăng nhập (chống IDOR).
    public function updateProfile(Request $request, $id = null)
    {
        $user = auth()->user();

        // whitelist field — KHÔNG cho set role/status/level qua đây (chống leo quyền)
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:500',
            'password' => 'nullable|string|min:6',
            'avatar'   => 'nullable|string',
        ]);

        // avatar mới (base64) → resize + lưu; không có thì giữ nguyên
        if (!empty($data['avatar']) && strpos($data['avatar'], ';')) {
            $file = $data['avatar'];

            if ($user->avatar && file_exists(public_path('uploads/avatars/' . $user->avatar))) {
                unlink(public_path('uploads/avatars/' . $user->avatar));
            }

            $name = time() . '.' . explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];
            $imageData = base64_decode(explode(',', $file)[1]);

            $path = public_path('uploads/avatars');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            (new ImageManager(new Driver()))->read($imageData)->resize(200, 200)->save($path . '/' . $name);
            $data['avatar'] = $name;
        } else {
            unset($data['avatar']); // giữ avatar cũ
        }

        // password: có thì hash, không thì giữ nguyên
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // token trả lại để giữ tương thích frontend (UpdateUser.js cần res.data.token).
        // ponytail: nên bỏ khi dọn frontend — tạo token mỗi lần sửa profile là thừa.
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'response' => 'success',
            'token' => $token,
            'Auth' => $user->only(['id', 'name', 'email', 'phone', 'address', 'avatar']),
        ], $this->successStatus);
    }
}