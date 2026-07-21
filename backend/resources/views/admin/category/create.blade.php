@extends('admin.layouts.app')

@section('title', 'Add Category')

@section('content')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Add Category</h4>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">

                    <form method="POST" action="{{ route('admin.category.store') }}">
                        @csrf

                        <div class="form-group">
                            <label class="col-md-12">Name</label>
                            <div class="col-md-12">
                                <input type="text"
                                       name="name"
                                       class="form-control form-control-line"
                                       required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12">Danh mục cha (để trống = gốc)</label>
                            <div class="col-md-12">
                                <select name="parent_id" class="form-control form-control-line">
                                    <option value="">— Không (danh mục gốc) —</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12">Hoa hồng (%)</label>
                            <div class="col-md-12">
                                <input type="number" step="0.01" min="0" max="100"
                                       name="commission_rate"
                                       value="0"
                                       class="form-control form-control-line">
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-success text-white">Save</button>
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
