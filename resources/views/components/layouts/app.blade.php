<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TechTrack - IT Worklog & Schedule System' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 font-sans antialiased">
    <x-mary-toast />

    <x-mary-nav sticky class="bg-base-100 shadow-md">
        <x-slot:brand>
            <div class="flex items-center gap-2 font-bold text-xl text-primary">
                <x-mary-icon name="o-cpu-chip" class="w-7 h-7 text-primary" />
                <span>TechTrack</span>
            </div>
        </x-slot:brand>
        <x-slot:actions>
            <x-mary-theme-toggle class="btn btn-circle btn-ghost btn-sm" />
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar placeholder">
                    @if(auth()->user()->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->avatar))
                        <div class="rounded-full overflow-hidden shadow-inner border border-base-content/10" style="width: 40px; height: 40px; min-width: 40px; min-height: 40px;">
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" style="width: 40px; height: 40px; object-fit: cover;" />
                        </div>
                    @else
                        <div class="bg-neutral text-neutral-content rounded-full flex items-center justify-center text-center font-bold text-sm leading-none shadow-inner select-none" style="width: 40px; height: 40px; min-width: 40px; min-height: 40px;">
                            <span class="inline-flex items-center justify-center text-center w-full h-full select-none">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</span>
                        </div>
                    @endif
                </div>
                <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-56">
                    <li class="menu-title text-xs opacity-60">
                        <div class="font-bold text-sm text-base-content">{{ auth()->user()->name ?? 'User' }}</div>
                        <span class="badge badge-primary badge-xs uppercase font-semibold">{{ auth()->user()->role ?? 'USER' }}</span>
                    </li>
                    <li class="border-t border-base-content/10 mt-1 pt-1">
                        <a href="{{ route('profile') }}" class="flex items-center gap-2 py-2">
                            <x-mary-icon name="o-user-circle" class="w-4 h-4 text-primary" />
                            <span>Edit Profil & Foto</span>
                        </a>
                    </li>
                    <li class="border-t border-base-content/10 mt-1 pt-1">
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                            @csrf
                        </form>
                        <a href="{{ route('logout') }}" data-navigate-ignore="true" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-error flex items-center gap-2 py-2 cursor-pointer">
                            <x-mary-icon name="o-arrow-right-start-on-rectangle" class="w-4 h-4" />
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </x-slot:actions>
    </x-mary-nav>

    <x-mary-main full-width>
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 shadow-md">
            <x-mary-menu activate-by-route>
                <x-mary-menu-item title="Dashboard" icon="o-home" route="dashboard" />
                <x-mary-menu-item title="Work Logs" icon="o-document-text" route="work-logs" />
                <x-mary-menu-item title="Routine Schedules" icon="o-calendar" route="routine-schedules" />
                <x-mary-menu-item title="PC Maintenance" icon="o-computer-desktop" route="pc-maintenance" />

                @if(in_array(auth()->user()->role ?? '', ['admin', 'it_lead']))
                    <x-mary-menu-sub title="Master Data" icon="o-cog-6-tooth">
                        <x-mary-menu-item title="Departments" icon="o-building-office" route="departments" />
                        <x-mary-menu-item title="Categories" icon="o-tag" route="categories" />
                        <x-mary-menu-item title="Users" icon="o-users" route="users" />
                    </x-mary-menu-sub>
                @endif
            </x-mary-menu>
        </x-slot:sidebar>

        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-mary-main>
</body>
</html>