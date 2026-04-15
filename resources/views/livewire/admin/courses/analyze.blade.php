<div>
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <flux:button variant="ghost" href="{{ route('admin.courses.show', $course->id) }}" wire:navigate icon="arrow-left" />
        <div class="flex-1">
            <flux:heading level="1" size="xl">Analyze: {{ $course->title }}</flux:heading>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Course Statistics & Analytics</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="mb-6 border-b border-zinc-200 dark:border-zinc-700">
        <nav class="flex gap-4">
            <a
                href="{{ route('admin.courses.analyze', ['course' => $course->id, 'activeTab' => 'overview']) }}"
                wire:navigate
                class="pb-3 px-1 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
            >
                Overview
            </a>
            <a
                href="{{ route('admin.courses.analyze', ['course' => $course->id, 'activeTab' => 'enrollments']) }}"
                wire:navigate
                class="pb-3 px-1 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'enrollments' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
            >
                Enrollments
            </a>
        </nav>
    </div>

    {{-- Overview Tab --}}
    @if ($activeTab === 'overview')
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <flux:heading level="3" size="md" class="mb-4">Content Breakdown</flux:heading>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Videos</span>
                    <div class="flex items-center gap-2">
                        <div class="w-32 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $stats['totalContent'] > 0 ? (int) round($stats['totalVideos'] / $stats['totalContent'] * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $stats['totalVideos'] }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Articles</span>
                    <div class="flex items-center gap-2">
                        <div class="w-32 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500 rounded-full" style="width: {{ $stats['totalContent'] > 0 ? (int) round($stats['totalArticles'] / $stats['totalContent'] * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $stats['totalArticles'] }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Quizzes</span>
                    <div class="flex items-center gap-2">
                        <div class="w-32 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full" style="width: {{ $stats['totalContent'] > 0 ? (int) round($stats['totalQuizzes'] / $stats['totalContent'] * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $stats['totalQuizzes'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Module Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Topic</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Content</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Quizzes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($moduleData as $module)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900">
                            <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100 font-medium">{{ $module['name'] }}</td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $module['topic_name'] }}</td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $module['content_count'] }}</td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $module['quiz_count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">No modules found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- Enrollments Tab --}}
    @if ($activeTab === 'enrollments')
        <div class="grid gap-4 md:grid-cols-4 mb-6">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 text-center">
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['totalEnrollments'] }}</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Enrollments</p>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 text-center">
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['completedEnrollments'] }}</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Completed</p>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 text-center">
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['inProgressEnrollments'] }}</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">In Progress</p>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 text-center">
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['notStartedEnrollments'] }}</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Not Started</p>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 mb-6">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <flux:heading level="3" size="md" class="mb-4">Progress Distribution</flux:heading>
                <div class="space-y-4">
                    @foreach ($progressData as $range => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $range }}</span>
                            <div class="flex items-center gap-2">
                                <div class="w-40 h-3 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                    <div
                                        class="h-full rounded-full {{ $range === '100%' ? 'bg-green-500' : 'bg-blue-500' }}"
                                        style="width: {{ $stats['totalEnrollments'] > 0 ? round($count / $stats['totalEnrollments'] * 100) : 0 }}%"
                                    ></div>
                                </div>
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100 w-8 text-right">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <flux:heading level="3" size="md" class="mb-4">Completion Timeline</flux:heading>
                @if (count($completionData) > 0)
                    <div class="space-y-3">
                        @foreach ($completionData as $month => $count)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $month }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-40 h-3 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 rounded-full" style="width: {{ $stats['completedEnrollments'] > 0 ? round($count / $stats['completedEnrollments'] * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">No completions yet</p>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="relative flex-1 max-w-sm">
                        <flux:input
                            id="analyze-enrollments-search"
                            placeholder="Search by learner or email..."
                            icon="magnifying-glass"
                            class="w-full"
                        />
                    </div>
                    <div class="flex items-center gap-3 text-sm text-zinc-600 dark:text-zinc-400">
                        <flux:select id="analyze-length-select" size="sm" class="w-20">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </flux:select>
                        <span class="text-zinc-500 dark:text-zinc-500">entries</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="analyze-enrollments-table" class="w-full">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Enrolled Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50"></tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    Showing <span id="analyze-table-info" class="font-medium text-zinc-900 dark:text-zinc-100">0</span>
                </div>
                <div class="flex items-center gap-1" id="analyze-table-pagination"></div>
            </div>
        </div>

        @assets
        <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" defer></script>
        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js" defer></script>
        @endassets

        <style>
            .dataTables_length,
            .dataTables_length select,
            .dataTables_length label,
            .dataTables_filter,
            .dataTables_filter label,
            .dataTables_filter input,
            .dataTables_info,
            .dataTables_paginate,
            .dataTables_paginate span,
            .dataTables_paginate select,
            div#analyze-enrollments-table_filter,
            div#analyze-enrollments-table_length,
            div.dataTables_info,
            div.dataTables_paginate,
            div.dataTables_length {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                position: absolute !important;
                pointer-events: none !important;
            }
        </style>
        @script
        <script>
            var analyzeEnrollmentsTable;

            const initializeAnalyzeEnrollmentsTable = function() {
                if (typeof jQuery === 'undefined' || typeof DataTable === 'undefined') {
                    setTimeout(initializeAnalyzeEnrollmentsTable, 100);
                    return;
                }

                const tableElement = document.getElementById('analyze-enrollments-table');

                if (!tableElement) {
                    return;
                }

                if ($.fn.DataTable.isDataTable(tableElement)) {
                    $(tableElement).DataTable().destroy();
                    tableElement.querySelector('tbody').innerHTML = '';
                }

                analyzeEnrollmentsTable = new DataTable(tableElement, {
                    destroy: true,
                    serverSide: true,
                    dom: 't',
                    ajax: {
                        url: "{{ route('admin.courses.analyze.datatable', $course->id) }}",
                        type: 'GET',
                        data: function(d) {
                            d.search = $('#analyze-enrollments-search').val();
                        }
                    },
                    columns: [
                        { data: 'user', name: 'user' },
                        { data: 'enrolled_at', name: 'enrolled_at', searchable: false },
                        { data: 'status', name: 'status', searchable: false }
                    ],
                    language: {
                        search: '',
                        searchPlaceholder: 'Search enrollments...',
                        info: 'Showing _START_ to _END_ of _TOTAL_ enrollments',
                        infoEmpty: 'No enrollments found',
                        infoFiltered: '(filtered from _MAX_ total)',
                        processing: '<span class="flex items-center gap-2"><svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing...</span>',
                        emptyTable: 'No enrollments found',
                        zeroRecords: 'No matching enrollments found'
                    },
                    order: [[1, 'desc']],
                    pageLength: 10,
                    drawCallback: function(settings) {
                        updateAnalyzeTableInfo(settings);
                        updateAnalyzePagination(settings);
                    }
                });

                $('#analyze-enrollments-search').off('keyup.analyze').on('keyup.analyze', function() {
                    analyzeEnrollmentsTable.search(this.value).draw();
                });

                $('#analyze-length-select').off('change.analyze').on('change.analyze', function() {
                    analyzeEnrollmentsTable.page.len(this.value).draw();
                });
            };

            initializeAnalyzeEnrollmentsTable();

            function updateAnalyzeTableInfo(settings) {
                const api = new DataTable.Api(settings);
                const info = api.page.info();
                const infoEl = document.getElementById('analyze-table-info');

                if (infoEl) {
                    infoEl.textContent = info.pages === 0 ? '0 enrollments' : `${info.start + 1}-${info.end} of ${info.recordsTotal}`;
                }
            }

            function updateAnalyzePagination(settings) {
                const api = new DataTable.Api(settings);
                const info = api.page.info();
                const container = document.getElementById('analyze-table-pagination');

                if (!container) {
                    return;
                }

                let html = '';

                const prevClass = info.page === 0
                    ? 'opacity-50 cursor-not-allowed bg-zinc-100 dark:bg-zinc-800 text-zinc-400'
                    : 'hover:bg-zinc-100 dark:hover:bg-zinc-700 bg-white dark:bg-zinc-700 border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300';

                html += `<button class="px-3 py-1.5 text-sm rounded-lg border ${prevClass}" ${info.page === 0 ? 'disabled' : ''} onclick="goToAnalyzePage(${info.page - 1})">Previous</button>`;

                for (let i = 0; i < info.pages; i++) {
                    if (i === 0 || i === info.pages - 1 || (i >= info.page - 1 && i <= info.page + 1)) {
                        const pageClass = i === info.page
                            ? 'bg-blue-600 text-white border-blue-600'
                            : 'bg-white dark:bg-zinc-700 border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-600';
                        html += `<button class="px-3 py-1.5 text-sm rounded-lg border ${pageClass}" onclick="goToAnalyzePage(${i})">${i + 1}</button>`;
                    } else if (i === info.page - 2 || i === info.page + 2) {
                        html += `<span class="px-2 text-zinc-400">...</span>`;
                    }
                }

                const nextClass = info.page >= info.pages - 1
                    ? 'opacity-50 cursor-not-allowed bg-zinc-100 dark:bg-zinc-800 text-zinc-400'
                    : 'hover:bg-zinc-100 dark:hover:bg-zinc-700 bg-white dark:bg-zinc-700 border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300';

                html += `<button class="px-3 py-1.5 text-sm rounded-lg border ${nextClass}" ${info.page >= info.pages - 1 ? 'disabled' : ''} onclick="goToAnalyzePage(${info.page + 1})">Next</button>`;

                container.innerHTML = html;
            }

            function goToAnalyzePage(page) {
                analyzeEnrollmentsTable.page(page).draw(false);
            }
        </script>
        @endscript
    @endif

</div>
