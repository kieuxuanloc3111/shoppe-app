@extends('admin.layouts.app')

@section('title', 'Blog')

@section('content')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Blog</h4>
        </div>
        <div class="col-7 align-self-center text-end">
            <a href="{{ route('admin.blog.create') }}"
               class="btn btn-success text-white">
                Add Blog
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered no-wrap">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Title</th>
                                    <th>Image</th>
                                    <th>Description</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($blogs as $blog)
                                    <tr>
                                        <td>{{ $blog->id }}</td>
                                        <td>{{ $blog->title }}</td>
                                        <td>
                                            @if($blog->image)
                                                <img
                                                    src="{{ asset($blog->image) }}"
                                                    alt="{{ $blog->title }}"
                                                    width="80"
                                                    style="object-fit: cover;"
                                                >
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>

                                        <td>{!! $blog->description !!}</td>
                                        <td>
                                            <a href="{{ route('admin.blog.edit', $blog->id) }}"
                                               class="btn btn-sm btn-warning text-white">
                                                Edit
                                            </a>

                                            <a href="{{ route('admin.blog.delete', $blog->id) }}"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Delete this blog?')">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            No blog found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection
