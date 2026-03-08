<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Intervention\Image\Facades\Image ;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MemberController extends Controller
{
    public $successStatus = 200;

    public function updateProfile(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->all();

        // check email tồn tại
        $getEmail = User::where('email', $data['email'])
            ->where('id', '<>', $id)
            ->first();

        if ($getEmail) {
            return response()->json([
                'errors' => ['errors' => 'Email da ton tai'],
                'email' => $getEmail->email
            ], JsonResponse::HTTP_OK);
        }

        $file = $request->avatar;

        // nếu có avatar mới
        if (!empty($file) && strpos($file, ';')) {

            // xóa avatar cũ
            if ($user->avatar && file_exists(public_path('uploads/avatars/'.$user->avatar))) {
                unlink(public_path('uploads/avatars/'.$user->avatar));
            }

            // lấy extension
            $name = time().'.'.explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];

            // decode base64
            $imageData = base64_decode(explode(',', $file)[1]);

            // tạo folder nếu chưa có
            $path = public_path('uploads/avatars');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // dùng ImageManager
            $manager = new ImageManager(new Driver());
            $img = $manager->read($imageData);

            // resize avatar
            $img->resize(200, 200);

            $img->save($path.'/'.$name);

            $data['avatar'] = $name;

        } else {
            // nếu không có avatar mới → giữ avatar cũ
            $data['avatar'] = $user->avatar;
        }

        // password
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            $data['password'] = $user->password;
        }

        $user->update($data);

        $data['id'] = $id;

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'response' => 'success',
            'token' => $token,
            'Auth' => $data
        ], $this->successStatus);
    }
}