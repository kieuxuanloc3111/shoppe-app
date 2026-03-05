<h2 style="font-family: Arial, sans-serif;">
    Xin chào {{ $user->email }}
</h2>

<p style="font-family: Arial, sans-serif;">
    Cảm ơn bạn đã mua hàng tại shop.
</p>

<table width="100%" cellpadding="0" cellspacing="0"
       style="border-collapse: collapse; font-family: Arial, sans-serif;">

    {{-- HEADER --}}
    <thead>
        <tr style="background:#FE980F; color:#fff;">
            <th style="padding:10px; border:1px solid #ddd;">Item</th>
            <th style="padding:10px; border:1px solid #ddd;">Description</th>
            <th style="padding:10px; border:1px solid #ddd;">Price</th>
            <th style="padding:10px; border:1px solid #ddd;">Qty</th>
            <th style="padding:10px; border:1px solid #ddd;">Total</th>
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
                {{-- IMAGE --}}
                <td style="padding:10px; border:1px solid #ddd; text-align:center;">
                    @if(!empty($item['image']))
                        <img src="{{ asset('upload/product/85x84_'.$item['image']) }}"
                             width="70"
                             style="display:block;">
                    @endif
                </td>

                {{-- NAME --}}
                <td style="padding:10px; border:1px solid #ddd;">
                    {{ $item['name'] }}
                </td>

                {{-- PRICE --}}
                <td style="padding:10px; border:1px solid #ddd;">
                    ${{ $item['price'] }}
                </td>

                {{-- QTY --}}
                <td style="padding:10px; border:1px solid #ddd; text-align:center;">
                    {{ $item['qty'] }}
                </td>

                {{-- TOTAL --}}
                <td style="padding:10px; border:1px solid #ddd;">
                    ${{ $rowTotal }}
                </td>
            </tr>
        @endforeach

        {{-- SUB TOTAL --}}
        <tr>
            <td colspan="4"
                style="padding:10px; border:1px solid #ddd; text-align:right;">
                <b>Cart Sub Total</b>
            </td>
            <td style="padding:10px; border:1px solid #ddd;">
                <b>${{ $subTotal }}</b>
            </td>
        </tr>

        {{-- TOTAL --}}
        <tr>
            <td colspan="4"
                style="padding:10px; border:1px solid #ddd; text-align:right;">
                <b>Total</b>
            </td>
            <td style="padding:10px; border:1px solid #ddd; color:#FE980F;">
                <b>${{ $subTotal }}</b>
            </td>
        </tr>
    </tbody>
</table>
