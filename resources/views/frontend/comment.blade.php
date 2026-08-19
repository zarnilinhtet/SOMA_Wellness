<div class="comment-item position-relative mb-3">
    <div class="d-flex gap-2 align-items-start">

        <div class="flex-shrink-0">
            <!-- Avatar updated to brand colors -->
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-semibold"
                style="width: 36px; height: 36px; font-size: 13px; background-color: rgba(141, 126, 113, 0.15); color: var(--soma-secondary);">
                {{ strtoupper(substr($comment->user->name, 0, 2)) }}
            </div>
        </div>

        <div class="flex-grow-1">
            <!-- Comment bubble updated to Soft Cream background -->
            <div class="rounded-4 p-2 px-3 border-0" style="max-width: 100%; background-color: var(--soma-bg);">
                <!-- Name updated to Perfect Beige -->
                <div class="fw-bold small" style="font-size: 13px; color: var(--soma-primary);">
                    {{ $comment->user->name }}
                </div>
                <!-- Text updated to Desert Taupe -->
                <div class="mt-1" style="font-size: 14px; white-space: pre-line; line-height: 1.4; color: var(--soma-secondary);">
                    {{ $comment->content }}
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 ms-2 mt-1" style="font-size: 12px; font-weight: 600;">
                <span class="fw-normal" style="color: rgba(141, 126, 113, 0.7);">{{ $comment->created_at->diffForHumans() }}</span>

                <button type="button"
                    @click="openReplyModal({{ $comment->id }}, '{{ $comment->user->name }}', '{{ addslashes(Str::limit($comment->content, 60)) }}')"
                    class="btn btn-link p-0 text-decoration-none fw-bold"
                    style="font-size: 12px; color: var(--soma-primary) !important;">
                    Reply
                </button>
                <!-- <button type="button" class="btn btn-link p-0 text-decoration-none fw-bold text-secondary"
                    style="font-size: 12px;">
                    Delete
                </button> -->
            </div>

            @if($comment->replies && $comment->replies->count() > 0)
                <!-- Reply thread border updated to a faded Perfect Beige line -->
                <div class="comment-replies mt-2 border-start border-2 ps-3" style="border-color: rgba(190, 150, 118, 0.3) !important;">
                    @foreach($comment->replies as $reply)
                        @include('frontend.comment', ['comment' => $reply])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>