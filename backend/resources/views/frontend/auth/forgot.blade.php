
@extends('frontend.layouts.app')

@section('title', 'Reset pass')

@section('content')


<div class="container">
    <div class="row">

        {{-- LEFT MENU --}}
        <div class="col-sm-3">
            @include('frontend.layouts.menuleft')
        </div>

        {{-- FORM --}}
        <div class="col-sm-4 col-sm-offset-1">
            <div class="login-form">
                <h2>Forgot Password</h2>

                @if(session('status'))
                    <p style="color:green">{{ session('status') }}</p>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <input type="email"
                        name="email"
                        placeholder="Email Address"
                        required>

                    <button type="submit" class="btn btn-default">
                        Send Reset Link
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>


@endsection
