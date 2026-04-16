<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <div class="px-3 py-2 in-data-flux-sidebar-collapsed-desktop:hidden">
                    <div class="text-sm font-medium leading-none text-zinc-400">{{ __('Platform') }}</div>
                </div>

                <div class="grid space-y-[2px]">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    @if(auth()->user()->role->value === 'faculty')
                        <flux:sidebar.item icon="book-open" :href="route('faculty.courses')" :current="request()->routeIs('faculty.courses') || request()->routeIs('faculty.course-player')" wire:navigate>
                            {{ __('My Courses') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="academic-cap" :href="route('faculty.certificates')" :current="request()->routeIs('faculty.certificates')" wire:navigate>
                            {{ __('Certificates') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="fire" :href="route('faculty.streaks')" :current="request()->routeIs('faculty.streaks')" wire:navigate>
                            {{ __('Streaks') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="cog-6-tooth" :href="route('faculty.suggestions')" :current="request()->routeIs('faculty.suggestions')" wire:navigate>
                            {{ __('Suggestions') }}
                        </flux:sidebar.item>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <flux:sidebar.item icon="users" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate>
                            {{ __('Users') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="academic-cap" :href="route('admin.courses.index')" :current="request()->routeIs('admin.courses.*')" wire:navigate>
                            {{ __('Courses') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="user-group" :href="route('admin.enrollments.index')" :current="request()->routeIs('admin.enrollments.*')" wire:navigate>
                            {{ __('Enrollments') }}
                        </flux:sidebar.item>
                    @endif
                </div>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <!-- <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item> -->

                <flux:sidebar.item icon="moon" x-data x-on:click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'" class="cursor-pointer">
                    <span x-show="$flux.appearance === 'light' || $flux.appearance === 'system'">{{ __('Dark mode') }}</span>
                    <span x-show="$flux.appearance === 'dark'">{{ __('Light mode') }}</span>
                </flux:sidebar.item>

                <!-- <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item> -->
            </flux:sidebar.nav>

            @if(auth()->user()->role->value === 'faculty')
                @php
                    $xpRecord = \App\Models\Xp::find(auth()->id());
                    $streakRecord = \App\Models\Streak::where('user_id', auth()->id())->orderBy('date', 'desc')->first();
                    $xp = $xpRecord?->xp ?? 0;
                    $streak = $streakRecord?->count ?? 0;
                    $level = (int) floor($xp / 500) + 1;
                    $progress = ($xp % 500) / 5;
                @endphp
                <div class="px-4 py-3 mx-2 mb-2 rounded-xl bg-zinc-100 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700/50 hidden lg:block">
                    <div class="flex items-center gap-2 mb-2">
                        <flux:icon.fire class="h-4 w-4 text-orange-500" />
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $streak }} Day Streak</span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 mb-2">
                        <span>{{ $xp }} XP</span>
                        <span>Level {{ $level }}</span>
                    </div>
                    <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            @endif

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
