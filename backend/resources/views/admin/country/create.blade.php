@extends('admin.layouts.app')

@section('title', 'Add Country')

@section('content')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Add Country</h4>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">

                    <form method="POST" action="{{ route('admin.country.store') }}">
                        @csrf

                        <div class="form-group">
                            <label class="col-md-12">Title</label>
                            <div class="col-md-12">
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control form-control-line"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <button class="btn btn-success text-white">
                                    Save
                                </button>
                                <a href="{{ route('admin.country.index') }}"
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
