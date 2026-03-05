@extends('frontend.layouts.app')

@section('title', 'Update Profile')

@section('content')
<section>
    <div class="container">
        <div class="row">

            <!-- left -->
            <div class="col-sm-3">
                <div class="left-sidebar">
                    <h2>Account</h2>

                    <div class="panel-group category-products">

                        <!-- account -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a href="{{ route('member.profile') }}">
                                        Account
                                    </a>
                                </h4>
                            </div>
                        </div>

                        <!-- my product  -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a href="{{ route('member.product.my') }}">
                                        My product
                                    </a>
                                </h4>
                            </div>
                        </div>

                        <!-- add product  -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a href="{{ route('member.product.add') }}">
                                        Add product
                                    </a>
                                </h4>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- right -->
            <div class="col-sm-9">
                <div class="blog-post-area">
                    <h2 class="title text-center">Update user</h2>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="signup-form">
                        <h2>User update</h2>

                        <form method="POST"
                              action="{{ route('member.profile.update') }}"
                              enctype="multipart/form-data">
                            @csrf

                            <input type="text" name="name"
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="Name"/>

                            <input type="email"
                                   value="{{ $user->email }}"
                                   readonly
                                   style="background:#eee;cursor:not-allowed;"/>

                            <input type="password"
                                   name="password"
                                   placeholder="New Password"/>

                            <input type="password"
                                   name="password_confirmation"
                                   placeholder="Confirm New Password"/>

                            <input type="text"
                                   name="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="Phone"/>

                            <input type="text"
                                   name="address"
                                   value="{{ old('address', $user->address) }}"
                                   placeholder="Address"/>

                            <select name="id_country" class="form-control">
                                <option value="">-- Select country --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}"
                                        {{ $user->id_country == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>

                            <br>

                            <img id="avatar-preview"
                                src="{{ $user->avatar ? asset($user->avatar) : '' }}"
                                width="80"
                                style="margin-bottom:10px; display:block;">

                            <input type="file"
                                name="avatar"
                                id="avatar-input"
                                accept="image/*">

                            <br><br>

                            <button type="submit" class="btn btn-default">
                                Update
                            </button>

                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
@section('scripts')
<script>
document.getElementById('avatar-input').addEventListener('change', function (e) {

    const file = e.target.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('Vui lòng chọn file ảnh');
        return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {
        document.getElementById('avatar-preview').src = e.target.result;
    };

    reader.readAsDataURL(file);
});
</script>
@endsection
