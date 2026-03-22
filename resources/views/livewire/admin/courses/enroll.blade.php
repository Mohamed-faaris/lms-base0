<div>
    <div class="flex items-center gap-4 mb-6">
        <flux:button variant="ghost" href="{{ route('admin.courses.show', $course->id) }}" wire:navigate icon="arrow-left" />
        <div class="flex-1">
            <flux:heading level="1" size="xl">Enroll Users</flux:heading>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $course->title }}</p>
        </div>
    </div>

    @if ($showSuccess)
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center gap-2 text-green-700 dark:text-green-400">
                <flux:icon.check-circle class="w-5 h-5" />
                <span class="font-medium">Successfully enrolled {{ $enrollmentCount }} users</span>
            </div>
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <flux:heading level="2" size="md" class="mb-4">Enrollment Options</flux:heading>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Enrollment Type</flux:label>
                    <flux:select wire:model="enrollType" required>
                        <flux:select.option value="college">By College (All Staff)</flux:select.option>
                        <flux:select.option value="department">By Department (All Staff)</flux:select.option>
                        <flux:select.option value="individual">Individual User</flux:select.option>
                    </flux:select>
                </flux:field>

                @if ($enrollType === 'college')
                    <flux:field>
                        <flux:label>Select College</flux:label>
                        <flux:select wire:model="selectedCollege" placeholder="Select a college">
                            <flux:select.option value="">Select college...</flux:select.option>
                            @foreach (App\Enums\College::cases() as $college)
                                <flux:select.option value="{{ $college }}">{{ $college->label() }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                @endif

                @if ($enrollType === 'department')
                    <flux:field>
                        <flux:label>Select College</flux:label>
                        <flux:select wire:model="selectedCollege" placeholder="Select a college">
                            <flux:select.option value="">Select college...</flux:select.option>
                            @foreach (App\Enums\College::cases() as $college)
                                <flux:select.option value="{{ $college }}">{{ $college->label() }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    @if ($selectedCollege)
                        <flux:field>
                            <flux:label>Select Department</flux:label>
                            <flux:select wire:model="selectedDepartment" placeholder="Select a department">
                                <flux:select.option value="">Select department...</flux:select.option>
                                @foreach (App\Enums\Department::cases() as $department)
                                    <flux:select.option value="{{ $department }}">{{ $department->label() }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    @endif
                @endif

                @if ($enrollType === 'individual')
                    <flux:field>
                        <flux:label>Select User</flux:label>
                        <flux:select wire:model="selectedUser" placeholder="Select a user" search>
                            <flux:select.option value="">Search for a user...</flux:select.option>
                            @foreach (App\Models\User::where('role', App\Enums\Role::Faculty)->orderBy('name')->get() as $user)
                                <flux:select.option value="{{ $user }}">{{ $user->name }} ({{ $user->college?->label() ?? 'N/A' }} - {{ $user->department?->label() ?? 'N/A' }})</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                @endif

                <flux:field>
                    <flux:label>Deadline (Days)</flux:label>
                    <flux:input wire:model="deadlineDays" type="number" min="1" placeholder="30" />
                    <flux:helper>Number of days from now to complete the course</flux:helper>
                </flux:field>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <flux:heading level="2" size="md" class="mb-4">Enrollment Summary</flux:heading>

            <div class="space-y-4">
                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Eligible Users</span>
                        <span class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $enrollmentCount }}</span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Users who can be enrolled (not already enrolled)</p>
                </div>

                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Deadline</span>
                        <span class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $deadlineDays }} days</span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">From: {{ now()->format('M d, Y') }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Until: {{ now()->addDays((int) $deadlineDays)->format('M d, Y') }}</p>
                </div>

                @if (count($eligibleUsers) > 0)
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                        <flux:heading level="3" size="sm" class="mb-2">Preview Users</flux:heading>
                        <div class="max-h-48 overflow-y-auto space-y-1">
                            @foreach (array_slice($eligibleUsers, 0, 10) as $user)
                                <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <div class="h-6 w-6 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                        <span class="text-xs font-medium">{{ substr($user['name'] ?? '?', 0, 1) }}</span>
                                    </div>
                                    <span>{{ $user['name'] }}</span>
                                    <span class="text-xs text-zinc-400">({{ $user['college'] ?? 'N/A' }} - {{ $user['department'] ?? 'N/A' }})</span>
                                </div>
                            @endforeach
                            @if (count($eligibleUsers) > 10)
                                <p class="text-xs text-zinc-500">... and {{ count($eligibleUsers) - 10 }} more</p>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="pt-4">
                    <flux:button wire:click="enrollUsers" variant="primary" :disabled="$enrollmentCount === 0" class="w-full">
                        Enroll {{ $enrollmentCount }} User{{ $enrollmentCount === 1 ? '' : 's' }}
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</div>