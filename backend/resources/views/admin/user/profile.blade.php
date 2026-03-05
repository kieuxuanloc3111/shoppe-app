@extends('admin.layouts.app')

@section('title', 'Profile')

@section('content')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Profile</h4>
        </div>
        <div class="col-7 align-self-center">
            <div class="d-flex align-items-center justify-content-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">

        <!-- LEFT PROFILE -->
        <div class="col-lg-4 col-xlg-3 col-md-5">
            <div class="card">
                <div class="card-body">
                    <center class="m-t-30">

                        <img
                            src="{{ $user->avatar ? asset($user->avatar) : asset('assets/images/avatar-empty.png') }}"
                            class="rounded-circle"
                            width="150"
                            alt="Avatar"
                        />

                        <h4 class="card-title m-t-10">
                            {{ $user->name }}
                        </h4>

                        <h6 class="card-subtitle">
                            {{ $user->email }}
                        </h6>

                    </center>
                </div>

                <div><hr></div>

                <div class="card-body">

                    <small class="text-muted">Email address</small>
                    <h6>{{ $user->email }}</h6>

                    <small class="text-muted p-t-30 db">Phone</small>
                    <h6>{{ $user->phone ?? '—' }}</h6>

                    <small class="text-muted p-t-30 db">Address</small>
                    <h6>{{ $user->address ?? '—' }}</h6>

                    <div class="map-box">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d470029.1604841957!2d72.29955005258641!3d23.019996818380896!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e848aba5bd449%3A0x4fcedd11614f6516!2sAhmedabad%2C+Gujarat!5e0!3m2!1sen!2sin!4v1493204785508"
                            width="100%"
                            height="150"
                            frameborder="0"
                            style="border:0"
                            allowfullscreen>
                        </iframe>
                    </div>

                    <small class="text-muted p-t-30 db">Social Profile</small>
                    <br/>
                    <button class="btn btn-circle btn-secondary">
                        <i class="mdi mdi-facebook"></i>
                    </button>
                    <button class="btn btn-circle btn-secondary">
                        <i class="mdi mdi-twitter"></i>
                    </button>
                    <button class="btn btn-circle btn-secondary">
                        <i class="mdi mdi-youtube-play"></i>
                    </button>

                </div>
            </div>
        </div>

        <!-- RIGHT FORM -->
        <div class="col-lg-8 col-xlg-9 col-md-7">
            <div class="card">
                <div class="card-body">

                    <form
                        method="POST"
                        action="{{ route('admin.profile.update') }}"
                        enctype="multipart/form-data"
                        class="form-horizontal form-material"
                    >
                        @csrf

                        <div class="form-group">
                            <label class="col-md-12">Username</label>
                            <div class="col-md-12">
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    class="form-control form-control-line"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12">Email</label>
                            <div class="col-md-12">
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email', $user->email) }}"
                                    class="form-control form-control-line"
                                    required
                                >
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-12">New Password</label>
                            <div class="col-md-12">
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control form-control-line"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12">Phone</label>
                            <div class="col-md-12">
                                <input
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone', $user->phone) }}"
                                    class="form-control form-control-line"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12">Address</label>
                            <div class="col-md-12">
                                <textarea
                                    name="address"
                                    rows="3"
                                    class="form-control form-control-line"
                                >{{ old('address', $user->address) }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-12">Select Country</label>
                            <div class="col-sm-12">
                                <select name="id_country" class="form-control form-control-line">
                                    <option value="">-- Select country --</option>
                                    @foreach ($countries as $country)
                                        <option
                                            value="{{ $country->id }}"
                                            {{ old('id_country', $user->id_country) == $country->id ? 'selected' : '' }}
                                        >
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12">Avatar</label>
                            <div class="col-md-12">
                                <input
                                    type="file"
                                    name="avatar"
                                    class="form-control form-control-line"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <button class="btn btn-success">
                                    Update Profile
                                </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection
