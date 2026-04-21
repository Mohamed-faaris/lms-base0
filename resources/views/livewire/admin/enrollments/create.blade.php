<div>
    <div class="mb-6 flex items-start gap-4">
        <flux:button
            variant="ghost"
            href="{{ route('admin.enrollments.index') }}"
            wire:navigate
            icon="arrow-left"
        />

        <div class="flex-1">
            <flux:heading level="1" size="xl">Create Enrollment Batch</flux:heading>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Build one batch, preview the exact learners, and enroll them into a course in a single run.
            </p>
        </div>
    </div>

    @if ($showSuccess)
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-950/30">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">Enrollment batch created</p>
                    <p class="text-sm text-emerald-700 dark:text-emerald-400">
                        Created {{ $createdCount }} enrollment{{ $createdCount === 1 ? '' : 's' }} and skipped {{ $skippedCount }} duplicate{{ $skippedCount === 1 ? '' : 's' }}.
                    </p>
                </div>
                <span class="rounded-md bg-white px-3 py-1 font-mono text-xs text-emerald-700 shadow-sm dark:bg-emerald-900/60 dark:text-emerald-200">
                    {{ $createdBatchId }}
                </span>
            </div>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(320px,0.9fr)]">
        <div class="space-y-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="mb-4">
                    <flux:heading level="2" size="lg">Course</flux:heading>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Select the course that will receive this enrollment batch.</p>
                </div>

                @if ($courseLocked && $selectedCourse)
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/30">
                        <p class="text-sm font-semibold text-blue-800 dark:text-blue-300">{{ $selectedCourse->title }}</p>
                        <p class="mt-1 text-sm text-blue-700 dark:text-blue-400">Course is locked because you came from the course page.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        <flux:input
                            wire:model.live="courseSearch"
                            icon="magnifying-glass"
                            placeholder="Search courses..."
                        />

                        <div class="max-h-64 space-y-2 overflow-y-auto rounded-xl border border-zinc-200 p-2 dark:border-zinc-700">
                            @forelse ($courseOptions as $courseOption)
                                <button
                                    type="button"
                                    wire:click="$set('courseId', {{ $courseOption->id }})"
                                    wire:key="course-{{ $courseOption->id }}"
                                    class="flex w-full items-start justify-between rounded-lg border px-4 py-3 text-left transition {{ $courseId === $courseOption->id ? 'border-blue-500 bg-blue-50 dark:border-blue-400 dark:bg-blue-950/30' : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-600 dark:hover:bg-zinc-900' }}"
                                >
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $courseOption->title }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $courseOption->slug }}</p>
                                    </div>
                                    @if ($courseId === $courseOption->id)
                                        <span class="rounded-full bg-blue-600 px-2 py-1 text-xs font-semibold text-white">Selected</span>
                                    @endif
                                </button>
                            @empty
                                <p class="px-3 py-4 text-sm text-zinc-500 dark:text-zinc-400">No courses matched your search.</p>
                            @endforelse
                        </div>
                    </div>
                @endif

                @error('courseId')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="mb-4">
                    <flux:heading level="2" size="lg">Who Do You Want To Enroll?</flux:heading>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Choose one hierarchy level. Only the relevant next step will appear.</p>
                </div>

                <div class="grid gap-3 md:grid-cols-2">
                    @php
                        $targetOptions = [
                            'all' => ['label' => 'All Learners', 'description' => 'Every faculty learner in the platform.'],
                            'college' => ['label' => 'College Selection', 'description' => 'Choose one or more colleges and optionally narrow by departments.'],
                            'user' => ['label' => 'Selected Users', 'description' => 'Search and select one or more specific learners.'],
                        ];
                    @endphp

                    @foreach ($targetOptions as $mode => $option)
                        <button
                            type="button"
                            wire:click="$set('targetMode', '{{ $mode }}')"
                            wire:key="target-mode-{{ $mode }}"
                            class="rounded-xl border p-4 text-left transition {{ $targetMode === $mode ? 'border-blue-500 bg-blue-50 dark:border-blue-400 dark:bg-blue-950/30' : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-600 dark:hover:bg-zinc-900' }}"
                        >
                            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $option['label'] }}</p>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $option['description'] }}</p>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="mb-4">
                    <flux:heading level="2" size="lg">Hierarchy Details</flux:heading>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Complete only the next step for the hierarchy level you selected.</p>
                </div>

                @if ($targetMode === 'all')
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900">
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">All learners will be included.</p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">This targets every eligible faculty user.</p>
                    </div>
                @elseif ($targetMode === 'college')
                    <div class="space-y-3">
                        <flux:label>Select Colleges</flux:label>
                        <div class="grid gap-3 sm:grid-cols-3">
                            @foreach ($collegeOptions as $college)
                                <button
                                    type="button"
                                    wire:click="toggleCollege('{{ $college->value }}')"
                                    wire:key="college-{{ $college->value }}"
                                    class="rounded-xl border px-4 py-3 text-left transition {{ in_array($college->value, $selectedColleges, true) ? 'border-blue-500 bg-blue-50 dark:border-blue-400 dark:bg-blue-950/30' : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-600 dark:hover:bg-zinc-900' }}"
                                >
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $college->label() }}</p>
                                </button>
                            @endforeach
                        </div>
                        @error('selectedColleges')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <div class="pt-2">
                            <flux:label>Optional Departments</flux:label>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($departmentOptions as $department)
                                    <button
                                        type="button"
                                        wire:click="toggleDepartment('{{ $department->value }}')"
                                        wire:key="college-department-{{ $department->value }}"
                                        class="rounded-full border px-4 py-2 text-sm transition {{ in_array($department->value, $selectedDepartments, true) ? 'border-blue-600 bg-blue-600 text-white' : 'border-zinc-300 text-zinc-700 hover:border-zinc-400 hover:bg-zinc-50 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-900' }}"
                                    >
                                        {{ $department->label() }}
                                    </button>
                                @endforeach
                            </div>
                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Leave departments empty to include every department inside the selected colleges.</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-3">
                        <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">
                            <div class="mb-3 flex flex-wrap gap-2">
                                @forelse ($selectedUserIds as $selectedUserId)
                                    @php
                                        $selectedUser = $userOptions->firstWhere('id', $selectedUserId) ?? \App\Models\User::find($selectedUserId);
                                    @endphp
                                    @if ($selectedUser)
                                        <button
                                            type="button"
                                            wire:click="toggleUser({{ $selectedUser->id }})"
                                            wire:key="selected-user-chip-{{ $selectedUser->id }}"
                                            class="inline-flex items-center gap-2 rounded-full border border-blue-600 bg-blue-600 px-3 py-1 text-xs font-medium text-white"
                                        >
                                            <span>{{ $selectedUser->name }}</span>
                                            <flux:icon.x-mark class="h-3.5 w-3.5" />
                                        </button>
                                    @endif
                                @empty
                                    <span class="text-sm text-zinc-500 dark:text-zinc-400">No learners selected yet.</span>
                                @endforelse
                            </div>

                            <flux:input
                                wire:model.live="userSearch"
                                icon="magnifying-glass"
                                placeholder="Search learners by name or email..."
                            />
                        </div>

                        <div class="max-h-64 space-y-2 overflow-y-auto rounded-xl border border-zinc-200 p-2 dark:border-zinc-700">
                            @forelse ($userOptions as $userOption)
                                <button
                                    type="button"
                                    wire:click="toggleUser({{ $userOption->id }})"
                                    wire:key="user-{{ $userOption->id }}"
                                    class="flex w-full items-start justify-between rounded-lg border px-4 py-3 text-left transition {{ in_array($userOption->id, $selectedUserIds, true) ? 'border-blue-500 bg-blue-50 dark:border-blue-400 dark:bg-blue-950/30' : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-600 dark:hover:bg-zinc-900' }}"
                                >
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $userOption->name }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $userOption->email }}</p>
                                    </div>
                                    @if (in_array($userOption->id, $selectedUserIds, true))
                                        <span class="rounded-full bg-blue-600 px-2 py-1 text-xs font-semibold text-white">Selected</span>
                                    @endif
                                </button>
                            @empty
                                <p class="px-3 py-4 text-sm text-zinc-500 dark:text-zinc-400">No learners matched your search.</p>
                            @endforelse
                        </div>
                        @error('selectedUserIds')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="mb-4">
                    <flux:heading level="2" size="lg">Deadline</flux:heading>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Set the number of days learners have to complete the course.</p>
                </div>

                <flux:input wire:model="deadlineDays" type="number" min="1" />

                @error('deadlineDays')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button wire:click="createBatch" variant="primary" class="w-full">
                    Enroll {{ $resolvedUserCount }} User{{ $resolvedUserCount === 1 ? '' : 's' }}
                </flux:button>
            </div>
        </div>

        <div class="xl:sticky xl:top-6 xl:self-start">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:heading level="2" size="lg">Preview</flux:heading>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Review the batch before you create it.</p>

                <div class="mt-5 space-y-4">
                    <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-900">
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Course</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $selectedCourse?->title ?? 'Choose a course' }}</p>
                    </div>

                    <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-900">
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Hierarchy</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="rounded-full bg-zinc-900 px-3 py-1 text-xs font-medium text-white dark:bg-zinc-100 dark:text-zinc-900">
                                {{ match ($targetMode) {
                                    'college' => 'Entire College',
                                    'user' => 'Selected Users',
                                    default => 'All Learners',
                                } }}
                            </span>

                            @if ($targetMode === 'college')
                                @foreach ($selectedColleges as $selectedCollege)
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">
                                        {{ App\Enums\College::from($selectedCollege)->label() }}
                                    </span>
                                @endforeach

                                @foreach ($selectedDepartments as $selectedDepartment)
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">
                                        {{ App\Enums\Department::from($selectedDepartment)->label() }}
                                    </span>
                                @endforeach
                            @endif

                            @if ($targetMode === 'user')
                                @foreach ($selectedUserIds as $selectedUserId)
                                    @php
                                        $selectedUser = $userOptions->firstWhere('id', $selectedUserId) ?? \App\Models\User::find($selectedUserId);
                                    @endphp
                                    @if ($selectedUser)
                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">
                                            {{ $selectedUser->name }}
                                        </span>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                        <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Resolved Users</p>
                            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $resolvedUserCount }}</p>
                        </div>
                        <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Skipped Duplicates</p>
                            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $skippedDuplicateCount }}</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Deadline</p>
                        <p class="mt-2 text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $deadlineDays }} day{{ $deadlineDays === '1' ? '' : 's' }}</p>
                    </div>

                    <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Sample Learners</p>
                        <div class="mt-3 space-y-3">
                            @forelse ($previewUsers as $previewUser)
                                <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-900" wire:key="preview-user-{{ $previewUser['id'] }}">
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $previewUser['name'] }}</p>
                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $previewUser['email'] }}</p>
                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $previewUser['college'] }} · {{ $previewUser['department'] }}
                                    </p>
                                </div>
                            @empty
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Select a course and hierarchy to preview matching learners.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800 dark:bg-amber-950/30 dark:text-amber-300">
                        One new batch ID will be generated when you submit this run.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
