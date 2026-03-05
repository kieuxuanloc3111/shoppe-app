@extends('frontend.layouts.app')

@section('title', 'Blog')

@section('content')
<section>
    <div class="container">
        <div class="row">

            <div class="col-sm-3">
                @include('frontend.layouts.menuleft')
            </div>
            
<!-- blog list -->
            <div class="col-sm-9">
                <div class="blog-post-area">
                    <h2 class="title text-center">Latest From our Blog</h2>

                    @foreach($blogs as $blog)
                        <div class="single-blog-post">
                            <h3>{{ $blog->title }}</h3>

                            <div class="post-meta">
                                <ul>
                                    <li>
                                        <i class="fa fa-user"></i>
                                        Admin
                                    </li>
                                    <li>
                                        <i class="fa fa-calendar"></i>
                                        {{ optional($blog->created_at)->format('M d, Y') }}
                                    </li>
                                </ul>
                            </div>

                            @if($blog->image)
                                <img 
                                    src="{{ asset($blog->image) }}" 
                                    alt="" 
                                    class="img-responsive"
                                    style="max-width:100%; height:auto;"
                                >
                            @endif


                            <p>
                                {!! \Illuminate\Support\Str::limit(strip_tags($blog->description), 200) !!}
                            </p>

                            <a class="btn btn-primary"
                            href="{{ route('blog.detail', $blog->id) }}">
                                Read More
                            </a>

                        </div>
                    @endforeach

                    <div class="pagination-area text-center">
                        {{ $blogs->links('pagination::bootstrap-4') }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection
