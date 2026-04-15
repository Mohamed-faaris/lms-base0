<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading level="1" size="xl">Enrollments</flux:heading>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Review who is enrolled, which course they are in, and which deadlines need attention.</p>
        </div>
        <flux:button href="{{ route('admin.enrollments.create') }}" wire:navigate>
            Create Enrollment Batch
        </flux:button>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-950/30">
            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        {{-- Search & Controls --}}
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                {{-- Search --}}
                <div class="relative flex-1 max-w-sm">
                    <flux:input 
                        id="enrollments-search"
                        placeholder="Search by learner, email, course..." 
                        icon="magnifying-glass"
                        class="w-full"
                    />
                </div>
                {{-- Filters --}}
                <div class="flex flex-wrap items-center gap-3">
                    <select id="filter-course" class="px-3 py-2 text-sm border border-zinc-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 w-48">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                    <flux:button variant="outline" size="sm" onclick="resetFilters()">
                        Reset
                    </flux:button>
                </div>
            </div>
            {{-- Entries per page --}}
            <div class="flex items-center gap-3 text-sm text-zinc-600 dark:text-zinc-400 mt-4">
                <span class="text-zinc-500 dark:text-zinc-500">Show</span>
                <select id="length-select" class="px-3 py-2 text-sm border border-zinc-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 w-20">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="text-zinc-500 dark:text-zinc-500">entries</span>
            </div>
        </div>
        
        {{-- Table --}}
        <div class="overflow-x-auto">
            <table id="enrollments-table" class="w-full">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Batch ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Course</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Users</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Enrolled By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Enrolled On</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Deadline</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                </tbody>
            </table>
        </div>
        
        {{-- Footer --}}
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                Showing <span id="table-info" class="font-medium text-zinc-900 dark:text-zinc-100">0</span>
            </div>
            <div class="flex items-center gap-1" id="table-pagination"></div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
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
        div#enrollments-table_filter,
        div#enrollments-table_length,
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script>
        let table;

        const initializeEnrollmentsTable = function() {
            if (table) {
                table.destroy();
                table = null;
            }

            table = new DataTable('#enrollments-table', {
                processing: true,
                serverSide: true,
                dom: 't',
                ajax: {
                    url: "{{ route('admin.enrollments.datatable') }}",
                    type: 'GET',
                    data: function(d) {
                        d.search = $('#enrollments-search').val();
                        d.course_id = $('#filter-course').val();
                    }
                },
                columns: [
                    { data: 'batch_id', name: 'batch_id' },
                    { data: 'course', name: 'course' },
                    { data: 'learners_count', name: 'learners_count' },
                    { data: 'enrolled_by', name: 'enrolled_by' },
                    { data: 'enrolled_at', name: 'enrolled_at' },
                    { data: 'deadline', name: 'deadline', searchable: false }
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
                order: [[4, 'desc']],
                pageLength: 10,
                drawCallback: function(settings) {
                    updateTableInfo(settings);
                    updatePagination(settings);
                }
            });

            $('#enrollments-search').off('keyup.enrollments').on('keyup.enrollments', function() {
                table.search(this.value).draw();
            });

            $('#length-select').off('change.enrollments').on('change.enrollments', function() {
                table.page.len(this.value).draw();
            });

            $('#filter-course').off('change.enrollments').on('change.enrollments', function() {
                table.draw();
            });
        };

        initializeEnrollmentsTable();
        document.addEventListener('livewire:navigated', initializeEnrollmentsTable);

        function updateTableInfo(settings) {
            const api = new DataTable.Api(settings);
            const info = api.page.info();
            const infoEl = document.getElementById('table-info');
            if (infoEl) {
                if (info.pages === 0) {
                    infoEl.textContent = '0 enrollments';
                } else {
                    infoEl.textContent = `${info.start + 1}-${info.end} of ${info.recordsTotal}`;
                }
            }
        }

        function updatePagination(settings) {
            const api = new DataTable.Api(settings);
            const info = api.page.info();
            const container = document.getElementById('table-pagination');
            if (!container) return;

            let html = '';
            
            // Previous button
            const prevClass = info.page === 0 
                ? 'opacity-50 cursor-not-allowed bg-zinc-100 dark:bg-zinc-800 text-zinc-400' 
                : 'hover:bg-zinc-100 dark:hover:bg-zinc-700 bg-white dark:bg-zinc-700 border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300';
            html += `<button class="px-3 py-1.5 text-sm rounded-lg border ${prevClass}" ${info.page === 0 ? 'disabled' : ''} onclick="goToPage(${info.page - 1})">Previous</button>`;
            
            // Page numbers
            for (let i = 0; i < info.pages; i++) {
                if (i === 0 || i === info.pages - 1 || (i >= info.page - 1 && i <= info.page + 1)) {
                    const pageClass = i === info.page 
                        ? 'bg-blue-600 text-white border-blue-600' 
                        : 'bg-white dark:bg-zinc-700 border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-600';
                    html += `<button class="px-3 py-1.5 text-sm rounded-lg border ${pageClass}" onclick="goToPage(${i})">${i + 1}</button>`;
                } else if (i === info.page - 2 || i === info.page + 2) {
                    html += `<span class="px-2 text-zinc-400">...</span>`;
                }
            }
            
            // Next button
            const nextClass = info.page >= info.pages - 1 
                ? 'opacity-50 cursor-not-allowed bg-zinc-100 dark:bg-zinc-800 text-zinc-400' 
                : 'hover:bg-zinc-100 dark:hover:bg-zinc-700 bg-white dark:bg-zinc-700 border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300';
            html += `<button class="px-3 py-1.5 text-sm rounded-lg border ${nextClass}" ${info.page >= info.pages - 1 ? 'disabled' : ''} onclick="goToPage(${info.page + 1})">Next</button>`;
            
            container.innerHTML = html;
        }

        function goToPage(page) {
            table.page(page).draw(false);
        }

        function resetFilters() {
            $('#enrollments-search').val('');
            $('#filter-course').val('');
            table.search('').draw();
        }
    </script>
</div>
