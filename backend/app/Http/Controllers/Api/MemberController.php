<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Intervention\Image\Facades\Image ;

class MemberController extends Controller
{
    //
    public $successStatus = 200;
    public function updateProfile(Request $request, $id){
        $user = User::findOrFail($id);

        $data = $request->all();

        $getEmail = User::All()
            ->where('email', $data['email'])
            ->where('id', '<>', $id)
            ->first();
        
        if($getEmail) {
            $getEmail->toArray();
            return response()->json([
                'errors' => ['errors' => 'Email da ton tai'],
                'email' => $getEmail['email']
            ], JsonResponse::HTTP_OK);
        }
        
        $file = $request->avatar;
        if($file) {
           $image = $file;
           if(strpos($image, ';')){
                $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                $data['avatar'] = $name;
           }
        } else {
            $data['avatar'] = $user->avatar;
        }
        
        if ($data['password']) {
            $data['password'] = bcrypt($data['password']);
        }else{
            $data['password'] = $user->password;
        }


        if ($getUser = $user->update($data)) {
            if(strpos($file, ';')){
                Image::make($file)->save(public_path('upload/user/avatar/').$data['avatar']);
            }
            $userAuth = Auth::user();
            
            $data['id'] = $id;
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                    'response' => 'success',
                    'token' => $token,
                    'Auth' => $data
                ],
                $this->successStatus
            );
        } else {
            return response()->json([
                'errors' => 'error update',
            ],
            $this->successStatus); 
            // return response()->json(['errors' => 'error sever'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}
