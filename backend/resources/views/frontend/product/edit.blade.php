@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Edit Product</h4>
        </div>
    </div>
</div>

<div class="container-fluid">
<div class="row">
<div class="col-lg-8">

<div class="card">
<div class="card-body">

{{-- ERROR --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form method="POST"
      action="{{ route('admin.product.update', $product->id) }}"
      enctype="multipart/form-data">
@csrf
@method('PUT')

{{-- NAME --}}
<div class="form-group mb-3">
    <label>Name</label>
    <input type="text"
           name="name"
           value="{{ $product->name }}"
           class="form-control form-control-line"
           required>
</div>

{{-- PRICE --}}
<div class="form-group mb-3">
    <label>Price</label>
    <input type="number"
           name="price"
           value="{{ $product->price }}"
           class="form-control form-control-line"
           required>
</div>

{{-- CATEGORY --}}
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

{{-- BRAND --}}
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

{{-- SALE --}}
<div class="form-group mb-3">
    <label>Sale</label>
    <select name="sale" id="sale-select" class="form-control">
        <option value="0" {{ $product->sale == 0 ? 'selected' : '' }}>New</option>
        <option value="1" {{ $product->sale == 1 ? 'selected' : '' }}>Sale</option>
    </select>
</div>

<div class="form-group mb-3">
    <input type="number"
           name="sale_price"
           id="sale-price"
           value="{{ $product->sale_price }}"
           class="form-control"
           placeholder="Sale price"
           style="{{ $product->sale ? '' : 'display:none' }}">
</div>

{{-- COMPANY --}}
<div class="form-group mb-3">
    <label>Company</label>
    <input type="text"
           name="company"
           value="{{ $product->company }}"
           class="form-control">
</div>

{{-- DETAIL --}}
<div class="form-group mb-3">
    <label>Detail</label>
    <textarea name="detail"
              rows="5"
              class="form-control">{{ $product->detail }}</textarea>
</div>

{{-- STATUS (ADMIN ONLY) --}}
<div class="form-group mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Pending</option>
        <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Approved</option>
    </select>
</div>

<hr>

<h5>Old Images (tick to delete)</h5>

<div style="display:flex;flex-wrap:wrap">
@foreach($oldImages as $img)
    <div class="old-image-box" style="margin:5px;text-align:center">
        <img src="{{ asset('upload/product/'.$img) }}"
             width="100"
             class="old-image">
        <br>
        <input type="checkbox"
               name="delete_images[]"
               value="{{ $img }}"
               class="delete-old">
        delete
    </div>
@endforeach
</div>

<hr>

<label>Upload new images (max 3)</label>
<input type="file"
       name="images[]"
       id="image-input"
       multiple
       accept="image/*"
       class="form-control">

<div id="image-preview"
     style="margin-top:10px;display:flex"></div>

<br>

<button class="btn btn-success text-white">Update</button>
<a href="{{ route('admin.product.index') }}"
   class="btn btn-secondary">Back</a>

</form>

</div>
</div>

</div>
</div>
</div>

@endsection


@section('scripts')
<script>

// SALE toggle
document.getElementById('sale-select').addEventListener('change', function () {
    document.getElementById('sale-price').style.display =
        this.value == 1 ? 'block' : 'none';
});

// highlight old image when delete checked
document.querySelectorAll('.delete-old').forEach(cb => {
    cb.addEventListener('change', function () {
        const img = this.closest('.old-image-box').querySelector('img');
        if (this.checked) {
            img.style.opacity = '0.4';
            img.style.border = '2px solid red';
        } else {
            img.style.opacity = '1';
            img.style.border = 'none';
        }
    });
});

// preview new images
const input = document.getElementById('image-input');
const preview = document.getElementById('image-preview');
let dataTransfer = new DataTransfer();

input.addEventListener('change', function (e) {
    Array.from(e.target.files).forEach(file => {

        if (dataTransfer.files.length >= 3) {
            alert('Max 3 images');
            return;
        }

        if (!file.type.startsWith('image/')) return;
        if (file.size > 1024 * 1024) return;

        dataTransfer.items.add(file);

        const reader = new FileReader();
        reader.onload = e => {
            const box = document.createElement('div');
            box.style.position = 'relative';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100px';

            const btn = document.createElement('span');
            btn.innerHTML = '❌';
            btn.style.position = 'absolute';
            btn.style.top = '0';
            btn.style.right = '5px';
            btn.style.cursor = 'pointer';

            btn.onclick = () => {
                const files = Array.from(dataTransfer.files);
                const index = files.findIndex(f => f.name === file.name);
                dataTransfer.items.remove(index);
                input.files = dataTransfer.files;
                box.remove();
            };

            box.appendChild(img);
            box.appendChild(btn);
            preview.appendChild(box);
        };
        reader.readAsDataURL(file);
    });

    input.files = dataTransfer.files;
});

// prevent no image
const form = document.querySelector('form');
form.addEventListener('submit', function (e) {
    const oldChecked = document.querySelectorAll('.delete-old:checked').length;
    const oldTotal   = document.querySelectorAll('.delete-old').length;
    const newFiles   = document.getElementById('image-input').files.length;

    if (oldChecked === oldTotal && newFiles === 0) {
        e.preventDefault();
        alert('Sản phẩm phải có ít nhất 1 ảnh');
    }
});

</script>
@endsection