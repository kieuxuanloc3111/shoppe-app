@extends('frontend.layouts.app')

@section('title', 'Reset Password')

@section('content')

<div class="container">
    <div class="row">

        <div class="col-sm-4 col-sm-offset-1">
            <div class="login-form">
                <h2>Reset Password</h2>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <input type="email"
                        name="email"
                        placeholder="Email"
                        required>

                    <input type="password"
                        name="password"
                        placeholder="New Password"
                        required>

                    <input type="password"
                        name="password_confirmation"
                        placeholder="Confirm Password"
                        required>

                    <button type="submit" class="btn btn-default">
                        Reset Password
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection