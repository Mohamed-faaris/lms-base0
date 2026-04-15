<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <flux:heading level="1" size="xl">Courses</flux:heading>
        <flux:button href="{{ route('admin.courses.create') }}" wire:navigate>
            <flux:icon.plus variant="mini" class="mr-2" />
            Create Course
        </flux:button>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        {{-- Search & Controls --}}
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="relative flex-1 max-w-sm">
                <flux:input 
                    id="courses-search"
                    placeholder="Search courses..." 
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>
            <div class="flex items-center gap-3 text-sm text-zinc-600 dark:text-zinc-400">
                <flux:select id="length-select" size="sm" class="w-20">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </flux:select>
                <span class="text-zinc-500 dark:text-zinc-500">entries</span>
            </div>
        </div>
        
        {{-- Table --}}
        <div class="overflow-x-auto">
            <table id="courses-table" class="w-full">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Slug</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Enrollments</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Created</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
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
        /* Hide ALL DataTables UI elements - more aggressive */
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
        div#courses-table_filter,
        div#courses-table_length,
        div.dataTables_info, 
        div.dataTables_paginate,
        div.dataTables_length,
        table.dataTable + .dataTables_paginate,
        table.dataTable + .dataTables_info { 
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
        var coursesTable;

        function initializeCoursesTable() {
            if (typeof jQuery === 'undefined' || typeof DataTable === 'undefined') {
                setTimeout(initializeCoursesTable, 100);
                return;
            }

            const coursesTableElement = document.getElementById('courses-table');
            if (!coursesTableElement) return;

            if ($.fn.DataTable.isDataTable('#courses-table')) {
                $('#courses-table').DataTable().destroy();
                coursesTableElement.querySelector('tbody').innerHTML = '';
            }

            coursesTable = new DataTable('#courses-table', {
                destroy: true,
                serverSide: true,
                dom: 't',
                ajax: {
                    url: "{{ route('admin.courses.index.datatable') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'title', name: 'title' },
                    { data: 'slug', name: 'slug' },
                    { data: 'enrollments_count', name: 'enrollments_count', searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Search courses...',
                    info: 'Showing _START_ to _END_ of _TOTAL_ courses',
                    infoEmpty: 'No courses found',
                    infoFiltered: '(filtered from _MAX_ total)',
                    processing: '<span class="flex items-center gap-2"><svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing...</span>',
                    emptyTable: 'No courses found',
                    zeroRecords: 'No matching courses found'
                },
                order: [[4, 'desc']],
                pageLength: 10,
                drawCallback: function(settings) {
                    updateTableInfo(settings);
                    updatePagination(settings);
                }
            });

            $('#courses-search').off('keyup.courses').on('keyup.courses', function() {
                coursesTable.search(this.value).draw();
            });

            $('#length-select').off('change.courses').on('change.courses', function() {
                coursesTable.page.len(this.value).draw();
            });
        }

        initializeCoursesTable();
        document.addEventListener('livewire:navigated', initializeCoursesTable);

        function updateTableInfo(settings) {
            const api = new DataTable.Api(settings);
            const info = api.page.info();
            const infoEl = document.getElementById('table-info');
            if (infoEl) {
                if (info.pages === 0) {
                    infoEl.textContent = '0 courses';
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
            coursesTable.page(page).draw(false);
        }
    </script>
</div>
