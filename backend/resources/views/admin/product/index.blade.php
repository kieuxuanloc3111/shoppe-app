@extends('admin.layouts.app')

@section('title', 'Product List')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <form method="GET" action="{{ route('admin.product.index') }}" class="mb-3">
                <input type="text"
                       name="member_name"
                       class="form-control"
                       placeholder="Search by member name"
                       value="{{ request('member_name') }}">
                <br>
                <button class="btn btn-primary">Search</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Member</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Company</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($products as $product)

                        @php
                            $imgs = json_decode($product->image, true);
                        @endphp

                        <tr>
                            <td>{{ $product->id }}</td>

                            <td>
                                @if($imgs && count($imgs))
                                    <img src="{{ asset('upload/product/'.$imgs[0]) }}" width="60">
                                @endif
                            </td>

                            <td>{{ $product->name }}</td>
                            <td>{{ $product->user->name ?? '' }}</td>
                            <td>{{ $product->category->name ?? '' }}</td>
                            <td>{{ $product->brand->name ?? '' }}</td>
                            <td>{{ $product->company }}</td>
                            <td>{{ $product->price }}</td>

                            <td>
                                @if($product->status == 1)
                                    <span>Sale</span>
                                @else
                                    <span>New</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('admin.product.edit', $product->id) }}"
                                   class="btn btn-sm btn-warning text-white">
                                   Edit
                                </a>

                                <a href="{{ route('admin.product.delete', $product->id) }}"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this product?')">
                                   Delete
                                </a>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

@endsection