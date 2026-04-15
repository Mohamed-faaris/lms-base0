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
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center gap-4">
                <div class="relative flex-1 max-w-sm">
                    <flux:icon.magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-400" />
                    <input type="search" id="courses-search" placeholder="Search courses..." 
                        class="w-full pl-10 pr-4 py-2 text-sm border border-zinc-200 dark:border-zinc-600 rounded-lg bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <flux:button variant="outline" size="sm" onclick="resetTable()">
                    <flux:icon.arrow-path class="h-4 w-4 mr-1" />
                    Reset
                </flux:button>
            </div>
        </div>
        
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
        
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                Showing <span id="table-info"></span>
            </div>
            <div class="flex items-center gap-2" id="table-pagination"></div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script>
        let table;
        
        document.addEventListener('DOMContentLoaded', function() {
            table = new DataTable('#courses-table', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.courses.index.datatable') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'title', name: 'title' },
                    { data: 'slug', name: 'slug' },
                    { data: 'enrollments_count', name: 'enrollments_count' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Search...',
                    lengthMenu: '_MENU_ per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ courses',
                    infoEmpty: 'No courses found',
                    infoFiltered: '(filtered from _MAX_ total)',
                    processing: '<span class="flex items-center gap-2"><svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing...</span>',
                    emptyTable: 'No courses found',
                    zeroRecords: 'No matching courses found'
                },
                order: [[4, 'desc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                drawCallback: function(settings) {
                    updateTableInfo(settings);
                    updatePagination(settings);
                }
            });

            // Custom search
            $('#courses-search').on('keyup', function() {
                table.search(this.value).draw();
            });
        });

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
            html += `<button class="px-3 py-1.5 text-sm rounded-lg border ${info.page === 0 ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400 cursor-not-allowed' : 'bg-white dark:bg-zinc-700 border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-600'} disabled:opacity-50" ${info.page === 0 ? 'disabled' : ''} onclick="goToPage(${info.page - 1})">Previous</button>`;
            
            // Page numbers
            for (let i = 0; i < info.pages; i++) {
                if (i === 0 || i === info.pages - 1 || (i >= info.page - 1 && i <= info.page + 1)) {
                    html += `<button class="px-3 py-1.5 text-sm rounded-lg border ${i === info.page ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-zinc-700 border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-600'}" onclick="goToPage(${i})">${i + 1}</button>`;
                } else if (i === info.page - 2 || i === info.page + 2) {
                    html += `<span class="px-2 text-zinc-400">...</span>`;
                }
            }
            
            // Next button
            html += `<button class="px-3 py-1.5 text-sm rounded-lg border ${info.page >= info.pages - 1 ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400 cursor-not-allowed' : 'bg-white dark:bg-zinc-700 border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-600'} disabled:opacity-50" ${info.page >= info.pages - 1 ? 'disabled' : ''} onclick="goToPage(${info.page + 1})">Next</button>`;
            
            container.innerHTML = html;
        }

        function goToPage(page) {
            table.page(page).draw(false);
        }

        function resetTable() {
            document.getElementById('courses-search').value = '';
            table.search('').page(0).draw();
        }
    </script>
</div>