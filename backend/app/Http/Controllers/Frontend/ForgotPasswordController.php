<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('frontend.auth.forgot');
    }

    // Gửi mail reset
    public function sendLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return back()->with('status', __($status));
    }

    // Form nhập password mới
    public function resetForm($token)
    {
        return view('frontend.auth.reset', [
            'token' => $token
        ]);
    }

    // Lưu password mới
    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed'
        ]);

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user, $password) {

                $user->password = Hash::make($password);
                $user->save();

            }
        );

        return redirect('/member/login')
            ->with('status', __($status));
    }
}
