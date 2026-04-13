@php
    $threadReplies = $comment->threadReplies ?? collect();
    $indentClass = match (true) {
        $depth <= 0 => '',
        $depth === 1 => 'ml-4',
        $depth === 2 => 'ml-8',
        default => 'ml-10',
    };

    $cardClass = $depth === 0
        ? 'border border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950/60'
        : 'border border-zinc-200/80 bg-white dark:border-zinc-800 dark:bg-zinc-900';
@endphp

<div wire:key="comment-node-{{ $comment->id }}" class="{{ $indentClass }}">
    <div class="rounded-2xl p-3 {{ $cardClass }}">
        <div class="flex items-center justify-between gap-3">
            <p class="text-sm font-semibold text-zinc-950 dark:text-white">{{ $comment->user?->name ?? 'Unknown user' }}</p>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $comment->created_at?->diffForHumans() }}</p>
        </div>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ $comment->comment_text }}</p>

        <div class="mt-3 border-t border-zinc-200 pt-3 dark:border-zinc-800">
            @if ($activeReplyCommentId === $comment->id)
                <div class="space-y-2">
                    <flux:textarea
                        wire:model="replyDrafts.{{ $comment->id }}"
                        placeholder="Write a reply..."
                        rows="2"
                    />
                    <div class="flex justify-end gap-2">
                        <flux:button wire:click="toggleReplyForm({{ $comment->id }})" size="sm" variant="ghost">Cancel</flux:button>
                        <flux:button wire:click="postReply({{ $comment->id }})" size="sm" variant="outline">Post Reply</flux:button>
                    </div>
                </div>
            @else
                <div class="flex justify-end">
                    <flux:button wire:click="toggleReplyForm({{ $comment->id }})" size="sm" variant="outline">Reply</flux:button>
                </div>
            @endif
        </div>
    </div>

    @if ($threadReplies->isNotEmpty())
        <div class="mt-2 space-y-2 border-l border-zinc-200 pl-3 dark:border-zinc-800">
            @foreach ($threadReplies as $reply)
                @include('livewire.faculty.partials.comment-thread', ['comment' => $reply, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
