@extends('admin.layouts.app')

@section('title', 'Purchase History')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <h4 class="mb-3">Purchase History</h4>

            <div class="table-responsive">
                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Price</th>
                            <th>Time</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($histories as $h)
                        <tr>
                            <td>{{ $h->id }}</td>
                            <td>{{ $h->id_user }}</td>
                            <td>{{ $h->name }}</td>
                            <td>{{ $h->email }}</td>
                            <td>{{ $h->phone }}</td>
                            <td>${{ number_format($h->price) }}</td>
                            <td>{{ $h->created_at }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                No purchase yet
                            </td>
                        </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

@endsection