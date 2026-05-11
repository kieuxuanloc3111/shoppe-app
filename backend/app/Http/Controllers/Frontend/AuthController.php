<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use App\Models\Country;
use App\Http\Requests\Member\RegisterRequest;
use App\Http\Requests\Member\LoginRequest;
use App\Http\Requests\Member\UpdateProfileRequest;

class AuthController extends Controller
{
    public function registerForm()
    {
        return view('frontend.member.register');
    }

    public function register(RegisterRequest $request)
    {
        $avatarPath = null;

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $fileName);

            $avatarPath = 'uploads/avatars/' . $fileName;
        }

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'avatar'   => $avatarPath,
            'level'    => 0,
        ]);

        return redirect()->route('member.login')
            ->with('success', 'Register success! Please login.');
    }


    public function loginForm()
    {
        return view('frontend.member.login');
    }
    public function login(LoginRequest $request)
    {
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
            'level'    => 0, 
        ];

        if (Auth::attempt($credentials, $request->filled('remember_me'))) {
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng',
        ]);
    }



    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/member/login');
    }
    public function profileForm()
    {
        $user = Auth::user();
        $countries = Country::all();

        return view('frontend.member.profile', compact('user', 'countries'));
    }
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $fileName);

            $user->avatar = 'uploads/avatars/'.$fileName;
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->id_country = $request->id_country;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Cập nhật thành công');
    }


}
