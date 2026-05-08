<div>
    <flux:heading>Behavioral Analytics</flux:heading>
    <flux:text class="mb-6">Monitor faculty engagement, focus, and compliance metrics</flux:text>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon.user class="h-5 w-5 text-blue-500" />
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Faculty</span>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ count($facultyData) }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon.fire class="h-5 w-5 text-amber-500" />
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Avg Engagement</span>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $avgEngagement }}%</p>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon.eye class="h-5 w-5 text-emerald-500" />
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Avg Focus</span>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $avgFocus }}%</p>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon.shield-check class="h-5 w-5 text-purple-500" />
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Avg Compliance</span>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $avgCompliance }}%</p>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
            <flux:heading>Faculty Behavioral Metrics</flux:heading>
        </div>
        
        <table class="w-full">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Faculty</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Engagement</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Consistency</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Focus</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Compliance</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Overall</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($facultyData as $faculty)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $faculty['name'] }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $faculty['email'] }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ $faculty['engagement_score'] }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $faculty['engagement_score'] }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-500 rounded-full" style="width: {{ $faculty['consistency_score'] }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $faculty['consistency_score'] }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $faculty['focus_score'] }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $faculty['focus_score'] }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-purple-500 rounded-full" style="width: {{ $faculty['compliance_score'] }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $faculty['compliance_score'] }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <flux:badge color="{{ $faculty['overall_score'] >= 70 ? 'emerald' : ($faculty['overall_score'] >= 40 ? 'amber' : 'red') }}">
                                {{ $faculty['overall_score'] }}%
                            </flux:badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                            No behavioral data recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>