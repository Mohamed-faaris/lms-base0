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
            <flux:button
                variant="outline"
                icon="calendar-days"
                wire:click="openGlobalDeadlineModal"
            >
                Edit Deadline
            </flux:button>

            <flux:button href="{{ route('admin.enrollments.index') }}" variant="outline" wire:navigate>
                Back to List
            </flux:button>

            <flux:button
                variant="danger"
                wire:click="openRevokeBatchModal"
            >
                Revoke Entire Batch
            </flux:button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-950/30">
            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

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

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <flux:heading level="2" size="lg">Progress Distribution</flux:heading>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">See how this batch is progressing across the course.</p>
                </div>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                @foreach ($progressDistribution as $range => $count)
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900/50">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $range }}</span>
                            <span class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $count }}</span>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                            <div
                                class="h-full rounded-full {{ $range === '100%' ? 'bg-emerald-500' : 'bg-blue-500' }}"
                                style="width: {{ $batch['learnersCount'] > 0 ? round(($count / $batch['learnersCount']) * 100) : 0 }}%"
                            ></div>
                        </div>
                    </div>
                @endforeach
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
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    class="px-0 text-xs font-medium uppercase tracking-wider text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                                    wire:click="setSort('name')"
                                >
                                    Learner
                                    @if ($sortBy === 'name')
                                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </flux:button>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    class="px-0 text-xs font-medium uppercase tracking-wider text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                                    wire:click="setSort('progress')"
                                >
                                    Progress
                                    @if ($sortBy === 'progress')
                                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </flux:button>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                <div class="flex items-center gap-1">
                                    <span>Status</span>

                                    <flux:dropdown position="bottom" align="start">
                                        <flux:button
                                            size="sm"
                                            variant="{{ $progressFilter !== 'all' ? 'primary' : 'ghost' }}"
                                            icon="funnel"
                                            class="min-w-0"
                                        />

                                        <flux:menu>
                                            <flux:menu.radio.group>
                                                <flux:menu.item as="button" type="button" wire:click="setProgressFilter('all')">
                                                    All statuses
                                                </flux:menu.item>
                                                <flux:menu.item as="button" type="button" wire:click="setProgressFilter('not-started')">
                                                    Not started
                                                </flux:menu.item>
                                                <flux:menu.item as="button" type="button" wire:click="setProgressFilter('25')">
                                                    25%
                                                </flux:menu.item>
                                                <flux:menu.item as="button" type="button" wire:click="setProgressFilter('50')">
                                                    50%
                                                </flux:menu.item>
                                                <flux:menu.item as="button" type="button" wire:click="setProgressFilter('75')">
                                                    75%
                                                </flux:menu.item>
                                                <flux:menu.item as="button" type="button" wire:click="setProgressFilter('100')">
                                                    100%
                                                </flux:menu.item>
                                            </flux:menu.radio.group>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    class="px-0 text-xs font-medium uppercase tracking-wider text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                                    wire:click="setSort('deadline')"
                                >
                                    Deadline
                                    @if ($sortBy === 'deadline')
                                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </flux:button>
                            </th>
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
                                <td class="px-4 py-4">
                                    <div class="min-w-36">
                                        <div class="flex items-center gap-2">
                                            <div class="h-2 w-24 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                                                <div class="h-full rounded-full {{ $learner['progress'] === 100 ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width: {{ $learner['progress'] }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $learner['progress'] }}%</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <flux:badge color="{{ $learner['statusBadgeColor'] }}">{{ $learner['statusLabel'] }}</flux:badge>
                                </td>
                                <td class="px-4 py-4 text-sm font-medium {{ $learner['deadlineTone'] }}">{{ $learner['deadlineLabel'] }}</td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button
                                            size="sm"
                                            variant="outline"
                                            icon="calendar-days"
                                            aria-label="Update learner deadline"
                                            title="Update learner deadline"
                                            wire:click="openLearnerDeadlineModal({{ $learner['userId'] }})"
                                        />

                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            icon="trash"
                                            aria-label="Revoke learner"
                                            title="Revoke learner"
                                            class="text-red-600 hover:text-red-700"
                                            wire:click="revokeLearner({{ $learner['userId'] }})"
                                            wire:confirm="Revoke {{ $learner['name'] }} from this batch?"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    No learners match your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <flux:modal wire:model="showGlobalDeadlineModal" class="max-w-lg">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">Update global deadline</flux:heading>
                    <flux:text class="mt-2">
                        Set a new deadline for all {{ $batch['learnersCount'] }} learners in this batch.
                    </flux:text>
                </div>

                <flux:field>
                    <flux:label>Deadline in Days</flux:label>
                    <flux:input wire:model="globalDeadlineDays" type="number" min="1" />
                    <flux:error name="globalDeadlineDays" />
                </flux:field>

                <div class="flex justify-end gap-2">
                    <flux:button
                        type="button"
                        variant="outline"
                        wire:click="closeGlobalDeadlineModal"
                    >
                        Cancel
                    </flux:button>

                    <flux:button variant="primary" icon="archive-box-arrow-down" wire:click="saveGlobalDeadline">
                        Save Deadline
                    </flux:button>
                </div>
            </div>
        </flux:modal>

        <flux:modal wire:model="showLearnerDeadlineModal" class="max-w-lg">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">Update learner deadline</flux:heading>
                    <flux:text class="mt-2">
                        Set a new deadline for {{ $selectedLearnerName }} in days.
                    </flux:text>
                </div>

                <flux:field>
                    <flux:label>Deadline in Days</flux:label>
                    <flux:input wire:model="selectedLearnerDeadlineDays" type="number" min="1" />
                    <flux:error name="selectedLearnerDeadlineDays" />
                </flux:field>

                <div class="flex justify-end gap-2">
                    <flux:button
                        type="button"
                        variant="outline"
                        wire:click="closeLearnerDeadlineModal"
                    >
                        Cancel
                    </flux:button>

                    <flux:button variant="primary" icon="archive-box-arrow-down" wire:click="saveSelectedLearnerDeadline">
                        Save Deadline
                    </flux:button>
                </div>
            </div>
        </flux:modal>

        <flux:modal wire:model="showRevokeBatchModal" class="max-w-lg">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">Are you sure you want to revoke this batch?</flux:heading>
                    <flux:text class="mt-2">
                        This will remove {{ $batch['learnersCount'] }} enrollment{{ $batch['learnersCount'] === 1 ? '' : 's' }} from this group and cannot be undone.
                    </flux:text>
                </div>

                <flux:field>
                    <flux:label>Type CONFIRM to continue</flux:label>
                    <flux:input wire:model="revokeBatchConfirmation" placeholder="CONFIRM" />
                    <flux:error name="revokeBatchConfirmation" />
                </flux:field>

                <div class="flex justify-end gap-2">
                    <flux:button
                        type="button"
                        variant="outline"
                        wire:click="closeRevokeBatchModal"
                    >
                        Cancel
                    </flux:button>

                    <flux:button variant="danger" wire:click="revokeBatch">
                        Revoke Entire Batch
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</div>
