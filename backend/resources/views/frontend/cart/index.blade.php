@extends('frontend.layouts.app')

@section('title', 'Cart')

@section('content')
<section id="cart_items">
    <div class="container">

        <div class="breadcrumbs">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="active">Shopping Cart</li>
            </ol>
        </div>

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

                <tbody id="cart-body">
                @php $subTotal = 0; @endphp

                @forelse($cart as $item)
                    @php
                        $rowTotal = $item['price'] * $item['qty'];
                        $subTotal += $rowTotal;
                    @endphp

                    <tr data-id="{{ $item['id'] }}">
                        <td class="cart_product">
                            <img src="{{ asset('upload/product/85x84_'.$item['image']) }}">
                        </td>

                        <td class="cart_description">
                            <h4>{{ $item['name'] }}</h4>
                        </td>

                        <td class="cart_price">
                            <p>${{ $item['price'] }}</p>
                        </td>

                        <td class="cart_quantity">
                            <div class="cart_quantity_button">
                                <a class="cart_quantity_up" href="javascript:void(0)"> + </a>
                                <input class="cart_quantity_input"
                                       type="text"
                                       value="{{ $item['qty'] }}"
                                       size="2">
                                <a class="cart_quantity_down" href="javascript:void(0)"> - </a>
                            </div>
                        </td>

                        <td class="cart_total">
                            <p class="cart_total_price">${{ $rowTotal }}</p>
                        </td>

                        <td class="cart_delete">
                            <a class="cart_quantity_delete"
                               href="javascript:void(0)">
                                <i class="fa fa-times"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Không có sản phẩm nào
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>
    </div>
</section>

<section id="do_action">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="chose_area">
                    {{-- để cho đẹp --}}
                </div>
            </div>

            <div class="col-sm-6">
                <div class="total_area">
                    <ul>
                        <li>Cart Sub Total <span id="sub-total">${{ $subTotal }}</span></li>
                        <li>Eco Tax <span>$0</span></li>
                        <li>Shipping Cost <span>Free</span></li>
                        <li>Total <span id="total">${{ $subTotal }}</span></li>
                    </ul>
                    <a class="btn btn-default check_out" href="/checkout">
                        Check Out
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script>
$(function () {

    function updateCart(id, qty, row) {
        $.post("{{ route('cart.update') }}", {
            _token: "{{ csrf_token() }}",
            id: id,
            qty: qty
        }, function (res) {

            if (qty <= 0) {
                row.remove();
            } else {
                row.find('.cart_quantity_input').val(qty);
                let price = parseFloat(row.find('.cart_price p').text().replace('$',''));
                row.find('.cart_total_price').text('$' + (price * qty));
            }

            $('#sub-total').text('$' + res.total);
            $('#total').text('$' + res.total);
            $('#cart-count').text(res.count);
        });
    }

    // +
    $('.cart_quantity_up').click(function () {
        let row = $(this).closest('tr');
        let id  = row.data('id');
        let qty = parseInt(row.find('.cart_quantity_input').val()) + 1;
        updateCart(id, qty, row);
    });

    // -
    $('.cart_quantity_down').click(function () {
        let row = $(this).closest('tr');
        let id  = row.data('id');
        let qty = parseInt(row.find('.cart_quantity_input').val()) - 1;
        updateCart(id, qty, row);
    });

    // delete
    $('.cart_quantity_delete').click(function () {
        let row = $(this).closest('tr');
        let id  = row.data('id');

        $.post("{{ route('cart.delete') }}", {
            _token: "{{ csrf_token() }}",
            id: id
        }, function (res) {
            row.remove();
            $('#sub-total').text('$' + res.total);
            $('#total').text('$' + res.total);
            $('#cart-count').text(res.count);
        });
    });

});
</script>
@endsection
