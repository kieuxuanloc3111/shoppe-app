@forelse($products as $product)

    @php
        $images = json_decode($product->image, true);
        $thumb = $images[0] ?? null;
    @endphp

    <div class="col-sm-4">
        <div class="product-image-wrapper">
            <div class="single-products">

                <div class="productinfo text-center">

                    @if($thumb)
                        <a href="{{ route('product.detail', $product->id) }}">
                            <img src="{{ asset('upload/product/'.$thumb) }}">
                        </a>
                    @endif

                    @if($product->is_sale)
                        <h2>
                            <del>${{ number_format($product->price) }}</del>
                            ${{ number_format($product->final_price) }}
                        </h2>
                    @else
                        <h2>${{ number_format($product->price) }}</h2>
                    @endif

                    <p>{{ $product->name }}</p>

                    <a href="javascript:void(0)"
                       class="btn btn-default add-to-cart"
                       data-id="{{ $product->id }}">
                        <i class="fa fa-shopping-cart"></i>
                        Add to cart
                    </a>

                </div>

                <div class="product-overlay">
                    <div class="overlay-content">

                        <h2>${{ number_format($product->final_price) }}</h2>

                        <p>
                            <a href="{{ route('product.detail', $product->id) }}"
                               style="color:#fff">
                                {{ $product->name }}
                            </a>
                        </p>

                        <a href="javascript:void(0)"
                           class="btn btn-default add-to-cart"
                           data-id="{{ $product->id }}">
                            <i class="fa fa-shopping-cart"></i>
                            Add to cart
                        </a>

                    </div>
                </div>

                @if($product->is_sale)
                    <div class="sale-badge">
                        -{{ $product->sale_price }}%
                    </div>
                @else
                    <img src="{{ asset('frontend/images/home/new.png') }}"
                         class="new">
                @endif

            </div>
        </div>
    </div>

@empty
    <p class="text-center">No product</p>

@endforelse