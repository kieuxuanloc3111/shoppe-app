@extends('frontend.layouts.app')

@section('title', 'Checkout')

@section('content')

<section id="cart_items">
    <div class="container">

        <div class="breadcrumbs">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="active">Checkout</li>
            </ol>
        </div>


        <div class="shopper-informations">
            <div class="row">

                <!-- not login -->
                @guest
                <div class="col-sm-4">
                    <div class="shopper-info">
                        <p>Register & Checkout</p>

                        <form method="POST" action="{{ route('checkout.process') }}">
                            @csrf

                            <input type="email"
                                   name="email"
                                   placeholder="Email"
                                   required>

                            <input type="password"
                                   name="password"
                                   placeholder="Password"
                                   required>

                            <input type="text"
                                   name="name"
                                   placeholder="Your Name">

                            <input type="text"
                                   name="phone"
                                   placeholder="Phone">

                            <button type="submit"
                                    class="btn btn-primary">
                                Continue
                            </button>
                        </form>
                    </div>
                </div>
                @endguest

                <!-- logined -->
                @auth
                <div class="col-sm-4">
                    <div class="shopper-info">
                        <p>Shopper Information</p>

                        <form method="POST" action="{{ route('checkout.process') }}">
                            @csrf

                            <input type="text"
                                   name="name"
                                   value="{{ auth()->user()->name ?? auth()->user()->email }}"
                                   placeholder="Your Name">

                            <input type="text"
                                name="phone"
                                value="{{ auth()->user()->phone ?? '' }}"
                                placeholder="Phone">


                            <button type="submit"
                                    class="btn btn-primary">
                                Continue
                            </button>
                        </form>
                    </div>
                </div>
                @endauth

                <!-- don hang -->
                <div class="col-sm-8">
                    <div class="table-responsive cart_info">
                        <table class="table table-condensed">
                            <thead>
                                <tr class="cart_menu">
                                    <td class="image">Item</td>
                                    <td class="description"></td>
                                    <td class="price">Price</td>
                                    <td class="quantity">Quantity</td>
                                    <td class="total">Total</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>

                            @php $subTotal = 0; @endphp

                            @foreach($cart as $item)
                                @php
                                    $rowTotal = $item['price'] * $item['qty'];
                                    $subTotal += $rowTotal;
                                @endphp
                                <tr>
                                    <td class="cart_product">
                                        <img src="{{ asset('upload/product/85x84_'.$item['image']) }}" alt="">
                                    </td>

                                    <td class="cart_description">
                                        <h4>{{ $item['name'] }}</h4>
                                    </td>

                                    <td class="cart_price">
                                        <p>${{ $item['price'] }}</p>
                                    </td>

                                    <td class="cart_quantity">
                                        <p>{{ $item['qty'] }}</p>
                                    </td>

                                    <td class="cart_total">
                                        <p class="cart_total_price">${{ $rowTotal }}</p>
                                    </td>

                                    <td></td>
                                </tr>
                            @endforeach


                            <tr>
                                <td colspan="4">&nbsp;</td>
                                <td colspan="2">
                                    <table class="table table-condensed total-result">
                                        <tr>
                                            <td>Cart Sub Total</td>
                                            <td>${{ $subTotal }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total</td>
                                            <td><span>${{ $subTotal }}</span></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>

    </div>
</section>

@endsection
