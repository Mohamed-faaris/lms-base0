<div class="flex flex-col lg:flex-row gap-4 h-[calc(100vh-8rem)]">
    {{-- Desktop Sidebar --}}
    <aside class="hidden lg:block bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl transition-all duration-300 shrink-0 overflow-hidden {{ $sidebarOpen ? 'w-80' : 'w-0 border-0' }}">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Course Modules</h3>
                <flux:button variant="ghost" size="sm" class="!px-2" wire:click="toggleSidebar">
                    <flux:icon.x-mark class="h-4 w-4" />
                </flux:button>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500 dark:text-zinc-400">Progress</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $completedModules }}/{{ $totalModules }} completed</span>
                </div>
                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $courseProgress }}%"></div>
                </div>
            </div>
        </div>
        <div class="p-3 max-h-[calc(100vh-14rem)] overflow-y-auto space-y-1">
            @foreach($modules as $index => $module)
                <button
                    wire:click="selectModule({{ $module->id }})"
                    @if($module->status === 'locked') disabled @endif
                    class="w-full flex items-center gap-3 p-3 rounded-lg text-left transition-colors
                        {{ $currentModule->id === $module->id ? 'bg-blue-600 text-white' : ($module->status === 'locked' ? 'opacity-50 cursor-not-allowed' : 'hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300') }}"
                >
                    <div class="h-8 w-8 rounded-full flex items-center justify-center shrink-0 text-sm font-medium
                        {{ $module->status === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-400' :
                           ($currentModule->id === $module->id ? 'bg-blue-500 text-white' :
                           ($module->status === 'unlocked' || $module->status === 'in-progress' ? 'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400')) }}"
                    >
                        @if($module->status === 'completed')
                            <flux:icon.check class="h-4 w-4" />
                        @elseif($module->status === 'locked')
                            <flux:icon.lock-closed class="h-3 w-3" />
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ $module->title }}</p>
                        <div class="flex items-center gap-2 text-xs opacity-70">
                            <flux:icon.clock class="h-3 w-3" />
                            {{ $module->duration }}
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 space-y-4 flex flex-col min-h-0">
        {{-- Top Bar --}}
        <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                @if(!$sidebarOpen)
                    <flux:button variant="outline" class="hidden lg:flex !px-2" wire:click="toggleSidebar">
                        <flux:icon.bars-3 class="h-4 w-4" />
                    </flux:button>
                @endif
                <flux:button variant="outline" class="lg:hidden !px-2" wire:click="toggleMobileDrawer">
                    <flux:icon.bars-3 class="h-4 w-4" />
                </flux:button>
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 line-clamp-1">
                        {{ $currentModule->title }}
                    </h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Module {{ $modules->search(fn($m) => $m->id === $currentModule->id) + 1 }} of {{ $totalModules }}
                    </p>
                </div>
            </div>
            <flux:badge variant="outline" class="hidden sm:flex">
                <flux:icon.clock class="h-3 w-3 mr-1" />
                {{ $currentModule->duration }}
            </flux:badge>
        </div>

        <div class="flex-1 overflow-y-auto">
            @if(!$showQuiz)
                <div class="space-y-4 pb-4">
                    {{-- Video Container --}}
                    <div class="rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                        <div class="relative pb-[56.25%] bg-black">
                            <div id="player" class="absolute inset-0 w-full h-full"></div>
                        </div>
                    </div>

                    <script src="https://www.youtube.com/iframe_api"></script>
                    <script>
                        let player;
                        const INITIAL_WATCHED_SECONDS_FROM_BACKEND = @json($watchedSeconds);
                        let maxWatched = INITIAL_WATCHED_SECONDS_FROM_BACKEND || 0;
                        let isSeeking = false;

                        function onYouTubeIframeAPIReady() {
                            player = new YT.Player('player', {
                                videoId: '{{ $currentModule->videoId }}',
                                playerVars: {
                                    'controls': 2,
                                    'rel': 0,
                                    'modestbranding': 1,
                                    'disablekb': 1,
                                    'enablejsapi': 1,
                                    'fs': 0,
                                    'iv_load_policy': 3,
                                    'showinfo': 0,
                                    'autohide': 0
                                },
                                events: {
                                    'onReady': onPlayerReady,
                                    'onStateChange': onPlayerStateChange
                                }
                            });
                        }

                        function onPlayerReady() {
                            player.seekTo(maxWatched);

                            setInterval(() => {
                                if (!player || typeof player.getCurrentTime !== "function") return;

                                let current = player.getCurrentTime();

                                if (!isSeeking && current > maxWatched) {
                                    maxWatched = current;
                                }
                            }, 1000);

                            setInterval(() => {
                                if (!player || typeof player.getDuration !== "function") return;

                                fetch('/progress/update', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        module_id: {{ $currentModule->id }},
                                        seconds: Math.floor(maxWatched),
                                        duration: player.getDuration()
                                    })
                                });
                            }, 5000);

                            setInterval(() => {
                                preventSeek();
                            }, 1000);
                        }

                        function onPlayerStateChange(event) {
                            if (event.data === YT.PlayerState.PLAYING) {
                                preventSeek();
                            }
                        }

                        function preventSeek() {
                            if (!player || typeof player.getCurrentTime !== "function") return;
                            if (isSeeking) return;

                            let current = player.getCurrentTime();

                            // prevent early reset bug
                            if (maxWatched < 1) return;

                            if (current > maxWatched + 2) {
                                isSeeking = true;

                                player.seekTo(maxWatched, true);

                                setTimeout(() => {
                                    isSeeking = false;
                                }, 500);
                            }
                        }

                        document.addEventListener('keydown', function (e) {
                            if (['ArrowRight', 'ArrowLeft'].includes(e.key)) {
                                e.preventDefault();
                            }
                        });

                        // Disable progress bar and controls interaction
                        setTimeout(() => {
                            const playerFrame = document.querySelector('#player iframe');
                            if (playerFrame) {
                                // Add CSS to disable progress bar interaction
                                const style = document.createElement('style');
                                style.textContent = `
                                    #player iframe {
                                        pointer-events: none !important;
                                    }
                                    #player {
                                        position: relative;
                                    }
                                    #player::after {
                                        content: '';
                                        position: absolute;
                                        top: 0;
                                        left: 0;
                                        right: 0;
                                        bottom: 40px; /* Leave controls area interactive */
                                        z-index: 10;
                                        cursor: default;
                                    }
                                `;
                                document.head.appendChild(style);
                            }
                        }, 2000);

                        document.addEventListener("visibilitychange", () => {
                            if (document.hidden && player && player.pauseVideo) {
                                player.pauseVideo();
                            }
                        });

                        window.addEventListener("blur", () => {
                            if (player && player.pauseVideo) {
                                player.pauseVideo();
                            }
                        });
                    </script>

                    {{-- In-Video Activities --}}
                    <div class="flex flex-wrap gap-2">
                        <flux:button variant="{{ $showFeedback ? 'primary' : 'outline' }}" size="sm" wire:click="toggleFeedback">
                            <flux:icon.chat-bubble-left-ellipsis class="mr-2 h-4 w-4" />
                            Feedback Activity
                        </flux:button>
                        <flux:button variant="{{ $showPuzzle ? 'primary' : 'outline' }}" size="sm" wire:click="togglePuzzle">
                            <flux:icon.puzzle-piece class="mr-2 h-4 w-4" />
                            Puzzle Challenge
                        </flux:button>
                    </div>

                    {{-- Feedback Panel --}}
                    @if($showFeedback)
                        <div class="p-4 rounded-xl border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20">
                            <div class="flex items-center gap-2 mb-2 font-semibold text-blue-800 dark:text-blue-300">
                                <flux:icon.chat-bubble-left-ellipsis class="h-4 w-4" />
                                Quick Feedback
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-3">
                                How would you apply active learning in your classroom?
                            </p>
                            <textarea
                                class="w-full p-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm resize-none focus:ring-2 focus:ring-blue-500"
                                rows="3"
                                placeholder="Share your thoughts..."
                            ></textarea>
                            <flux:button size="sm" class="mt-2" wire:click="toggleFeedback">Submit Feedback</flux:button>
                        </div>
                    @endif

                    {{-- Puzzle Panel --}}
                    @if($showPuzzle)
                        <div class="p-4 rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20">
                            <div class="flex items-center gap-2 mb-2 font-semibold text-amber-800 dark:text-amber-300">
                                <flux:icon.puzzle-piece class="h-4 w-4" />
                                Puzzle Question
                            </div>
                            <p class="text-sm text-zinc-800 dark:text-zinc-200 mb-3">
                                Arrange these teaching steps in the correct order: Evaluate, Plan, Implement, Assess
                            </p>
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach(['Plan', 'Implement', 'Assess', 'Evaluate'] as $step)
                                    <flux:badge variant="outline" class="cursor-pointer hover:bg-amber-100 dark:hover:bg-amber-800 px-3 py-1">
                                        {{ $step }}
                                    </flux:badge>
                                @endforeach
                            </div>
                            <flux:button size="sm" variant="outline" wire:click="togglePuzzle">Check Answer</flux:button>
                        </div>
                    @endif

                    {{-- Content Body (Text) --}}
                    <div class="prose dark:prose-invert max-w-none p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                        <p>{{ $currentModule->body }}</p>
                    </div>

                    {{-- Take Quiz Button --}}
                    <flux:button variant="primary" class="w-full" wire:click="startQuiz">
                        Take Module Quiz
                    </flux:button>
                </div>
            @else
                {{-- Quiz Section --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 mb-4">
                    <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                        <h3 class="font-semibold text-lg text-zinc-900 dark:text-zinc-100">Module Quiz</h3>
                        <flux:badge variant="solid" color="zinc">Pass: 80%</flux:badge>
                    </div>
                    <div class="p-6">
                        @if(!$quizSubmitted)
                            <div class="space-y-6">
                                @foreach($quizQuestions as $qIndex => $question)
                                    <div class="space-y-3">
                                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $qIndex + 1 }}. {{ $question['question'] }}
                                        </p>
                                        <div class="space-y-2">
                                            @foreach($question['options'] as $option)
                                                <label class="flex items-center space-x-3 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer transition-colors">
                                                    <input 
                                                        type="radio" 
                                                        name="q_{{ $question['id'] }}" 
                                                        value="{{ $option['id'] }}"
                                                        wire:click="setAnswer('{{ $question['id'] }}', '{{ $option['id'] }}')"
                                                        @checked(isset($quizAnswers[$question['id']]) && $quizAnswers[$question['id']] === $option['id'])
                                                        class="text-blue-600 focus:ring-blue-500 h-4 w-4 border-zinc-300"
                                                    >
                                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $option['text'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                                <flux:button 
                                    variant="primary" 
                                    class="w-full" 
                                    wire:click="submitQuiz"
                                >
                                    Submit Quiz
                                </flux:button>
                            </div>
                        @else
                            {{-- Quiz Results --}}
                            <div class="text-center py-8 space-y-6">
                                @if($quizScore >= 80)
                                    <div class="h-24 w-24 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto">
                                        <flux:icon.check-circle class="h-12 w-12 text-emerald-600 dark:text-emerald-400" />
                                    </div>
                                    <div>
                                        <h3 class="text-3xl font-bold text-zinc-900 dark:text-zinc-100 mb-2">Congratulations!</h3>
                                        <p class="text-zinc-500 dark:text-zinc-400">You passed with {{ $quizScore }}%</p>
                                    </div>
                                    <flux:button variant="primary" wire:click="resetQuiz">
                                        Continue to Next Module
                                    </flux:button>
                                @else
                                    <div class="h-24 w-24 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto">
                                        <flux:icon.x-circle class="h-12 w-12 text-red-600 dark:text-red-400" />
                                    </div>
                                    <div>
                                        <h3 class="text-3xl font-bold text-zinc-900 dark:text-zinc-100 mb-2">Score: {{ $quizScore }}%</h3>
                                        <p class="text-zinc-500 dark:text-zinc-400">You need 80% to pass</p>
                                    </div>
                                    <div class="max-w-md mx-auto p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg text-left">
                                        <div class="flex items-start gap-3">
                                            <flux:icon.exclamation-triangle class="h-5 w-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
                                            <p class="text-sm text-amber-800 dark:text-amber-300">Please review the module content and try again to improve your score.</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-3 justify-center pt-2">
                                        <flux:button variant="outline" wire:click="resetQuiz">
                                            <flux:icon.arrow-path class="mr-2 h-4 w-4" />
                                            Review Material
                                        </flux:button>
                                        <flux:button variant="primary" wire:click="retakeQuiz">
                                            Retake Quiz
                                        </flux:button>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Mobile Drawer Overlay --}}
    @if($mobileDrawerOpen)
        <div 
            class="fixed inset-0 z-40 bg-zinc-900/80 backdrop-blur-sm lg:hidden" 
            wire:click="toggleMobileDrawer"
        ></div>
        
        <div class="fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-zinc-800 border-t border-zinc-200 dark:border-zinc-700 rounded-t-2xl max-h-[80vh] lg:hidden flex flex-col animate-in slide-in-from-bottom duration-300">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
                <div class="w-12 h-1.5 bg-zinc-300 dark:bg-zinc-600 rounded-full mx-auto mb-4"></div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Course Modules</h3>
                    <flux:button variant="ghost" size="sm" class="!px-2" wire:click="toggleMobileDrawer">
                        <flux:icon.chevron-down class="h-5 w-5" />
                    </flux:button>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400">Progress</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $completedModules }}/{{ $totalModules }} completed</span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $courseProgress }}%"></div>
                    </div>
                </div>
            </div>
            <div class="p-3 overflow-y-auto flex-1 space-y-1">
                @foreach($modules as $index => $module)
                    <button
                        wire:click="selectModule({{ $module->id }})"
                        @if($module->status === 'locked') disabled @endif
                        class="w-full flex items-center gap-3 p-3 rounded-lg text-left transition-colors
                            {{ $currentModule->id === $module->id ? 'bg-blue-600 text-white' : ($module->status === 'locked' ? 'opacity-50 cursor-not-allowed' : 'hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300') }}"
                    >
                        <div class="h-8 w-8 rounded-full flex items-center justify-center shrink-0 text-sm font-medium
                            {{ $module->status === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-400' :
                            ($currentModule->id === $module->id ? 'bg-blue-500 text-white' :
                            ($module->status === 'unlocked' || $module->status === 'in-progress' ? 'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400')) }}"
                        >
                            @if($module->status === 'completed')
                                <flux:icon.check class="h-4 w-4" />
                            @elseif($module->status === 'locked')
                                <flux:icon.lock-closed class="h-3 w-3" />
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ $module->title }}</p>
                            <div class="flex items-center gap-2 text-xs opacity-70">
                                <flux:icon.clock class="h-3 w-3" />
                                {{ $module->duration }}
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>
