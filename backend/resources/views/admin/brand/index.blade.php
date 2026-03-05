@extends('admin.layouts.app')

@section('title', 'Brand')

@section('content')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Brand</h4>
        </div>
        <div class="col-7 align-self-center text-end">
            <a href="{{ route('admin.brand.create') }}"
               class="btn btn-success text-white">
                Add Brand
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered no-wrap">
                            <thead>
                                <tr>
                                    <th width="80">ID</th>
                                    <th>Name</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $brand)
                                    <tr>
                                        <td>{{ $brand->id }}</td>
                                        <td>{{ $brand->name }}</td>
                                        <td>
                                            <a href="{{ route('admin.brand.edit', $brand->id) }}"
                                               class="btn btn-sm btn-warning text-white">
                                                Edit
                                            </a>

                                            <a href="{{ route('admin.brand.delete', $brand->id) }}"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Delete this brand?')">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            No brand found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
