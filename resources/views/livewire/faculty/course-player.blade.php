<div class="flex h-[calc(100vh-8rem)] flex-col gap-4 lg:flex-row">
    <aside class="hidden shrink-0 overflow-hidden rounded-3xl border border-zinc-200 bg-white transition-all duration-300 dark:border-zinc-800 dark:bg-zinc-900 lg:block {{ $sidebarOpen ? 'w-84' : 'w-0 border-0' }}">
        <div class="border-b border-zinc-200 p-5 dark:border-zinc-800">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">Course Flow</p>
                    <h3 class="mt-1 text-lg font-semibold text-zinc-950 dark:text-white">Lesson Queue</h3>
                </div>
                <flux:button variant="ghost" size="sm" class="!px-2" wire:click="toggleSidebar">
                    <flux:icon.x-mark class="h-4 w-4" />
                </flux:button>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/70">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-zinc-500 dark:text-zinc-400">Progress</span>
                    <span class="font-semibold text-zinc-950 dark:text-white">{{ $completedModules }}/{{ $totalModules }}</span>
                </div>
                <div class="mt-3 h-2 rounded-full bg-zinc-200 dark:bg-zinc-800">
                    <div class="h-2 rounded-full bg-gradient-to-r from-blue-500 via-cyan-500 to-emerald-500" style="width: {{ $courseProgress }}%"></div>
                </div>
                <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">{{ $courseProgress }}% complete</p>
            </div>
        </div>

        <div class="space-y-2 overflow-y-auto p-3 max-h-[calc(100vh-16rem)]">
            @foreach ($modules as $index => $module)
                @php
                    $isCurrentModule = $currentModule->id === $module->id;
                    $desktopCardClasses = $isCurrentModule
                        ? 'border-blue-500 bg-linear-to-br from-blue-600 to-cyan-500 text-white shadow-lg shadow-blue-500/20'
                        : ($module->status === 'locked'
                            ? 'cursor-not-allowed border-zinc-200 bg-zinc-50 opacity-50 dark:border-zinc-800 dark:bg-zinc-950/40'
                            : 'border-zinc-200 bg-white hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50/70 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-blue-700 dark:hover:bg-zinc-800');
                    $desktopBadgeClasses = $isCurrentModule
                        ? 'bg-white/18 text-white ring-1 ring-white/30'
                        : ($module->status === 'completed'
                            ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300'
                            : ($module->status === 'locked'
                                ? 'bg-zinc-200 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400'
                                : 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'));
                    $desktopMetaClasses = $isCurrentModule ? 'text-white/80' : 'text-zinc-500 dark:text-zinc-400';
                    $desktopMetaBadgeClasses = $isCurrentModule ? 'border-white/20 bg-white/10' : 'border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-950/50';
                @endphp

                <button
                    wire:key="desktop-module-{{ $module->id }}"
                    wire:click="selectModule({{ $module->id }})"
                    @disabled($module->status === 'locked')
                    class="group flex w-full items-start gap-3 rounded-2xl border p-4 text-left transition-all {{ $desktopCardClasses }}"
                >
                    <div class="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-sm font-semibold {{ $desktopBadgeClasses }}">
                        @if ($module->status === 'completed')
                            <flux:icon.check class="h-4 w-4" />
                        @elseif ($module->status === 'locked')
                            <flux:icon.lock-closed class="h-4 w-4" />
                        @else
                            {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold">{{ $module->title }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs {{ $desktopMetaClasses }}">
                            <span class="inline-flex items-center gap-1 rounded-full border px-2 py-1 {{ $desktopMetaBadgeClasses }}">
                                <flux:icon.clock class="h-3 w-3" />
                                @php
                                    $totalSeconds = (int) $module->duration;
                                    $minutes = intdiv($totalSeconds, 60);
                                    $seconds = $totalSeconds % 60;
                                    echo $minutes > 0 ? "{$minutes} min {$seconds} sec" : "{$seconds} sec";
                                @endphp
                            </span>
                            <!-- <span class="inline-flex items-center gap-1 rounded-full border px-2 py-1 {{ $desktopMetaBadgeClasses }}">
                                <flux:icon.play class="h-3 w-3" />
                                {{ $module->watchRequirementPercent }}% watch
                            </span> -->
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    </aside>

    <div class="flex min-h-0 flex-1 flex-col gap-4">
        <div class="flex shrink-0 items-start justify-between gap-3">
            <div class="flex items-center gap-2">
                @if (! $sidebarOpen)
                    <flux:button variant="outline" class="hidden lg:flex !px-2" wire:click="toggleSidebar">
                        <flux:icon.bars-3 class="h-4 w-4" />
                    </flux:button>
                @endif

                <flux:button variant="outline" class="lg:hidden !px-2" wire:click="toggleMobileDrawer">
                    <flux:icon.bars-3 class="h-4 w-4" />
                </flux:button>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">{{ $course->title }}</p>
                    <h2 class="mt-1 text-xl font-semibold text-zinc-950 dark:text-white">{{ $currentModule->title }}</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Lesson {{ $modules->search(fn ($module) => $module->id === $currentModule->id) + 1 }} of {{ $totalModules }}
                    </p>
                </div>
            </div>

            <div class="hidden flex-wrap items-center gap-2 md:flex">
                <flux:badge variant="outline">
                    <flux:icon.clock class="mr-1 h-3 w-3" />
                    {{ $currentModule->duration }}
                </flux:badge>
                <flux:badge variant="outline">
                    <flux:icon.lock-closed class="mr-1 h-3 w-3" />
                    {{ $currentModule->seekForwardEnabled ? 'Free seek' : 'Seek lock active' }}
                </flux:badge>
            </div>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto pb-4">
            @if (! $showQuiz || $activeQuizContext === 'timestamped')
                @php
                    $playerConfig = [
                        'moduleId' => $currentModule->id,
                        'playerElementId' => 'course-video-player-'.$currentModule->id,
                        'youtubeId' => $currentModule->videoId,
                        'title' => $currentModule->title,
                        'isCompleted' => $currentModule->status === 'completed',
                        'isVideoLesson' => $currentModule->isVideoLesson,
                        'startTimeSeconds' => $currentModule->startTimeSeconds,
                        'endTimeSeconds' => $currentModule->endTimeSeconds,
                        'seekForwardEnabled' => $currentModule->seekForwardEnabled,
                        'allowSpeedChange' => $currentModule->allowSpeedChange,
                        'allowCaptions' => $currentModule->allowCaptions,
                        'rewindSeconds' => $currentModule->rewindSeconds,
                        'forwardSeconds' => $currentModule->forwardSeconds,
                        'watchRequirementPercent' => $currentModule->watchRequirementPercent,
                        'timestampedQuizzes' => $currentModule->timestampedQuizSummaries,
                        'completedTimestampedQuizIds' => $completedTimestampedQuizIds,
                    ];
                @endphp

                <div
                    wire:key="lesson-player-{{ $currentModule->id }}"
                    data-player-config='@json($playerConfig)'
                    x-data="courseVideoPlayer(JSON.parse($el.dataset.playerConfig))"
                    x-init="init(); return () => destroy()"
                    x-on:timestamped-quiz-resolved.window="handleTimestampedQuizResolved($event.detail)"
                    class="space-y-4"
                >
                    <div class="overflow-hidden rounded-[2rem] border border-zinc-200 bg-white shadow-[0_24px_80px_-40px_rgba(15,23,42,0.55)] dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="border-b border-zinc-200 bg-linear-to-r from-zinc-950 via-zinc-900 to-slate-900 px-5 py-4 text-white dark:border-zinc-800">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/60">Controlled Playback</p>
                                    <h3 class="mt-1 text-lg font-semibold" x-text="title"></h3>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/8 px-3 py-1.5 text-white/80">
                                        <span class="h-2 w-2 rounded-full" :class="watchUnlocked || isCompleted ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                                        <span x-text="watchUnlocked || isCompleted ? 'Quiz unlocked' : 'Watch gate active'"></span>
                                    </span>
                                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/8 px-3 py-1.5 text-white/80">
                                        <flux:icon.lock-closed class="h-3 w-3" />
                                        <span x-text="seekForwardEnabled ? 'Free seek' : 'Forward seek blocked'"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-0 lg:grid-cols-[minmax(0,1fr)_20rem]">
                            <div class="space-y-4 p-4 sm:p-5">
                                <template x-if="isVideoLesson">
                                    <div class="space-y-4">
                                        <div
                                            x-ref="playerShell"
                                            class="relative overflow-hidden rounded-[1.5rem] border border-zinc-200 bg-black shadow-2xl dark:border-zinc-800"
                                            tabindex="0"
                                        >
                                            <div class="absolute inset-0 bg-radial-[circle_at_top] from-cyan-500/15 via-transparent to-transparent"></div>
                                            <div class="relative aspect-video overflow-hidden">
                                                <div x-ref="playerHost" :id="playerElementId" class="absolute inset-0"></div>
                                                <div class="absolute inset-0 z-10 cursor-pointer bg-linear-to-t from-black/65 via-black/15 to-black/10" @click="handlePlayPause"></div>

                                                <div class="pointer-events-none absolute inset-x-0 top-0 z-20 flex items-start justify-between p-4">
                                                    <div class="rounded-full border border-white/10 bg-black/40 px-3 py-1 text-xs font-medium text-white/80 backdrop-blur">
                                                        <span x-text="formatTime(currentTime)"></span>
                                                        <span class="mx-1 text-white/40">/</span>
                                                        <span x-text="formatTime(displayDuration)"></span>
                                                    </div>
                                                    <div class="rounded-full border border-white/10 bg-black/40 px-3 py-1 text-xs font-medium text-white/80 backdrop-blur">
                                                        Completed <span x-text="watchPercent"></span>% 
                                                    </div>
                                                </div>

                                                <div class="pointer-events-none absolute inset-0 z-20 flex items-center justify-center">
                                                    <button
                                                        type="button"
                                                        class="pointer-events-auto inline-flex h-18 w-18 items-center justify-center rounded-full border border-white/20 bg-white/15 text-white shadow-2xl backdrop-blur transition hover:scale-105 hover:bg-white/25"
                                                        @click.stop="handlePlayPause"
                                                    >
                                                        <flux:icon.pause x-show="playing" class="h-8 w-8" />
                                                        <flux:icon.play x-show="! playing" class="h-8 w-8 translate-x-0.5" />
                                                    </button>
                                                </div>

                                                <div
                                                    x-show="blockedMessage"
                                                    x-transition.opacity
                                                    class="absolute inset-x-6 bottom-6 z-30 rounded-2xl border border-amber-400/30 bg-amber-500/15 px-4 py-3 text-sm text-amber-50 shadow-lg backdrop-blur"
                                                    x-text="blockedMessage"
                                                ></div>
                                            </div>
                                        </div>

                                        <div class="rounded-[1.5rem] border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/60">
                                            <div class="mb-3 flex items-center justify-between gap-3">
                                                <div>
                                                    <p class="text-sm font-semibold text-zinc-950 dark:text-white">Scrub Guard</p>
                                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                        Forward seeking snaps back to the furthest watched point unless free seek is enabled.
                                                    </p>
                                                </div>
                                                <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="watchUnlocked || isCompleted ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'">
                                                    <span x-text="watchUnlocked || isCompleted ? 'Ready for quiz' : 'Watch required'"></span>
                                                </span>
                                            </div>

                                            <div class="space-y-3">
                                                <input
                                                    type="range"
                                                    min="0"
                                                    :max="sliderMax"
                                                    step="0.1"
                                                    :value="sliderValue"
                                                    class="h-2 w-full cursor-pointer appearance-none rounded-full bg-zinc-200 accent-blue-600 dark:bg-zinc-800"
                                                    @input="handleSeekInput($event)"
                                                    @change="handleSeekChange($event)"
                                                >

                                                <div class="flex flex-wrap items-center justify-between gap-3">
                                                    <div class="flex items-center gap-2">
                                                        <flux:button size="sm" variant="outline" @click="handleRewind">
                                                            <span class="inline-flex items-center gap-2">
                                                                <flux:icon.backward class="h-4 w-4" />
                                                                -<span x-text="rewindSeconds"></span>s
                                                            </span>
                                                        </flux:button>

                                                        <flux:button size="sm" variant="primary" @click="handlePlayPause">
                                                            <span class="inline-flex items-center gap-2">
                                                                <flux:icon.pause x-show="playing" class="h-4 w-4" />
                                                                <flux:icon.play x-show="! playing" class="h-4 w-4" />
                                                                <span x-text="playing ? 'Pause' : 'Play'"></span>
                                                            </span>
                                                        </flux:button>

                                                        <template x-if="seekForwardEnabled">
                                                            <flux:button size="sm" variant="outline" @click="handleForward">
                                                                <span class="inline-flex items-center gap-2">
                                                                    +<span x-text="forwardSeconds"></span>s
                                                                    <flux:icon.forward class="h-4 w-4" />
                                                                </span>
                                                            </flux:button>
                                                        </template>
                                                    </div>

                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <label class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-2 text-xs text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                                                            <flux:icon.speaker-wave class="h-4 w-4" />
                                                            <input type="range" min="0" max="100" step="1" :value="volume" class="w-20 accent-blue-600" @input="handleVolumeChange($event)">
                                                        </label>

                                                         <template x-if="allowSpeedChange">
                                                              <label class="inline-flex items-center gap-1 rounded-full border border-zinc-200 bg-white px-2 py-2 text-xs text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                                                                 <flux:icon.bolt class="h-4 w-4" />
                                                                 <div class="relative">
                                                                      <select class="bg-transparent outline-none appearance-none pr-2" x-model="playbackRate" @change="handlePlaybackRateChange($event)">
                                                                          <template x-for="rate in playbackRates" :key="rate">
                                                                              <option :value="rate" x-text="rate + 'x'"></option>
                                                                          </template>
                                                                      </select>
                                                                     <flux:icon.chevron-down class="absolute right-1 top-1/2 -translate-y-1/2 h-3 w-3 opacity-60 pointer-events-none" />
                                                                 </div>
                                                             </label>
                                                         </template>

                                                         <template x-if="allowCaptions">
                                                             <flux:button size="sm" variant="outline" @click="toggleCaptions" :disabled="!captionsAvailable" :class="{ 'opacity-50 cursor-not-allowed': !captionsAvailable }">
                                                                  <span class="inline-flex items-center gap-2">
                                                                     <flux:icon.chat-bubble-bottom-center-text class="h-4 w-4" />
                                                                      <span x-text="captionsAvailable ? (captionsEnabled ? 'Captions on' : 'Captions off') : 'No captions'"></span>
                                                                 </span>
                                                             </flux:button>
                                                         </template>

                                                        <flux:button size="sm" variant="outline" @click="toggleFullscreen">
                                                            <flux:icon.arrows-pointing-out class="h-4 w-4" />
                                                        </flux:button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="! isVideoLesson">
                                    <div class="rounded-[1.5rem] border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-800 dark:bg-zinc-950/60">
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                                <flux:icon.document-text class="h-6 w-6" />
                                            </div>
                                            <div class="space-y-2">
                                                <p class="text-base font-semibold text-zinc-950 dark:text-white">Reading lesson</p>
                                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                                    This lesson does not use controlled video playback. The quiz can be opened immediately after reviewing the lesson content.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div class="rounded-[1.5rem] border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                                    <div class="mb-3 flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-base font-semibold text-zinc-950 dark:text-white">Lesson Notes</p>
                                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Use the content below before attempting the quiz.</p>
                                        </div>
                                        <flux:badge variant="outline">
                                            {{ $currentModule->type->label() }}
                                        </flux:badge>
                                    </div>

                                    <div class="prose max-w-none text-zinc-700 dark:prose-invert dark:text-zinc-300">
                                        <p>{{ $currentModule->body }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-zinc-200 bg-zinc-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/70 lg:border-t-0 lg:border-l">
                                <div class="space-y-4">
                                    <div class="rounded-[1.5rem] border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                                        <button
                                            type="button"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-semibold transition"
                                            :class="(watchUnlocked || isCompleted || ! isVideoLesson) ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/25 hover:bg-blue-500' : 'cursor-not-allowed bg-zinc-200 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400'"
                                            @click="startQuiz"
                                        >
                                            <flux:icon.academic-cap class="h-4 w-4" />
                                            <span x-text="(watchUnlocked || isCompleted || ! isVideoLesson) ? @js($currentModule->mainQuizButtonLabel) : 'Watch More to Unlock Quiz'"></span>
                                        </button>
                                    </div>

                                    <div class="rounded-[1.5rem] border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-sm font-semibold text-zinc-950 dark:text-white">Comments</p>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $comments->count() }} {{ \Illuminate\Support\Str::plural('comment', $comments->count()) }}</span>
                                        </div>

                                        <div class="mt-4 space-y-4">
                                            <div class="space-y-3">
                                                <flux:textarea wire:model="newComment" placeholder="Add a comment about this lesson..." rows="4" />
                                                <flux:error name="newComment" />
                                                <div class="flex justify-end">
                                                    <flux:button wire:click="postComment" size="sm">Post Comment</flux:button>
                                                </div>
                                            </div>

                                            <div class="space-y-3">
                                                @forelse ($comments as $comment)
                                                    @include('livewire.faculty.partials.comment-thread', ['comment' => $comment, 'depth' => 0])
                                                @empty
                                                    <div class="rounded-2xl border border-dashed border-zinc-200 bg-zinc-50 p-4 text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-950/60 dark:text-zinc-400">
                                                        No comments yet. Start the discussion for this lesson.
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($showQuiz && $activeQuizContext === 'timestamped' && $activeQuiz)
                        <div class="fixed inset-0 z-60 flex items-center justify-center bg-zinc-950/80 px-4 backdrop-blur-sm">
                            <div class="w-full max-w-3xl rounded-[2rem] border border-zinc-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-800">
                                    <div>
                                        <h3 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ $activeQuizTitle }}</h3>
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                            Pass score: {{ $activeQuizPassPercentage }}%
                                        </p>
                                    </div>
                                    <flux:badge variant="solid" color="blue">Timestamped</flux:badge>
                                </div>

                                <div class="p-6">
                                    @include('livewire.faculty.partials.quiz-panel', ['isTimestampedQuiz' => true])
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($showFeedback)
                        <div class="rounded-[1.5rem] border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/40 dark:bg-blue-900/20">
                            <div class="flex items-center gap-2 text-sm font-semibold text-blue-700 dark:text-blue-300">
                                <flux:icon.chat-bubble-left-ellipsis class="h-4 w-4" />
                                Quick Reflection
                            </div>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">How would you apply the lesson in a real classroom or workshop?</p>
                            <textarea class="mt-3 w-full rounded-2xl border border-blue-200 bg-white p-3 text-sm outline-none ring-0 dark:border-blue-900/40 dark:bg-zinc-900" rows="4" placeholder="Write a concrete application of the idea here..."></textarea>
                            <div class="mt-3 flex justify-end">
                                <flux:button size="sm" wire:click="toggleFeedback">Close Reflection</flux:button>
                            </div>
                        </div>
                    @endif

                    @if ($showPuzzle)
                        <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/40 dark:bg-amber-900/20">
                            <div class="flex items-center gap-2 text-sm font-semibold text-amber-700 dark:text-amber-300">
                                <flux:icon.puzzle-piece class="h-4 w-4" />
                                Sequence Puzzle
                            </div>
                            <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">Arrange the teaching flow in the right order: Plan, Implement, Assess, Evaluate.</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach (['Plan', 'Implement', 'Assess', 'Evaluate'] as $step)
                                    <flux:badge variant="outline" class="px-3 py-1">{{ $step }}</flux:badge>
                                @endforeach
                            </div>
                            <div class="mt-3 flex justify-end">
                                <flux:button size="sm" variant="outline" wire:click="togglePuzzle">Close Puzzle</flux:button>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="rounded-[2rem] border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-800">
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ $activeQuizTitle }}</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Pass with at least {{ $activeQuizPassPercentage }}% to complete this lesson.</p>
                        </div>
                        <flux:badge variant="solid" color="zinc">Pass: {{ $activeQuizPassPercentage }}%</flux:badge>
                    </div>

                    <div class="p-6">
                        @include('livewire.faculty.partials.quiz-panel', ['isTimestampedQuiz' => false])
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if ($mobileDrawerOpen)
        <div class="fixed inset-0 z-40 bg-zinc-950/70 backdrop-blur-sm lg:hidden" wire:click="toggleMobileDrawer"></div>

        <div class="fixed inset-x-0 bottom-0 z-50 max-h-[80vh] rounded-t-[2rem] border-t border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 lg:hidden">
            <div class="border-b border-zinc-200 p-4 dark:border-zinc-800">
                <div class="mx-auto mb-4 h-1.5 w-12 rounded-full bg-zinc-300 dark:bg-zinc-700"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">Lesson Queue</p>
                        <h3 class="mt-1 text-lg font-semibold text-zinc-950 dark:text-white">Course Modules</h3>
                    </div>
                    <flux:button variant="ghost" size="sm" class="!px-2" wire:click="toggleMobileDrawer">
                        <flux:icon.chevron-down class="h-5 w-5" />
                    </flux:button>
                </div>
            </div>

            <div class="space-y-2 overflow-y-auto p-3">
                @foreach ($modules as $index => $module)
                    @php
                        $isCurrentMobileModule = $currentModule->id === $module->id;
                        $mobileCardClasses = $isCurrentMobileModule
                            ? 'border-blue-500 bg-blue-600 text-white'
                            : ($module->status === 'locked'
                                ? 'cursor-not-allowed border-zinc-200 bg-zinc-50 opacity-50 dark:border-zinc-800 dark:bg-zinc-950/40'
                                : 'border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900');
                        $mobileBadgeClasses = $isCurrentMobileModule
                            ? 'bg-white/15'
                            : ($module->status === 'completed'
                                ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300'
                                : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300');
                        $mobileMetaClasses = $isCurrentMobileModule ? 'text-white/80' : 'text-zinc-500 dark:text-zinc-400';
                    @endphp

                    <button
                        wire:key="mobile-module-{{ $module->id }}"
                        wire:click="selectModule({{ $module->id }})"
                        @disabled($module->status === 'locked')
                        class="flex w-full items-center gap-3 rounded-2xl border p-4 text-left transition {{ $mobileCardClasses }}"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $mobileBadgeClasses }}">
                            @if ($module->status === 'completed')
                                <flux:icon.check class="h-4 w-4" />
                            @elseif ($module->status === 'locked')
                                <flux:icon.lock-closed class="h-4 w-4" />
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold">{{ $module->title }}</p>
                            <p class="mt-1 text-xs {{ $mobileMetaClasses }}">{{ $module->duration }} /p>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>

