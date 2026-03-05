<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\HttpFoundation\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Models\Country;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.user.index', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    public function update_member(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'level' => 'required'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'level' => $request->level,
            'phone' => $request->phone,
            'address' => $request->address
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.user.index');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id == auth()->id()) {
            return back()->with('error', 'Cannot delete yourself');
        }

        if ($user->level == 1) {
            return back()->with('error', 'Cannot delete another admin');
        }

        $user->delete();

        return redirect()->route('admin.user.index')
            ->with('success', 'User deleted');
    }
    
    public function profile()
    {
        $user = Auth::user();
        $countries = Country::all();

        return view('admin.user.profile', compact('user', 'countries'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $data = [
            'name'       => $request->name,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'id_country' => $request->id_country,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $fileName = time() . '_' . $request->avatar->getClientOriginalName();
            $request->avatar->move(public_path('uploads/avatars'), $fileName);

            $data['avatar'] = 'uploads/avatars/' . $fileName;
        }

        $user->update($data);

        return redirect()->route('admin.profile');
    }
}
