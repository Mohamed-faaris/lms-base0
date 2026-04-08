<div>
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex items-start gap-4">
            <flux:button
                variant="ghost"
                href="{{ route('admin.enrollments.index') }}"
                wire:navigate
                icon="arrow-left"
            />

            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <flux:heading level="1" size="xl">Enrollment Batch</flux:heading>
                    @if ($batch['isLegacy'])
                        <flux:badge color="amber" size="sm">Legacy batch</flux:badge>
                    @else
                        <flux:badge color="zinc" size="sm">Batch</flux:badge>
                    @endif
                </div>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $batch['courseTitle'] }} · enrolled by {{ $batch['enrolledBy'] }} on {{ $batch['enrolledAt'] }}
                </p>
                <p class="mt-1 text-sm font-mono text-zinc-500 dark:text-zinc-400">
                    {{ $batch['displayBatchId'] }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <flux:button href="{{ route('admin.enrollments.index') }}" variant="outline" wire:navigate>
                Back to List
            </flux:button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-950/30">
            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(320px,0.9fr)]">
        <div class="space-y-6">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Learners</p>
                    <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $batch['learnersCount'] }}</p>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Course</p>
                    @if ($batch['courseUrl'])
                        <a href="{{ $batch['courseUrl'] }}" wire:navigate class="mt-2 block text-lg font-semibold text-zinc-900 hover:text-blue-600 dark:text-zinc-100 dark:hover:text-blue-400">
                            View Course
                        </a>
                    @else
                        <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Unknown Course</p>
                    @endif
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Deadline</p>
                    <p class="mt-2 text-lg font-semibold {{ $batch['deadlineTone'] }}">{{ $batch['deadlineLabel'] }}</p>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Batch Key</p>
                    <p class="mt-2 break-all font-mono text-sm text-zinc-900 dark:text-zinc-100">{{ $batch['batchKey'] }}</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
                <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <flux:heading level="2" size="lg">Learners in this batch</flux:heading>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Search by learner name or email, update an individual deadline, or remove one learner.</p>
                        </div>
                        <div class="sm:w-72">
                            <flux:input
                                wire:model.live="learnerSearch"
                                icon="magnifying-glass"
                                placeholder="Search learners..."
                            />
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Learner</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Deadline</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Update Deadline</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse ($learners as $learner)
                                <tr wire:key="learner-{{ $learner['userId'] }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40">
                                    <td class="px-4 py-4">
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $learner['name'] }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $learner['enrolledBy'] }} · {{ $learner['enrolledAt'] }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-zinc-500 dark:text-zinc-400">{{ $learner['email'] }}</td>
                                    <td class="px-4 py-4 text-sm font-medium {{ $learner['deadlineTone'] }}">{{ $learner['deadlineLabel'] }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <flux:input
                                                wire:model="learnerDeadlineDays.{{ $learner['userId'] }}"
                                                type="number"
                                                min="1"
                                                class="w-28"
                                            />
                                            <span class="text-sm text-zinc-500 dark:text-zinc-400">days</span>
                                            <flux:button
                                                size="sm"
                                                variant="outline"
                                                wire:click="saveLearnerDeadline({{ $learner['userId'] }})"
                                            >
                                                Save
                                            </flux:button>
                                        </div>
                                        @error('learnerDeadlineDays.'.$learner['userId'])
                                            <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            class="text-red-600 hover:text-red-700"
                                            wire:click="revokeLearner({{ $learner['userId'] }})"
                                            wire:confirm="Revoke {{ $learner['name'] }} from this batch?"
                                        >
                                            Revoke
                                        </flux:button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                        No learners match your search.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:heading level="2" size="lg">Edit Batch</flux:heading>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Update the batch reference or change the deadline for every learner in this group.</p>

                <div class="mt-5 space-y-4">
                    <flux:field>
                        <flux:label>Batch Reference</flux:label>
                        <flux:input
                            wire:model="batchIdInput"
                            placeholder="{{ $batch['isLegacy'] ? 'Leave blank to keep this batch legacy' : 'Enter a new batch reference' }}"
                        />
                        <flux:error name="batchIdInput" />
                    </flux:field>

                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-3 text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900/60 dark:text-zinc-400">
                        @if ($batch['isLegacy'])
                            This batch currently has no stored batch reference. Enter one above to give it a reusable batch ID.
                        @else
                            Leave the reference as-is to keep the current batch ID.
                        @endif
                    </div>

                    <flux:field>
                        <flux:label>Deadline in Days</flux:label>
                        <flux:input wire:model="deadlineDays" type="number" min="1" />
                        <flux:error name="deadlineDays" />
                    </flux:field>

                    <flux:button wire:click="saveBatchChanges" variant="primary" class="w-full">
                        Save Changes
                    </flux:button>
                </div>
            </div>

            <div class="rounded-xl border border-red-200 bg-red-50 p-6 dark:border-red-900 dark:bg-red-950/20">
                <flux:heading level="2" size="lg" class="text-red-700 dark:text-red-300">Danger Zone</flux:heading>
                <p class="mt-1 text-sm text-red-700/80 dark:text-red-300/80">
                    Revoke the whole batch only if you want to remove every learner from this enrollment group.
                </p>

                <flux:button
                    variant="danger"
                    class="mt-4 w-full"
                    wire:click="revokeBatch"
                    wire:confirm="Revoke this entire batch and remove every learner from it?"
                >
                    Revoke Entire Batch
                </flux:button>
            </div>
        </div>
    </div>
</div>
