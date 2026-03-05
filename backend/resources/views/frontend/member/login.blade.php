@extends('frontend.layouts.app')

@section('title', 'Login')

@section('content')
<section id="form">
    <div class="container">
        <div class="row">

            <div class="col-sm-4 col-sm-offset-4">
                <div class="login-form">
                    <h2>Login</h2>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('member.login.post') }}">
                        @csrf

                        <input type="email"
                               name="email"
                               placeholder="Email Address"
                               value="{{ old('email') }}"/>

                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        <input type="password"
                               name="password"
                               placeholder="Password"/>

                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        <button type="submit" class="btn btn-default">
                            Login
                        </button>
                        <span>
                            <input type="checkbox" name="remember_me" class="checkbox">
                            Keep me signed in
                        </span>
                        <div style="margin-top:10px; text-align:right;">
                            <a href="{{ route('password.request') }}">
                                Forgot your password?
                            </a>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection
