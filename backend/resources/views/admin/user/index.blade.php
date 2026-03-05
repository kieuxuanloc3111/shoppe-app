@if(session('error'))
    <script>
        alert("{{ session('error') }}");
    </script>
@endif

@if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif
@extends('admin.layouts.app')

@section('title', 'User List')

@section('content')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">User List</h4>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-striped table-bordered no-wrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Level</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->level == 1)
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-success">Member</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.user.edit', $user->id) }}"
                                   class="btn btn-sm btn-warning text-white">
                                   Edit
                                </a>

                                <a href="{{ route('admin.user.delete', $user->id) }}"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this user?')">
                                   Delete
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection