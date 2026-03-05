@extends('frontend.layouts.app')

@section('title', 'Register')

@section('content')
<section id="form">
    <div class="container">
        <div class="row">

            <div class="col-sm-4 col-sm-offset-4">
                <div class="signup-form">
                    <h2>Signup</h2>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST"
                          action="{{ route('member.register.post') }}"
                          enctype="multipart/form-data">
                        @csrf

                        <input type="text"
                               name="name"
                               placeholder="Name"
                               value="{{ old('name') }}"/>

                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

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

                        <input type="password"
                               name="password_confirmation"
                               placeholder="Confirm Password"/>

                        {{-- AVATAR --}}
                        <input type="file"
                               name="avatar"
                               accept="image/*"/>

                        @error('avatar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        <button type="submit" class="btn btn-default">
                            Signup
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection
