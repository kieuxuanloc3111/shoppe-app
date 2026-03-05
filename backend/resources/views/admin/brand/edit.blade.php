@extends('admin.layouts.app')

@section('title', 'Edit Brand')

@section('content')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Edit Brand</h4>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">

                    <form method="POST"
                          action="{{ route('admin.brand.update', $brand->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="col-md-12">Title</label>
                            <div class="col-md-12">
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $brand->name) }}"
                                    class="form-control form-control-line"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <button class="btn btn-success text-white">
                                    Update
                                </button>
                                <a href="{{ route('admin.brand.index') }}"
                                   class="btn btn-secondary">
                                    Back
                                </a>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection
