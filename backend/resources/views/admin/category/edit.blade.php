@extends('admin.layouts.app')

@section('title', 'Edit Category')

@section('content')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Edit Category</h4>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">

                    <form method="POST"
                          action="{{ route('admin.category.update', $category->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="col-md-12">Name</label>
                            <div class="col-md-12">
                                <input type="text"
                                       name="name"
                                       value="{{ old('name', $category->name) }}"
                                       class="form-control form-control-line"
                                       required>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-success text-white">Update</button>
                            <a href="{{ route('admin.category.index') }}"
                               class="btn btn-secondary">Back</a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
