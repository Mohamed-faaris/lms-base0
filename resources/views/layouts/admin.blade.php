<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="flex min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-zinc-800 border-r border-zinc-200 dark:border-zinc-700 flex flex-col">
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-zinc-200 dark:border-zinc-700">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-semibold text-zinc-900 dark:text-white">
                    Admin Panel
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700/50' }}">
                    <flux:icon name="squares-2x2" class="w-5 h-5" />
                    Dashboard
                </a>

                <a href="{{ route('admin.courses.index') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.courses.*') ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700/50' }}">
                    <flux:icon name="academic-cap" class="w-5 h-5" />
                    Courses
                </a>

                {{-- Future: Users, Enrollments, Badges --}}
                {{-- 
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                    <flux:icon name="users" class="w-5 h-5" />
                    Users
                </a>
                
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                    <flux:icon name="user-group" class="w-5 h-5" />
                    Enrollments
                </a>
                
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                    <flux:icon name="star" class="w-5 h-5" />
                    Badges
                </a>
                --}}

                <a href="{{ route('courses') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('courses') ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700/50' }}">
                    <flux:icon name="book-open" class="w-5 h-5" />
                    Public Courses
                </a>
            </nav>

            <!-- User Menu -->
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center gap-3 px-2">
                    <flux:avatar name="{{ auth()->user()->name }}" size="sm" />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                            {{ auth()->user()->role->label() }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:button type="submit" variant="ghost" size="sm" icon="arrow-right-on-square" />
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="h-16 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between px-6">
                <flux:heading level="1" size="lg">{{ $title ?? 'Dashboard' }}</flux:heading>
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white">
                        Go to Home
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 p-6">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>