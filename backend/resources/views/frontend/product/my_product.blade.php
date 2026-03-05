@extends('frontend.layouts.app')

@section('title', 'My Product')

@section('content')
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

            @if($products->count() == 0)
                <h3>Không có mặt hàng nào</h3>
            @else
                <div class="table-responsive cart_info">
                    <table class="table table-condensed">
                        <thead>
                            <tr class="cart_menu">
                                <td class="image">image</td>
                                <td class="description">name</td>
                                <td class="price">price</td>
                                <td class="total">action</td>
                            </tr>
                        </thead>
                        <tbody>

                        @foreach($products as $product)
                            @php
                                $images = json_decode($product->image, true);
                                $thumb = $images[0] ?? null;
                            @endphp

                            <tr>
                                <td class="cart_product">
                                    @if($thumb)
                                        <img src="{{ asset('upload/product/' . $thumb) }}"
                                             width="80">
                                    @endif
                                </td>

                                <td class="cart_description">
                                    <h4>{{ $product->name }}</h4>
                                </td>

                                <td class="cart_price">
                                    <p>${{ $product->price }}</p>
                                </td>

                                <td class="cart_total">
                                    <a href="{{ route('member.product.edit', $product->id) }}">edit</a> |
                                    <a href="{{ route('member.product.delete', $product->id) }}"
                                    onclick="return confirm('Delete product?')">
                                    delete
                                    </a>

                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
