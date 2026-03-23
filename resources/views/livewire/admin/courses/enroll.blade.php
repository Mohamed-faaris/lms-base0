<div>
    <div class="flex items-center gap-4 mb-6">
        <flux:button variant="ghost" href="{{ route('admin.courses.show', $course->id) }}" wire:navigate icon="arrow-left" />
        <div class="flex-1">
            <flux:heading level="1" size="xl">Enroll Users</flux:heading>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $course->title }}</p>
        </div>
    </div>

    @if ($showSuccess)
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <p class="font-medium text-green-700 dark:text-green-400">Successfully enrolled {{ $enrollmentCount }} users</p>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <flux:heading level="2" size="md" class="mb-4">Enrollment Filters</flux:heading>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>College</flux:label>
                    <div x-data="{ open: false, query: '' }" class="relative rounded-lg border border-zinc-300 bg-white dark:border-zinc-700 dark:bg-zinc-900 p-3">
                        <div class="flex flex-wrap gap-2 mb-3">
                            @if ($this->isAllSelected('selectedColleges'))
                                <span class="rounded-full bg-zinc-900 px-3 py-1 text-xs text-white dark:bg-zinc-100 dark:text-zinc-900">All</span>
                            @endif
                            @if ($this->isAllSelected('selectedColleges'))
                                <span class="rounded-full bg-zinc-900 px-3 py-1 text-xs text-white dark:bg-zinc-100 dark:text-zinc-900">All</span>
                            @else
                                @foreach (App\Enums\College::cases() as $college)
                                    @if (in_array($college->value, $selectedColleges, true))
                                        <button type="button" wire:click="toggleSelection('selectedColleges', '{{ $college->value }}')" class="rounded-full px-3 py-1 text-xs border bg-blue-600 text-white border-blue-600">
                                            {{ $college->label() }}
                                        </button>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <flux:input x-on:focus="open = true" x-on:input="query = $event.target.value; open = true" placeholder="Search colleges" />
                        <div x-show="open" x-on:click.outside="open = false" class="mt-2 max-h-40 overflow-y-auto rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-950">
                            <button type="button" wire:click="selectAll('selectedColleges')" class="flex w-full items-center justify-between px-3 py-2 text-left text-sm hover:bg-zinc-50 dark:hover:bg-zinc-900">
                                <span>All</span>
                                <span class="text-xs text-zinc-400">{{ count($selectedColleges) }} selected</span>
                            </button>
                            @foreach (App\Enums\College::cases() as $college)
                                <button type="button" x-show="query === '' || '{{ strtolower($college->label()) }}'.includes(query.toLowerCase())" wire:click="toggleSelection('selectedColleges', '{{ $college->value }}')" class="flex w-full items-center justify-between px-3 py-2 text-left text-sm hover:bg-zinc-50 dark:hover:bg-zinc-900">
                                    <span>{{ $college->label() }}</span>
                                    <span class="text-xs text-zinc-400">{{ $this->countForOption('selectedColleges', $college->value) }} staff</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Default selects all colleges.</p>
                </flux:field>

                <flux:field>
                    <flux:label>Department</flux:label>
                    <div x-data="{ open: false, query: '' }" class="relative rounded-lg border border-zinc-300 bg-white dark:border-zinc-700 dark:bg-zinc-900 p-3">
                        <div class="flex flex-wrap gap-2 mb-3">
                            @if ($this->isAllSelected('selectedDepartments'))
                                <span class="rounded-full bg-zinc-900 px-3 py-1 text-xs text-white dark:bg-zinc-100 dark:text-zinc-900">All</span>
                            @else
                                @foreach (App\Enums\Department::cases() as $department)
                                    @if (in_array($department->value, $selectedDepartments, true))
                                        <button type="button" wire:click="toggleSelection('selectedDepartments', '{{ $department->value }}')" class="rounded-full px-3 py-1 text-xs border bg-blue-600 text-white border-blue-600">
                                            {{ $department->label() }}
                                        </button>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <flux:input x-on:focus="open = true" x-on:input="query = $event.target.value; open = true" placeholder="Search departments" />
                        <div x-show="open" x-on:click.outside="open = false" class="mt-2 max-h-40 overflow-y-auto rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-950">
                            <button type="button" wire:click="selectAll('selectedDepartments')" class="flex w-full items-center justify-between px-3 py-2 text-left text-sm hover:bg-zinc-50 dark:hover:bg-zinc-900">
                                <span>All</span>
                                <span class="text-xs text-zinc-400">{{ count($selectedDepartments) }} selected</span>
                            </button>
                            @foreach (App\Enums\Department::cases() as $department)
                                <button type="button" x-show="query === '' || '{{ strtolower($department->label()) }}'.includes(query.toLowerCase())" wire:click="toggleSelection('selectedDepartments', '{{ $department->value }}')" class="flex w-full items-center justify-between px-3 py-2 text-left text-sm hover:bg-zinc-50 dark:hover:bg-zinc-900">
                                    <span>{{ $department->label() }}</span>
                                    <span class="text-xs text-zinc-400">{{ $this->countForOption('selectedDepartments', $department->value) }} staff</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Choose one or more departments, or leave all selected.</p>
                </flux:field>

                <flux:field>
                    <flux:label>Faculty</flux:label>
                    <div x-data="{ open: false, query: '' }" class="relative rounded-lg border border-zinc-300 bg-white dark:border-zinc-700 dark:bg-zinc-900 p-3">
                        <div class="flex flex-wrap gap-2 mb-3">
                            @if ($this->isAllSelected('selectedFacultyIds'))
                                <span class="rounded-full bg-zinc-900 px-3 py-1 text-xs text-white dark:bg-zinc-100 dark:text-zinc-900">All</span>
                            @else
                                @foreach (App\Models\User::where('role', App\Enums\Role::Faculty)->orderBy('name')->get() as $user)
                                    @if (in_array((string) $user->id, $selectedFacultyIds, true))
                                        <button type="button" wire:click="toggleSelection('selectedFacultyIds', '{{ $user->id }}')" class="rounded-full px-3 py-1 text-xs border bg-blue-600 text-white border-blue-600">
                                            {{ $user->name }}
                                        </button>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <flux:input x-on:focus="open = true" x-on:input="query = $event.target.value; open = true" placeholder="Search faculty" />
                        <div x-show="open" x-on:click.outside="open = false" class="mt-2 max-h-40 overflow-y-auto rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-950">
                            <button type="button" wire:click="selectAll('selectedFacultyIds')" class="flex w-full items-center justify-between px-3 py-2 text-left text-sm hover:bg-zinc-50 dark:hover:bg-zinc-900">
                                <span>All</span>
                                <span class="text-xs text-zinc-400">{{ $this->facultyCount }} faculty</span>
                            </button>
                            @foreach (App\Models\User::where('role', App\Enums\Role::Faculty)->orderBy('name')->get() as $user)
                                <button type="button" x-show="query === '' || '{{ strtolower($user->name) }}'.includes(query.toLowerCase())" wire:click="toggleSelection('selectedFacultyIds', '{{ $user->id }}')" class="flex w-full items-center justify-between px-3 py-2 text-left text-sm hover:bg-zinc-50 dark:hover:bg-zinc-900">
                                    <span>{{ $user->name }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Use this to enroll only specific faculty members.</p>
                </flux:field>

                <flux:field>
                    <flux:label>Deadline (Days)</flux:label>
                    <flux:input wire:model="deadlineDays" type="number" min="1" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Deadline is stored as an integer number of days.</p>
                </flux:field>

                <flux:button wire:click="enrollUsers" variant="primary" class="w-full">
                    Enroll {{ $enrollmentCount }} User{{ $enrollmentCount === 1 ? '' : 's' }}
                </flux:button>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <flux:heading level="2" size="md" class="mb-4">Preview</flux:heading>

            <div class="mb-4 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">Eligible Users</span>
                    <span class="text-2xl font-bold">{{ $enrollmentCount }}</span>
                </div>
            </div>

            <div class="space-y-2">
                @forelse (array_slice($eligibleUsers, 0, 10) as $user)
                    <div class="flex items-center justify-between rounded-lg border border-zinc-200 px-3 py-2 dark:border-zinc-700">
                        <div>
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $user['name'] }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user['college'] }} - {{ $user['department'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">No eligible users found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
