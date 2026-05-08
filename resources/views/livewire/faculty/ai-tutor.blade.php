<div class="flex flex-col h-[calc(100vh-8rem)]" x-data="{
    messages: @js($messages),
    input: '',
    isLoading: false,
    scrollToBottom() {
        this.$nextTick(() => {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    }
}" x-init="$watch('messages', () => scrollToBottom()); scrollToBottom()">
    <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                <flux:icon.sparkles class="h-6 w-6 text-white" />
            </div>
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">AI Learning Assistant</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Your personal AI tutor</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <flux:button variant="ghost" size="sm" x-on:click="$wire.clearChat()" wire:loading.attr="disabled">
                <flux:icon.trash class="h-4 w-4 mr-2" />
                Clear Chat
            </flux:button>
        </div>
    </div>

    <div class="flex-1 flex gap-4 pt-4 overflow-hidden">
        <div class="w-64 shrink-0 space-y-4 overflow-y-auto">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 p-4">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Your Courses</h3>
                <div class="space-y-2">
                    @forelse($enrollments as $enrollment)
                        <button
                            wire:click="setContext('course-{{ $enrollment['id'] }}')"
                            class="w-full text-left px-3 py-2 rounded-lg text-sm transition-colors {{ $currentContext === 'course-' . $enrollment['id'] ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300' }}"
                        >
                            {{ $enrollment['title'] }}
                        </button>
                    @empty
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">No courses enrolled</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 p-4">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Quick Actions</h3>
                <div class="space-y-2">
                    <button wire:click="setContext('study-tips')" class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 transition-colors">
                        <flux:icon.light-bulb class="h-4 w-4 inline mr-2" />
                        Study Tips
                    </button>
                    <button wire:click="setContext('quiz-help')" class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 transition-colors">
                        <flux:icon.clipboard-document-check class="h-4 w-4 inline mr-2" />
                        Quiz Help
                    </button>
                    <button wire:click="setContext('concept-explain')" class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 transition-colors">
                        <flux:icon.book-open class="h-4 w-4 inline mr-2" />
                        Explain Concept
                    </button>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 p-4">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Current Context</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                    {{ ucfirst(str_replace('-', ' ', $currentContext)) }}
                </span>
            </div>
        </div>

        <div class="flex-1 flex flex-col rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden">
            <div class="flex-1 overflow-y-auto p-4 space-y-4" x-ref="messagesContainer">
                @foreach($messages as $message)
                    <div class="flex gap-3" x-show="true">
                        @if($message['role'] === 'user')
                            <div class="flex-1 flex justify-end">
                                <div class="bg-purple-600 text-white rounded-2xl px-4 py-2 max-w-[80%]">
                                    <p class="text-sm whitespace-pre-wrap">{{ $message['content'] }}</p>
                                </div>
                            </div>
                        @elseif($message['role'] === 'system')
                            <div class="w-full flex justify-center">
                                <span class="text-xs text-zinc-400 dark:text-zinc-500 bg-zinc-100 dark:bg-zinc-700/50 px-3 py-1 rounded-full">
                                    {{ $message['content'] }}
                                </span>
                            </div>
                        @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shrink-0">
                                <flux:icon.sparkles class="h-4 w-4 text-white" />
                            </div>
                            <div class="flex-1 bg-zinc-50 dark:bg-zinc-700/50 rounded-2xl px-4 py-3 max-w-[80%]">
                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                    @markdown($message['content'])
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

                @if($isLoading)
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shrink-0">
                            <flux:icon.sparkles class="h-4 w-4 text-white" />
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-700/50 rounded-2xl px-4 py-3">
                            <div class="flex gap-1">
                                <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="border-t border-zinc-200 dark:border-zinc-700 p-4">
                <form wire:submit.prevent="sendMessage" class="flex gap-3">
                    <div class="flex-1 relative">
                        <flux:input
                            wire:model="input"
                            placeholder="Ask me anything..."
                            x-on:keydown.enter.prevent="$wire.sendMessage()"
                            :disabled="$isLoading"
                        />
                    </div>
                    <flux:button
                        type="submit"
                        :disabled="$isLoading || empty(trim($input))"
                        class="shrink-0"
                    >
                        <flux:icon.paper-airplane class="h-4 w-4" />
                    </flux:button>
                </form>
                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-2 text-center">
                    AI responses are generated automatically and may not always be accurate.
                </p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.prose strong {
    font-weight: 600;
    color: inherit;
}
.prose ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin: 0.5rem 0;
}
.prose ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin: 0.5rem 0;
}
.prose li {
    margin: 0.25rem 0;
}
</style>
@endpush

@if(!function_exists('markdown'))
@php
function markdown($text) {
    $text = e($text);
    $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $text);
    $text = preg_replace('/`(.*?)`/', '<code class="px-1 py-0.5 bg-zinc-100 dark:bg-zinc-700 rounded text-sm">$1</code>', $text);
    $text = preg_replace('/^(\d+\.)\s+(.*)$/m', '<li>$2</li>', $text);
    $text = preg_replace('/^(\-)\s+(.*)$/m', '<li>$2</li>', $text);
    $text = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $text);
    $text = nl2br($text);
    return $text;
}
@endphp
@endif