<div>
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <flux:heading level="1" size="xl">Users</flux:heading>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Browse every account and open a profile to review or update details.
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-950/30">
            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Users</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $totalUsers }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Admin Users</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $adminUsers }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Faculty Users</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $facultyUsers }}</p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        {{-- Search & Controls --}}
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                {{-- Search --}}
                <div class="relative flex-1 max-w-sm">
                    <flux:input 
                        id="users-search"
                        placeholder="Search users..." 
                        icon="magnifying-glass"
                        class="w-full"
                    />
                </div>
                {{-- Filters --}}
                <div class="flex flex-wrap items-center gap-3">
                    <select id="filter-role" class="px-3 py-2 text-sm border border-zinc-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Roles</option>
                        <option value="faculty">Faculty</option>
                        <option value="staff">Staff</option>
                        <option value="learner">Learner</option>
                    </select>
                    <select id="filter-college" class="px-3 py-2 text-sm border border-zinc-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 w-40">
                        <option value="">All Colleges</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->value }}">{{ $college->label() }}</option>
                        @endforeach
                    </select>
                    <select id="filter-department" class="px-3 py-2 text-sm border border-zinc-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 w-40">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->value }}">{{ $department->label() }}</option>
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
            <table id="users-table" class="w-full">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">College</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Role</th>
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
        div#users-table_filter,
        div#users-table_length,
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
        
        document.addEventListener('DOMContentLoaded', function() {
            table = new DataTable('#users-table', {
                processing: true,
                serverSide: true,
                dom: 't',
                ajax: {
                    url: "{{ route('admin.users.datatable') }}",
                    type: 'GET',
                    data: function(d) {
                        d.role = $('#filter-role').val();
                        d.college = $('#filter-college').val();
                        d.department = $('#filter-department').val();
                    }
                },
                columns: [
                    { data: 'user', name: 'user', orderable: false, searchable: true },
                    { data: 'college', name: 'college', searchable: false },
                    { data: 'department', name: 'department', searchable: false },
                    { data: 'role', name: 'role', searchable: false }
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Search users...',
                    info: 'Showing _START_ to _END_ of _TOTAL_ users',
                    infoEmpty: 'No users found',
                    infoFiltered: '(filtered from _MAX_ total)',
                    processing: '<span class="flex items-center gap-2"><svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing...</span>',
                    emptyTable: 'No users found',
                    zeroRecords: 'No matching users found'
                },
                order: [[0, 'asc']],
                pageLength: 10,
                drawCallback: function(settings) {
                    updateTableInfo(settings);
                    updatePagination(settings);
                }
            });

            // Search
            $('#users-search').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Length change
            $('#length-select').on('change', function() {
                table.page.len(this.value).draw();
            });

            // Filter changes
            $('#filter-role, #filter-college, #filter-department').on('change', function() {
                table.draw();
            });
        });

        function resetFilters() {
            $('#users-search').val('');
            $('#filter-role').val('');
            $('#filter-college').val('');
            $('#filter-department').val('');
            table.search('').draw();
        }

        function updateTableInfo(settings) {
            const api = new DataTable.Api(settings);
            const info = api.page.info();
            const infoEl = document.getElementById('table-info');
            if (infoEl) {
                if (info.pages === 0) {
                    infoEl.textContent = '0 users';
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
    </script>
</div>