@extends('frontend.layouts.app')

@section('title', 'Edit Product')

@section('content')
<section>
<div class="container">
<div class="row">

{{-- LEFT --}}
<div class="col-sm-3">
    <div class="left-sidebar">
        <h2>Account</h2>
        <div class="panel-group category-products">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="{{ route('member.profile') }}">Account</a>
                    </h4>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="{{ route('member.product.my') }}">My product</a>
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- RIGHT --}}
<div class="col-sm-9">
<div class="signup-form">
<h2>Edit Product</h2>

<form method="POST"
      action="{{ route('member.product.update', $product->id) }}"
      enctype="multipart/form-data">
@csrf

<input type="text" name="name" value="{{ $product->name }}" required>
<input type="number" name="price" value="{{ $product->price }}" required>

<select name="category_id" required>
    @foreach($categories as $cat)
        <option value="{{ $cat->id }}"
            {{ $product->category_id == $cat->id ? 'selected' : '' }}>
            {{ $cat->name }}
        </option>
    @endforeach
</select>

<select name="brand_id" required>
    @foreach($brands as $brand)
        <option value="{{ $brand->id }}"
            {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
            {{ $brand->name }}
        </option>
    @endforeach
</select>

{{-- SALE --}}
<select name="sale" id="sale-select">
    <option value="0" {{ $product->sale == 0 ? 'selected' : '' }}>New</option>
    <option value="1" {{ $product->sale == 1 ? 'selected' : '' }}>Sale</option>
</select>

<input type="number"
       name="sale_price"
       id="sale-price"
       value="{{ $product->sale_price }}"
       style="{{ $product->sale ? '' : 'display:none' }}">

<input type="text" name="company" value="{{ $product->company }}" required>

<textarea name="detail" rows="5">{{ $product->detail }}</textarea>

<hr>
<h4>Old Images (tick to delete)</h4>

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
<input type="file" name="images[]" id="image-input" multiple accept="image/*">

<div id="image-preview" style="margin-top:10px;display:flex"></div>

<br>
<button class="btn btn-default">Update product</button>

</form>
</div>
</div>

</div>
</div>
</section>
@endsection

@section('scripts')
<script>
/* ================= SALE ================= */
document.getElementById('sale-select').addEventListener('change', function () {
    document.getElementById('sale-price').style.display =
        this.value == 1 ? 'block' : 'none';
});

/* ================= OLD IMAGE DELETE UI ================= */
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

/* ================= NEW IMAGE PREVIEW ================= */
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
</script>
@endsection