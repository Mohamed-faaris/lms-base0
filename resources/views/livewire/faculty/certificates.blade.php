<div class="space-y-8 pb-10">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Certificates & History</h1>
            <p class="text-zinc-500 dark:text-zinc-400 mt-2 text-lg">Your learning achievements, milestones, and timeline.</p>
        </div>
    </div>

    <div>
        {{-- Tabs Navigation --}}
        <div class="flex items-center gap-6 border-b border-zinc-200 dark:border-zinc-800 mb-8 px-2">
            <button 
                wire:click="setActiveTab('certificates')" 
                class="flex items-center gap-2 pb-4 px-2 text-sm font-semibold transition-all duration-300 border-b-2 relative {{ $activeTab === 'certificates' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200' }}"
            >
                <flux:icon.academic-cap class="h-5 w-5 {{ $activeTab === 'certificates' ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 dark:text-zinc-500' }}" />
                Certificates
                @if($activeTab === 'certificates')
                    <div class="absolute bottom-0 left-0 w-full h-[2px] bg-blue-600 dark:bg-blue-400 shadow-[0_-2px_10px_rgba(37,99,235,0.5)]"></div>
                @endif
            </button>
            <button 
                wire:click="setActiveTab('history')" 
                class="flex items-center gap-2 pb-4 px-2 text-sm font-semibold transition-all duration-300 border-b-2 relative {{ $activeTab === 'history' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200' }}"
            >
                <flux:icon.clock class="h-5 w-5 {{ $activeTab === 'history' ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 dark:text-zinc-500' }}" />
                Progress History
                @if($activeTab === 'history')
                    <div class="absolute bottom-0 left-0 w-full h-[2px] bg-blue-600 dark:bg-blue-400 shadow-[0_-2px_10px_rgba(37,99,235,0.5)]"></div>
                @endif
            </button>
        </div>

        {{-- Certificates Tab Content --}}
        @if($activeTab === 'certificates')
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse($completedCourses as $course)
                    <div class="group flex flex-col rounded-3xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm hover:shadow-xl hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-300 hover:-translate-y-1 relative">
                        
                        {{-- Certificate Thumbnail --}}
                        <div class="aspect-[4/3] bg-gradient-to-br from-slate-100 to-blue-50 dark:from-slate-800/80 dark:to-blue-900/20 p-6 flex flex-col items-center justify-center text-center relative overflow-hidden">
                            {{-- Decorative Background pattern --}}
                            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:16px_16px]"></div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-400/20 rounded-full blur-3xl transition-transform group-hover:scale-150 duration-500"></div>

                            <div class="absolute top-4 right-4 z-10">
                                <flux:badge color="emerald" class="shadow-sm font-bold tracking-wide">
                                    {{ $course['score'] }}%
                                </flux:badge>
                            </div>

                            <div class="h-20 w-20 rounded-2xl bg-white/60 dark:bg-zinc-900/60 backdrop-blur-sm border border-white/50 dark:border-zinc-700/50 flex items-center justify-center mb-4 shadow-sm relative z-10 transition-transform group-hover:scale-110 duration-300">
                                <flux:icon.academic-cap class="h-10 w-10 text-blue-600 dark:text-blue-400" />
                            </div>
                            
                            <h3 class="font-bold text-zinc-900 dark:text-zinc-100 text-lg leading-tight relative z-10 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors px-4">
                                {{ $course['name'] }}
                            </h3>
                            <p class="text-sm font-medium text-blue-600/80 dark:text-blue-400/80 mt-2 tracking-wide uppercase relative z-10">
                                Certificate of Completion
                            </p>
                            
                            <div class="absolute bottom-4 left-4 right-4 flex justify-between items-center text-xs font-medium text-zinc-500 dark:text-zinc-400 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md py-2 px-3 rounded-lg border border-white/20 dark:border-zinc-700/50 z-10">
                                <span class="font-mono">{{ $course['certificateId'] }}</span>
                                <span class="text-blue-600 dark:text-blue-400 font-bold">KR Learn</span>
                            </div>
                        </div>

                        {{-- Details & Actions --}}
                        <div class="p-6 flex-1 flex flex-col bg-gradient-to-b from-transparent to-zinc-50/50 dark:to-zinc-900/50">
                            <div class="flex items-center justify-between text-sm font-medium mb-6 px-1">
                                <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800/50 px-3 py-1.5 rounded-md">
                                    <flux:icon.calendar class="h-4 w-4 text-zinc-400" />
                                    {{ $course['completedDate'] }}
                                </div>
                                <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800/50 px-3 py-1.5 rounded-md">
                                    <flux:icon.clock class="h-4 w-4 text-zinc-400" />
                                    {{ $course['duration'] }}
                                </div>
                            </div>
                            <div class="flex gap-3 mt-auto">
                                <flux:button variant="outline" class="flex-1 font-semibold group-hover:border-blue-200 dark:group-hover:border-blue-800 transition-colors" wire:click="viewCertificate({{ $course['id'] }})">
                                    <flux:icon.eye class="mr-2 h-4 w-4 text-zinc-400 group-hover:text-blue-500" />
                                    View
                                </flux:button>
                                <flux:button variant="primary" class="flex-1 font-semibold shadow-lg shadow-blue-500/20 group-hover:shadow-blue-500/40" wire:click="viewCertificate({{ $course['id'] }})">
                                    <flux:icon.arrow-down-tray class="mr-2 h-4 w-4" />
                                    Download
                                </flux:button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 px-6 flex flex-col items-center justify-center text-center bg-white dark:bg-zinc-900 rounded-3xl border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-sm">
                        <div class="h-28 w-28 bg-zinc-50 dark:bg-zinc-800/50 rounded-full shadow-inner border border-zinc-100 dark:border-zinc-700/50 flex items-center justify-center mb-6 relative">
                            <div class="absolute inset-0 bg-blue-400/10 rounded-full blur-xl animate-pulse"></div>
                            <flux:icon.academic-cap class="h-14 w-14 text-zinc-300 dark:text-zinc-600 relative z-10" />
                        </div>
                        <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-3">No certificates yet</h3>
                        <p class="text-zinc-500 dark:text-zinc-400 max-w-md mx-auto text-lg leading-relaxed">
                            Complete your enrolled courses to earn certificates. They will be displayed here for you to download and share.
                        </p>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- History Tab Content --}}
        @if($activeTab === 'history')
            <div class="rounded-3xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-8 shadow-sm">
                <div class="space-y-0 relative before:absolute before:inset-0 before:ml-[1.1rem] before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-zinc-200 dark:before:via-zinc-800 before:to-transparent">
                    @foreach($progressHistory as $index => $item)
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active py-5">
                            
                            {{-- Timeline Icon --}}
                            <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white dark:border-zinc-900 bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 transition-transform duration-300 group-hover:scale-110">
                                @if(str_contains($item['action'], 'Earned'))
                                    <flux:icon.trophy class="h-4 w-4" />
                                @elseif(str_contains($item['action'], 'Quiz'))
                                    <flux:icon.clipboard-document-check class="h-4 w-4" />
                                @else
                                    <flux:icon.check-circle class="h-4 w-4" />
                                @endif
                            </div>
                            
                            {{-- Timeline Content Card --}}
                            <div class="w-[calc(100%-3rem)] md:w-[calc(50%-2.5rem)] p-5 rounded-2xl border border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/20 shadow-sm transition-all duration-300 hover:shadow-md hover:bg-white dark:hover:bg-zinc-800 hover:border-blue-200 dark:hover:border-blue-900/50">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-2">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400 mb-1">{{ $item['date'] }}</p>
                                        <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-lg">{{ $item['action'] }}</h4>
                                    </div>
                                    <flux:badge color="amber" class="shrink-0 font-bold self-start">
                                        +{{ $item['xp'] }} XP
                                    </flux:badge>
                                </div>
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 flex items-center gap-2">
                                    <flux:icon.book-open class="h-4 w-4" />
                                    {{ $item['course'] }}
                                </p>
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
            <div class="w-[900px] max-w-full space-y-6">
                <div class="flex items-center justify-between pb-2">
                    <div>
                        <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Certificate Details</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Preview and download your certificate</p>
                    </div>
                </div>

                {{-- Editable Name --}}
                <div class="flex items-center gap-4 p-5 bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl border border-blue-100 dark:border-blue-900/50">
                    <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center shrink-0">
                        <flux:icon.user class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Recipient Name Display</label>
                        <div class="flex items-center gap-3 mt-1.5">
                            @if($isEditing)
                                <flux:input wire:model="recipientName" class="max-w-sm font-medium" />
                            @else
                                <span class="font-semibold text-lg text-zinc-900 dark:text-zinc-100">{{ $recipientName }}</span>
                            @endif
                            <flux:button variant="ghost" size="sm" class="font-semibold" wire:click="toggleEditName">
                                @if($isEditing)
                                    <flux:icon.check class="h-4 w-4 mr-1.5" /> Done
                                @else
                                    <flux:icon.pencil-square class="h-4 w-4 mr-1.5" /> Edit Name
                                @endif
                            </flux:button>
                        </div>
                    </div>
                </div>

                {{-- Certificate Design (High Fidelity) --}}
                <div class="aspect-[1.414] bg-white dark:bg-zinc-950 border-[12px] border-double border-slate-200 dark:border-slate-800 rounded-lg p-1 relative overflow-hidden shadow-2xl">
                    <div class="absolute inset-0 border-[3px] border-solid border-amber-400/60 dark:border-amber-500/40 m-3 pointer-events-none"></div>
                    <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#000_1px,transparent_1px)] dark:bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
                    
                    <div class="w-full h-full bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800 flex flex-col items-center justify-between text-center p-8 md:p-12 relative z-10 border border-slate-200 dark:border-slate-800">
                        
                        {{-- Corner Ornaments --}}
                        <div class="absolute top-0 left-0 w-24 h-24 border-t-2 border-l-2 border-amber-400/50 rounded-tl-3xl m-6"></div>
                        <div class="absolute top-0 right-0 w-24 h-24 border-t-2 border-r-2 border-amber-400/50 rounded-tr-3xl m-6"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 border-b-2 border-l-2 border-amber-400/50 rounded-bl-3xl m-6"></div>
                        <div class="absolute bottom-0 right-0 w-24 h-24 border-b-2 border-r-2 border-amber-400/50 rounded-br-3xl m-6"></div>

                        {{-- Header --}}
                        <div class="space-y-4 pt-4">
                            <div class="flex items-center justify-center gap-3">
                                <div class="h-12 w-12 rounded-full bg-blue-600 flex items-center justify-center">
                                    <flux:icon.academic-cap class="h-7 w-7 text-white" />
                                </div>
                                <span class="text-2xl font-black tracking-widest text-blue-900 dark:text-blue-500 uppercase">KR Learn</span>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-slate-100 tracking-[0.2em] mt-6" style="font-family: serif;">
                                CERTIFICATE
                            </h2>
                            <p class="text-sm font-bold tracking-[0.3em] text-slate-500 dark:text-slate-400 uppercase">Of Completion</p>
                        </div>

                        {{-- Body --}}
                        <div class="space-y-6 w-full max-w-2xl mx-auto py-8">
                            <p class="text-slate-500 dark:text-slate-400 italic text-lg" style="font-family: serif;">This is to certify that</p>
                            
                            <h3 class="text-3xl md:text-5xl font-bold text-slate-900 dark:text-slate-50 border-b border-slate-300 dark:border-slate-700 pb-4 px-8 inline-block w-[80%] text-center" style="font-family: serif;">
                                {{ $recipientName }}
                            </h3>
                            
                            <p class="text-slate-500 dark:text-slate-400 italic text-lg" style="font-family: serif;">has successfully completed the course</p>
                            
                            <h4 class="text-2xl md:text-3xl font-black text-blue-900 dark:text-blue-400 tracking-wide uppercase px-4 leading-tight">
                                {{ $selectedCourse['name'] }}
                            </h4>
                            
                            <div class="inline-block mt-4 px-6 py-2 bg-slate-100 dark:bg-slate-800 rounded-full border border-slate-200 dark:border-slate-700">
                                <span class="font-bold text-slate-700 dark:text-slate-300">Completion Score: <span class="text-emerald-600 dark:text-emerald-400">{{ $selectedCourse['score'] }}%</span></span>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="w-full flex justify-between items-end text-sm text-slate-600 dark:text-slate-400 px-8 pb-4">
                            <div class="text-left space-y-2 w-48">
                                <p class="font-bold text-slate-900 dark:text-slate-100 text-lg border-b border-slate-400 dark:border-slate-600 pb-1">{{ $selectedCourse['completedDate'] }}</p>
                                <p class="text-xs uppercase tracking-wider font-semibold">Date of Issuance</p>
                            </div>
                            
                            <div class="text-center w-32 relative">
                                <div class="absolute -top-16 left-1/2 -translate-x-1/2 opacity-20 pointer-events-none">
                                    <flux:icon.check-badge class="w-24 h-24 text-blue-900 dark:text-blue-500" />
                                </div>
                            </div>

                            <div class="text-right space-y-2 w-48">
                                <p class="font-mono text-xs text-slate-900 dark:text-slate-100 border-b border-slate-400 dark:border-slate-600 pb-1">{{ $selectedCourse['certificateId'] }}</p>
                                <p class="text-xs uppercase tracking-wider font-semibold">Certificate ID</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <flux:button variant="outline" size="lg" class="flex-1 font-semibold border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800" onclick="alert('Downloading Image...')">
                        <flux:icon.photo class="mr-2.5 h-5 w-5" />
                        Download Image (PNG)
                    </flux:button>
                    <flux:button variant="primary" size="lg" class="flex-1 font-semibold shadow-lg shadow-blue-500/20" onclick="alert('Downloading PDF...')">
                        <flux:icon.document-text class="mr-2.5 h-5 w-5" />
                        Download PDF Document
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
