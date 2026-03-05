<li class="media">
    <a class="pull-left" href="#">
        <img class="media-object"
             src="{{ asset($comment->user_avatar) }}"
             width="50">
    </a>

    <div class="media-body">
        <ul class="sinlge-post-meta">
            <li><i class="fa fa-user"></i> {{ $comment->user_name }}</li>
            <li><i class="fa fa-clock-o"></i> {{ $comment->created_at->format('h:i a') }}</li>
            <li><i class="fa fa-calendar"></i> {{ $comment->created_at->format('M d, Y') }}</li>
        </ul>

        <p>{{ $comment->content }}</p>

        <a href="javascript:void(0)"
           class="btn btn-primary btn-reply"
           data-id="{{ $comment->id }}">
            <i class="fa fa-reply"></i> Reply
        </a>

        @if($comment->children && $comment->children->count())
            <ul class="media-list second-media">
                @foreach($comment->children as $child)
                    @include('frontend.blog._single_comment', ['comment' => $child])
                @endforeach
            </ul>
        @endif

        <div class="reply-form" id="reply-form-{{ $comment->id }}" style="display:none;">
            <textarea class="form-control reply-content"></textarea>
            <button class="btn btn-sm btn-primary btn-send-reply"
                    data-parent="{{ $comment->id }}">
                Gửi
            </button>
        </div>
    </div>
</li>
