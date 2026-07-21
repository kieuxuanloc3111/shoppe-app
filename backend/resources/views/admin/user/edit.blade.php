
@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <form method="POST"
                  action="{{ route('admin.user.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Name</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Password (optional)</label>
                    <input type="password"
                           name="password"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <select name="role" class="form-control">
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Member</option>
                    </select>
                </div>

                <br>

                <button class="btn btn-success">Update</button>
                <a href="{{ route('admin.user.index') }}"
                   class="btn btn-secondary">Back</a>

            </form>

        </div>
    </div>
</div>

@endsection