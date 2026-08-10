<div class="comment-item position-relative mb-3">
    <div class="d-flex gap-2 align-items-start">

        <div class="flex-shrink-0">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-dark fw-semibold"
                style="width: 36px; height: 36px; font-size: 13px; background-color: #e4e6eb;">
                {{ strtoupper(substr($comment->user->name, 0, 2)) }}
            </div>
        </div>

        <div class="flex-grow-1">
            <div class="rounded-4 p-2  px-3 border-0" style="max-width: 100%; background-color: #f0f2f5;">
                <div class="fw-bold small text-dark" style="font-size: 13px;">
                    {{ $comment->user->name }}
                </div>
                <div class="text-dark mt-1" style="font-size: 14px; white-space: pre-line; line-height: 1.4;">
                    {{ $comment->content }}
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 ms-2 mt-1" style="font-size: 12px; font-weight: 600;">
                <span class="text-muted fw-normal">{{ $comment->created_at->diffForHumans() }}</span>

                <button type="button"
                    @click="openReplyModal({{ $comment->id }}, '{{ $comment->user->name }}', '{{ addslashes(Str::limit($comment->content, 60)) }}')"
                    class="btn btn-link p-0 text-decoration-none fw-bold text-primary"
                    style="font-size: 12px; color: #1877f2 !important;">
                    Reply
                </button>
                <!-- <button type="button" class="btn btn-link p-0 text-decoration-none fw-bold text-secondary"
                    style="font-size: 12px;">
                    Delete
                </button> -->
            </div>

            @if($comment->replies && $comment->replies->count() > 0)
                <div class="comment-replies mt-2 border-start border-2 ps-3" style="border-color: #ccd0d5 !important;">
                    @foreach($comment->replies as $reply)
                        @include('frontend.comment', ['comment' => $reply])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>