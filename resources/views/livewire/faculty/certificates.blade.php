<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Certificates & History</h2>
        <p class="text-zinc-500 dark:text-zinc-400">Your learning achievements and progress</p>
    </div>

    <div>
        {{-- Tabs Navigation --}}
        <div class="flex items-center gap-4 border-b border-zinc-200 dark:border-zinc-700 mb-6">
            <button 
                wire:click="setActiveTab('certificates')" 
                class="flex items-center gap-2 pb-3 px-1 text-sm font-medium transition-colors border-b-2 {{ $activeTab === 'certificates' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
            >
                <flux:icon.academic-cap class="h-4 w-4" />
                Certificates
            </button>
            <button 
                wire:click="setActiveTab('history')" 
                class="flex items-center gap-2 pb-3 px-1 text-sm font-medium transition-colors border-b-2 {{ $activeTab === 'history' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
            >
                <flux:icon.clock class="h-4 w-4" />
                Progress History
            </button>
        </div>

        {{-- Certificates Tab Content --}}
        @if($activeTab === 'certificates')
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse($completedCourses as $course)
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden">
                        {{-- Thumbnail --}}
                        <div class="aspect-[4/3] bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/30 dark:to-blue-800/10 p-6 flex flex-col items-center justify-center text-center relative">
                            <div class="absolute top-3 right-3">
                                <flux:badge color="emerald" variant="subtle">{{ $course['score'] }}%</flux:badge>
                            </div>
                            <div class="h-16 w-16 rounded-full bg-blue-200/50 dark:bg-blue-800/50 flex items-center justify-center mb-3">
                                <flux:icon.academic-cap class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                            </div>
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ $course['name'] }}</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Certificate of Completion</p>
                            <div class="absolute bottom-3 left-3 right-3 flex justify-between items-center text-[10px] text-zinc-500 dark:text-zinc-400">
                                <span>{{ $course['certificateId'] }}</span>
                                <span>KR Learn</span>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="flex items-center justify-between text-sm mb-4">
                                <div class="flex items-center gap-1 text-zinc-500 dark:text-zinc-400">
                                    <flux:icon.calendar class="h-3 w-3" />
                                    {{ $course['completedDate'] }}
                                </div>
                                <div class="flex items-center gap-1 text-zinc-500 dark:text-zinc-400">
                                    <flux:icon.clock class="h-3 w-3" />
                                    {{ $course['duration'] }}
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <flux:button variant="outline" size="sm" class="flex-1" wire:click="viewCertificate({{ $course['id'] }})">
                                    <flux:icon.arrow-top-right-on-square class="mr-2 h-3 w-3" />
                                    View
                                </flux:button>
                                <flux:button size="sm" class="flex-1" wire:click="viewCertificate({{ $course['id'] }})">
                                    <flux:icon.arrow-down-tray class="mr-2 h-3 w-3" />
                                    Download
                                </flux:button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <flux:icon.academic-cap class="h-12 w-12 text-zinc-400 dark:text-zinc-500 mx-auto mb-4" />
                        <h3 class="font-medium text-zinc-900 dark:text-zinc-100">No certificates yet</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Complete courses to earn certificates</p>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- History Tab Content --}}
        @if($activeTab === 'history')
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <div class="space-y-0">
                    @foreach($progressHistory as $index => $item)
                        <div class="flex items-start gap-4 py-4 {{ !$loop->last ? 'border-b border-zinc-200 dark:border-zinc-700' : '' }}">
                            {{-- Timeline Dot --}}
                            <div class="flex flex-col items-center">
                                <div class="h-8 w-8 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                                    <flux:icon.check-circle class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                </div>
                                @if(!$loop->last)
                                    <div class="w-0.5 flex-1 bg-zinc-200 dark:bg-zinc-700 mt-2 min-h-[2rem]"></div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item['action'] }}</p>
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $item['course'] }}</p>
                                    </div>
                                    <flux:badge color="zinc" class="shrink-0">
                                        +{{ $item['xp'] }} XP
                                    </flux:badge>
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ $item['date'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Certificate Modal --}}
    <flux:modal wire:model.live="selectedCourse">
        @if($selectedCourse)
            <div class="w-[800px] max-w-full space-y-6">
                <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Certificate Preview</h2>
                </div>

                {{-- Editable Name --}}
                <div class="flex items-center gap-4 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <div class="flex-1">
                        <label class="text-sm text-zinc-500 dark:text-zinc-400">Recipient Name</label>
                        <div class="flex items-center gap-2 mt-1">
                            @if($isEditing)
                                <flux:input wire:model="recipientName" class="max-w-xs" />
                            @else
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $recipientName }}</span>
                            @endif
                            <flux:button variant="ghost" size="sm" wire:click="toggleEditName">
                                <flux:icon.pencil-square class="h-4 w-4 mr-1" />
                                {{ $isEditing ? 'Done' : 'Edit' }}
                            </flux:button>
                        </div>
                    </div>
                </div>

                {{-- Certificate Design --}}
                <div class="aspect-[1.414] bg-white dark:bg-zinc-900 border-2 border-blue-200 dark:border-blue-800 rounded-lg p-6 md:p-8 flex flex-col items-center justify-between text-center relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-16 h-16 border-t-4 border-l-4 border-blue-300 dark:border-blue-700 rounded-tl-lg"></div>
                    <div class="absolute top-0 right-0 w-16 h-16 border-t-4 border-r-4 border-blue-300 dark:border-blue-700 rounded-tr-lg"></div>
                    <div class="absolute bottom-0 left-0 w-16 h-16 border-b-4 border-l-4 border-blue-300 dark:border-blue-700 rounded-bl-lg"></div>
                    <div class="absolute bottom-0 right-0 w-16 h-16 border-b-4 border-r-4 border-blue-300 dark:border-blue-700 rounded-br-lg"></div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-center gap-2">
                            <flux:icon.academic-cap class="h-8 w-8 text-blue-600 dark:text-blue-500" />
                            <span class="text-xl font-bold text-blue-600 dark:text-blue-500">KR Learn</span>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-bold text-zinc-900 dark:text-zinc-100 tracking-wide">
                            CERTIFICATE OF COMPLETION
                        </h2>
                    </div>

                    <div class="space-y-4 py-6">
                        <p class="text-zinc-500 dark:text-zinc-400">This is to certify that</p>
                        <h3 class="text-2xl md:text-3xl font-semibold text-zinc-900 dark:text-zinc-100 border-b-2 border-blue-300 dark:border-blue-800 pb-2 px-4 inline-block">
                            {{ $recipientName }}
                        </h3>
                        <p class="text-zinc-500 dark:text-zinc-400">has successfully completed</p>
                        <h4 class="text-xl md:text-2xl font-bold text-blue-600 dark:text-blue-500">{{ $selectedCourse['name'] }}</h4>
                        <flux:badge color="emerald" class="text-base px-4 py-1 mt-2">
                            Score: {{ $selectedCourse['score'] }}%
                        </flux:badge>
                    </div>

                    <div class="w-full flex justify-between items-end text-xs text-zinc-500 dark:text-zinc-400">
                        <div class="text-left">
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedCourse['completedDate'] }}</p>
                            <p>Date of Completion</p>
                        </div>
                        <div class="text-center">
                            <div class="h-12 w-32 border-b border-zinc-400 dark:border-zinc-600 mx-auto mb-1"></div>
                            <p>Authorized Signature</p>
                        </div>
                        <div class="text-right">
                            <p class="font-mono text-[10px] text-zinc-900 dark:text-zinc-100">{{ $selectedCourse['certificateId'] }}</p>
                            <p>Certificate ID</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <flux:button variant="outline" class="flex-1" onclick="alert('Downloading PNG...')">
                        <flux:icon.photo class="mr-2 h-4 w-4" />
                        Download as Image (PNG)
                    </flux:button>
                    <flux:button variant="primary" class="flex-1" onclick="alert('Downloading PDF...')">
                        <flux:icon.document-text class="mr-2 h-4 w-4" />
                        Download as PDF
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
