@extends('frontend.layouts.app')

@section('title', $product->name)

@section('content')
<section>
    <div class="container">
        <div class="row">

            <div class="col-sm-3">
                @include('frontend.layouts.menuleft')
            </div>

            <div class="product-details">
                <div class="col-sm-5">
                    <div class="view-product">
                        @php
                            $mainImage = $images[0] ?? null;
                        @endphp

                        @if($mainImage)
                            <a href="{{ asset('upload/product/'.$mainImage) }}"
                            rel="prettyPhoto[product]">
                                <img id="main-image"
                                    src="{{ asset('upload/product/'.$mainImage) }}"
                                    alt="">
                            </a>

                            <a href="{{ asset('upload/product/'.$mainImage) }}"
                            rel="prettyPhoto[product]">
                                <h3>ZOOM</h3>
                            </a>
                        @endif

                    </div>

                    <!-- slide -->
                    @if(count($images) > 0)
                        <div id="similar-product"
                             class="carousel slide"
                             data-ride="carousel">

                            <div class="carousel-inner">

                                @foreach(array_chunk($images, 3) as $key => $chunk)
                                    <div class="item {{ $key == 0 ? 'active' : '' }}">
                                        @foreach($chunk as $img)
                                            <a href="javascript:void(0)"
                                               onclick="changeImage('{{ asset('upload/product/'.$img) }}')">
                                                <img src="{{ asset('upload/product/85x84_'.$img) }}" alt="">
                                            </a>
                                        @endforeach
                                    </div>
                                @endforeach

                            </div>

                            <a class="left item-control"
                               href="#similar-product"
                               data-slide="prev">
                                <i class="fa fa-angle-left"></i>
                            </a>

                            <a class="right item-control"
                               href="#similar-product"
                               data-slide="next">
                                <i class="fa fa-angle-right"></i>
                            </a>

                        </div>
                    @endif
                </div>

                <div class="col-sm-7">
                    <div class="product-information">

                        @if($product->sale == 1)
                            <img src="{{ asset('frontend/images/product-details/sale.png') }}"
                                 class="newarrival"
                                 alt="">
                        @else
                            <img src="{{ asset('frontend/images/product-details/new.jpg') }}"
                                 class="newarrival"
                                 alt="">
                        @endif

                        <h2>{{ $product->name }}</h2>
                        <p>Product ID: {{ $product->id }}</p>

                        <span>
                            @if($product->sale == 1)
                                <span>${{ $product->sale_price }}</span>
                                <del>${{ $product->price }}</del>
                            @else
                                <span>${{ $product->price }}</span>
                            @endif

                            <label>Quantity:</label>
                            <input type="number" value="1" min="1">

                            <button type="button"
                                    class="btn btn-fefault cart">
                                <i class="fa fa-shopping-cart"></i>
                                Add to cart
                            </button>
                        </span>

                        <p><b>Availability:</b> In Stock</p>
                        <p><b>Condition:</b> {{ $product->sale ? 'Sale' : 'New' }}</p>
                        <p><b>Brand:</b> {{ optional($product->brand)->name }}</p>
                        <p><b>Category:</b> {{ optional($product->category)->name }}</p>

                        <a href="#">
                            <img src="{{ asset('frontend/images/product-details/share.png') }}"
                                 class="share img-responsive"
                                 alt="">
                        </a>

                    </div>
                </div>

                <div class="col-sm-12">
                    <h3>Description</h3>
                    <p>{{ $product->detail }}</p>
                </div>

            </div>

        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $("a[rel^='prettyPhoto']").prettyPhoto({
        theme: 'light_square',
    });
});

function changeImage(src) {
    $('#main-image').attr('src', src);
    $('.view-product a').attr('href', src);
}
</script>
@endsection
