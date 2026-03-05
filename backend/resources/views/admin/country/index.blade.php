@extends('admin.layouts.app')

@section('title', 'Country')

@section('content')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Country</h4>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Country List</h4>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th width="180">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($countries as $country)
                                <tr>
                                    <td>{{ $country->id }}</td>
                                    <td>{{ $country->name }}</td>
                                    <td>
                                        <a href="{{ route('admin.country.edit', $country->id) }}"
                                        class="btn btn-warning btn-sm text-white">
                                            Edit
                                        </a>

                                        <a href="javascript:void(0)"
                                        onclick="deleteCountry({{ $country->id }})"
                                        class="btn btn-danger btn-sm">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-body">
                    <a href="{{ route('admin.country.create') }}"
                       class="btn btn-success text-white">
                        Add Country
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>


@endsection

<script>
    function deleteCountry(id) {
        if (!confirm('xóa???')) return;

        fetch(`/admin/country/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(() => {
            window.location.reload();
        });
    }
</script>
