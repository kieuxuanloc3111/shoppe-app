@extends('frontend.layouts.app')

@section('title', $blog->title)

@section('content')
<section>
    <div class="container">
        <div class="row">

            <div class="col-sm-3">
                @include('frontend.layouts.menuleft')
            </div>

            <!-- detail -->
            <div class="col-sm-9">
                <div class="blog-post-area">
                    <h2 class="title text-center">Blog Detail</h2>

                    <div class="single-blog-post">
                        <h3>{{ $blog->title }}</h3>

                        <div class="post-meta">
                            <ul>
                                <li><i class="fa fa-user"></i> Admin</li>
                                <li>
                                    <i class="fa fa-clock-o"></i>
                                    {{ optional($blog->created_at)->format('h:i a') }}
                                </li>
                                <li>
                                    <i class="fa fa-calendar"></i>
                                    {{ optional($blog->created_at)->format('M d, Y') }}
                                </li>
                            </ul>
                        </div>

                        @if($blog->image)
                            <img src="{{ asset($blog->image) }}"
                                 class="img-responsive"
                                 style="max-width:100%;height:auto;">
                        @endif

                        {!! $blog->description !!}

                            <!-- prev next  -->
                        <div class="pager-area">
                            <ul class="pager pull-right">
                                @if($prevBlog)
                                    <li>
                                        <a href="{{ route('blog.detail', $prevBlog->id) }}">Pre</a>
                                    </li>
                                @endif
                                @if($nextBlog)
                                    <li>
                                        <a href="{{ route('blog.detail', $nextBlog->id) }}">Next</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- rating  -->
                <div class="rating-area">
                    <div class="rate" data-blog="{{ $blog->id }}">
                        <div class="vote">
                            @for($i = 1; $i <= 5; $i++)
                                <span
                                    class="ratings_stars {{ $i <= round($avgRate ?? 0) ? 'active' : '' }}"
                                    data-value="{{ $i }}">
                                    ★
                                </span>
                            @endfor
                            <span class="rate-np">
                                {{ round($avgRate ?? 0, 1) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="socials-share">
                    <img src="{{ asset('frontend/images/blog/socials.png') }}" alt="">
                </div>

                <!-- comment -->
                <div class="response-area">
                    <h2>{{ $comments->count() }} RESPONSES</h2>
                    <ul class="media-list" id="comment-list">
                        @foreach($comments as $comment)
                            @include('frontend.blog._single_comment', ['comment' => $comment])
                        @endforeach
                    </ul>
                </div>

    
                <div class="replay-box">
                    <h2>Leave a comment</h2>
                    <textarea id="comment-content" class="form-control" rows="5"></textarea>
                    <br>
                    <button class="btn btn-primary" id="btn-comment">
                        Post comment
                    </button>
                </div>
                
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    console.log('JS LOADED');

    let isLogin = {{ auth()->check() ? 'true' : 'false' }};
    let blogId  = {{ $blog->id }};

    // rate
    $('.ratings_stars').on('click', function () {

        let rate = $(this).data('value');

        $.ajax({
            url: "{{ route('blog.rate') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                blog_id: blogId,
                rate: rate
            },
            success: function (res) {

                if (res.status === 'not_login') {
                    alert('Vui lòng đăng nhập để đánh giá');
                    return;
                }

                $('.rate-np').text(res.avg);

                $('.ratings_stars').removeClass('active');
                $('.ratings_stars').each(function () {
                    if ($(this).data('value') <= rate) {
                        $(this).addClass('active');
                    }
                });
            }
        });
    });

    // comment
    $('#btn-comment').click(function () {

        if (!isLogin) {
            alert('Vui lòng login để comment');
            return;
        }

        let content = $('#comment-content').val().trim();
        if (!content) return;

        $.post("{{ route('blog.comment') }}", {
            _token: "{{ csrf_token() }}",
            blog_id: blogId,
            content: content
        }, function (res) {

            if (res.status === 'success') {
                $('#comment-list').prepend(res.html);
                $('#comment-content').val('');
            }
        });
    });


    // reply
    $(document).on('click', '.btn-reply', function () {
        $('#reply-form-' + $(this).data('id')).toggle();
    });

    // comment con
    $(document).on('click', '.btn-send-reply', function () {

        if (!isLogin) {
            alert('Vui lòng login để reply');
            return;
        }

        let parentId = $(this).data('parent');
        let content  = $(this).prev('.reply-content').val().trim();
        let box      = $(this).closest('.media-body');

        if (!content) return;

        $.post("{{ route('blog.comment') }}", {
            _token: "{{ csrf_token() }}",
            blog_id: blogId,
            parent_id: parentId,
            content: content
        }, function (res) {
            if (res.status === 'success') {
                box.append(res.html);
            }
        });
    });

});
</script>
@endsection
