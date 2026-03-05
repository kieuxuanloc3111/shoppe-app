@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')

<div class="container-fluid">
<div class="card">
<div class="card-body">

@if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
      action="{{ route('admin.product.update', $product->id) }}"
      enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="form-group mb-3">
    <label>Name</label>
    <input type="text" name="name"
           value="{{ $product->name }}"
           class="form-control" required>
</div>

<div class="form-group mb-3">
    <label>Price</label>
    <input type="number" name="price"
           value="{{ $product->price }}"
           class="form-control" required>
</div>

<div class="form-group mb-3">
    <label>Category</label>
    <select name="category_id" class="form-control">
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}"
                {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group mb-3">
    <label>Brand</label>
    <select name="brand_id" class="form-control">
        @foreach($brands as $brand)
            <option value="{{ $brand->id }}"
                {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                {{ $brand->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group mb-3">
    <label>Status</label>
    <select name="status" id="sale-select" class="form-control">
        <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>New</option>
        <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Sale</option>
    </select>
</div>

<div class="form-group mb-3">
    <input type="number"
           name="sale_price"
           id="sale-price"
           value="{{ $product->sale_price }}"
           class="form-control"
           placeholder="Sale price"
           style="{{ $product->status ? '' : 'display:none' }}">
</div>

<div class="form-group mb-3">
    <label>Company</label>
    <input type="text"
           name="company"
           value="{{ $product->company }}"
           class="form-control">
</div>

<div class="form-group mb-3">
    <label>Detail</label>
    <textarea name="detail"
              rows="5"
              class="form-control">{{ $product->detail }}</textarea>
</div>

<hr>

<h5>Old Images (tick to delete)</h5>

<div style="display:flex;flex-wrap:wrap">
@foreach($oldImages as $img)
    <div style="margin:5px;text-align:center">
        <img src="{{ asset('upload/product/'.$img) }}" width="100">
        <br>
        <input type="checkbox" name="delete_images[]" value="{{ $img }}">
        delete
    </div>
@endforeach
</div>

<hr>

<label>Upload new images (max 3)</label>
<input type="file" name="images[]" multiple
       accept="image/*"
       class="form-control">

<br>

<button class="btn btn-success">Update</button>
<a href="{{ route('admin.product.index') }}"
   class="btn btn-secondary">Back</a>

</form>

</div>
</div>
</div>

@endsection