<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Admin</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')

        <style>
            .sidebar-link {
                display: flex;
                align-items: center;
                padding: 12px 24px;
                color: #d1d5db;
                text-decoration: none;
                transition: all 0.2s;
            }
            .sidebar-link:hover {
                background-color: #374151;
                color: white;
            }
            .sidebar-link.active {
                background-color: #374151;
                border-left: 4px solid #3b82f6;
                color: white;
            }
            .menu-item {
                cursor: pointer;
            }
            .submenu {
                display: none;
                background-color: #111827;
            }
            .submenu.show {
                display: block;
            }
            .submenu-link {
                display: block;
                padding: 10px 24px 10px 48px;
                color: #9ca3af;
                text-decoration: none;
                font-size: 14px;
                transition: all 0.2s;
            }
            .submenu-link:hover {
                color: white;
                background-color: #1f2937;
            }
            .submenu-link.active {
                color: #3b82f6;
                background-color: #1f2937;
            }
            .chevron {
                transition: transform 0.2s;
            }
            .chevron.rotate {
                transform: rotate(90deg);
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <div class="flex">
                <!-- Sidebar -->
                <aside style="background-color: #1f2937; min-height: 100vh; width: 256px; position: fixed; left: 0; top: 0;">
                    <div style="padding: 24px 16px; border-bottom: 1px solid #374151;">
                        <h1 style="font-size: 24px; font-weight: bold; text-align: center; color: white;">LMS Admin</h1>
                    </div>

                    <nav style="padding: 16px 0;">
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}"
                           class="sidebar-link menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <svg style="width: 20px; height: 20px; margin-right: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <!-- Colleges (with submenu) -->
                        <div class="menu-item">
                            <a href="javascript:void(0)" onclick="toggleSubmenu('colleges')"
                               class="sidebar-link {{ request()->routeIs('admin.colleges.*') ? 'active' : '' }}">
                                <svg style="width: 20px; height: 20px; margin-right: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span style="flex: 1;">Colleges</span>
                                <svg class="chevron {{ request()->routeIs('admin.colleges.*') ? 'rotate' : '' }}" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <div class="submenu {{ request()->routeIs('admin.colleges.*') ? 'show' : '' }}" id="colleges">
                                <a href="{{ route('admin.colleges.index') }}"
                                   class="submenu-link {{ request()->routeIs('admin.colleges.index') ? 'active' : '' }}">
                                    All Colleges
                                </a>
                                <a href="{{ route('admin.departments.index') }}"
                                   class="submenu-link {{ request()->routeIs('admin.departments.index') ? 'active' : '' }}">
                                    Departments
                                </a>
                                <a href="{{ route('admin.faculties.index') }}"
                                   class="submenu-link {{ request()->routeIs('admin.faculties.index') ? 'active' : '' }}">
                                    Faculties
                                </a>
                            </div>
                        </div>

                        <!-- Courses (with submenu) -->
                        <div class="menu-item">
                            <a href="javascript:void(0)" onclick="toggleSubmenu('courses')"
                               class="sidebar-link {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.modules.*') || request()->routeIs('admin.videos.*') || request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.fun.*') ? 'active' : '' }}">
                                <svg style="width: 20px; height: 20px; margin-right: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span style="flex: 1;">Courses</span>
                                <svg class="chevron {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.modules.*') || request()->routeIs('admin.videos.*') || request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.fun.*') ? 'rotate' : '' }}" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <div class="submenu {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.modules.*') || request()->routeIs('admin.videos.*') || request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.fun.*') ? 'show' : '' }}" id="courses">
                                <a href="{{ route('admin.courses.index') }}"
                                   class="submenu-link {{ request()->routeIs('admin.courses.index') ? 'active' : '' }}">
                                    All Courses
                                </a>
                                <a href="{{ route('admin.modules.index') }}"
                                   class="submenu-link {{ request()->routeIs('admin.modules.index') ? 'active' : '' }}">
                                    Modules
                                </a>
                                <a href="{{ route('admin.videos.index') }}"
                                   class="submenu-link {{ request()->routeIs('admin.videos.index') ? 'active' : '' }}">
                                    Videos
                                </a>
                                <a href="{{ route('admin.quizzes.index') }}"
                                   class="submenu-link {{ request()->routeIs('admin.quizzes.index') ? 'active' : '' }}">
                                    Quizzes
                                </a>
                                <a href="{{ route('admin.fun.index') }}"
                                   class="submenu-link {{ request()->routeIs('admin.fun.index') ? 'active' : '' }}">
                                    Fun Activities
                                </a>
                            </div>
                        </div>

                        <!-- Reports -->
                        <a href="{{ route('admin.reports.index') }}"
                           class="sidebar-link menu-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <svg style="width: 20px; height: 20px; margin-right: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Reports</span>
                        </a>
                    </nav>

                    <!-- Logout -->
                    <div style="position: absolute; bottom: 0; width: 256px; padding: 24px 16px; border-top: 1px solid #374151;">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="display: flex; align-items: center; width: 100%; padding: 12px 24px; color: #d1d5db; background: transparent; border: none; cursor: pointer;">
                                <svg style="width: 20px; height: 20px; margin-right: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </aside>

                <!-- Main Content -->
                <div style="margin-left: 256px; flex: 1;">
                    <!-- Top Navigation -->
                    <header style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="max-width: 1280px; margin: 0 auto; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center;">
                            <h2 style="font-size: 20px; font-weight: 600; color: #111827;">
                                @yield('page-title', 'Admin Dashboard')
                            </h2>

                            <!-- User Menu -->
                            <div style="display: flex; align-items: center;">
                                <span style="margin-right: 16px; color: #4b5563;">{{ Auth::user()->name ?? 'Admin' }}</span>
                                <img style="height: 32px; width: 32px; border-radius: 50%; background-color: #d1d5db;" src="{{ Auth::user()->image ?? '' }}" alt="">
                            </div>
                        </div>
                    </header>

                    <!-- Page Content -->
                    <main style="max-width: 1280px; margin: 0 auto; padding: 24px 32px;">
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>

        <script>
            function toggleSubmenu(id) {
                const submenu = document.getElementById(id);
                const chevron = submenu.previousElementSibling.querySelector('.chevron');

                if (submenu.classList.contains('show')) {
                    submenu.classList.remove('show');
                    chevron.classList.remove('rotate');
                } else {
                    submenu.classList.add('show');
                    chevron.classList.add('rotate');
                }
            }
        </script>
        @stack('scripts')
    </body>
</html>
