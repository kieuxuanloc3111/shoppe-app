@extends('frontend.layouts.app')

@section('title', 'Add Product')

@section('content')
<section>
    <div class="container">
        <div class="row">

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


            <div class="col-sm-9">
                <div class="signup-form">
                    <h2>Add Product</h2>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST"
                          action="{{ route('member.product.store') }}"
                          enctype="multipart/form-data">
                        @csrf

                        <input type="text" name="name" placeholder="Product name" required>

                        <input type="number" name="price" placeholder="Price" required>

                        <select name="category_id" required>
                            <option value="">-- Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>


                        <select name="brand_id" required>
                            <option value="">-- Brand --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>

 
                        <select name="sale" id="sale-select">
                            <option value="0">New</option>
                            <option value="1">Sale</option>
                        </select>

  
                        <input type="number"
                               name="sale_price"
                               id="sale-price"
                               placeholder="Sale price"
                               style="display:none;">

   
                        <input type="text" name="company" placeholder="Company profile" required>

                  
                        <textarea name="detail" rows="5" placeholder="Detail" required></textarea>

                   
                        <label>Product Images (max 3 images)</label>
                        <input type="file"
                               name="images[]"
                               id="image-input"
                               multiple
                               accept="image/*">

                  
                        <div id="image-preview" style="margin-top:10px;"></div>

                        <br>

                        <button class="btn btn-default">
                            Add product
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>

document.getElementById('sale-select').addEventListener('change', function () {
    document.getElementById('sale-price').style.display =
        this.value == 1 ? 'block' : 'none';
});



const input = document.getElementById('image-input');
const preview = document.getElementById('image-preview');

let dataTransfer = new DataTransfer();

input.addEventListener('change', function (e) {

    const files = Array.from(e.target.files);

    files.forEach(file => {

        if (dataTransfer.files.length >= 3) {
            alert('Chỉ được upload tối đa 3 hình');
            return;
        }

        if (!file.type.startsWith('image/')) {
            alert(file.name + ' không phải là hình ảnh');
            return;
        }

        if (file.size > 1024 * 1024) {
            alert(file.name + ' lớn hơn 1MB');
            return;
        }

        dataTransfer.items.add(file);

        const reader = new FileReader();
        reader.onload = function (event) {
            const img = document.createElement('img');
            img.src = event.target.result;
            img.style.width = '100px';
            img.style.margin = '5px';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });

    input.files = dataTransfer.files;
});
</script>
@endsection
