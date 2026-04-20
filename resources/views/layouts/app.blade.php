<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('dark') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AxiaOrto') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        
        /* Fallback Dark Mode Styles */
        .dark body { background-color: #020617 !important; color: #f1f5f9 !important; }
        .dark aside { background-color: #020617 !important; border-color: #0f172a !important; }
        .dark header { background-color: #0f172a !important; border-color: #1e293b !important; }
        .dark .bg-white { background-color: #0f172a !important; }
        .dark .text-slate-900 { color: #f8fafc !important; }
        .dark .text-slate-800 { color: #f1f5f9 !important; }
        .dark .border-slate-200 { border-color: #1e293b !important; }
        .dark input, .dark select { background-color: #1e293b !important; color: white !important; border-color: #334155 !important; }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-300" 
      x-data="{ 
        sidebarOpen: false, 
        sidebarMinimized: localStorage.getItem('sidebarMinimized') === 'true'
      }">
    <div class="min-h-screen flex relative overflow-hidden">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 md:hidden"
             @click="sidebarOpen = false"></div>

        <!-- Sidebar Navigation -->
        <aside :class="{
                 'translate-x-0': sidebarOpen,
                 '-translate-x-full': !sidebarOpen,
                 'w-72': !sidebarMinimized,
                 'w-20': sidebarMinimized
               }"
               class="fixed inset-y-0 left-0 bg-slate-900 dark:bg-slate-950 border-r border-slate-800 dark:border-slate-900 z-50 transform transition-all duration-300 ease-in-out md:translate-x-0 md:static md:inset-auto md:flex md:flex-col flex-shrink-0 shadow-2xl md:shadow-none">
            
            <div class="flex flex-col h-full overflow-y-auto overflow-x-hidden custom-scrollbar">
                <!-- Logo -->
                <div class="flex items-center h-20 flex-shrink-0 px-6 bg-slate-900 dark:bg-slate-950 border-b border-slate-800/50 overflow-hidden">
                    <div class="flex items-center space-x-3 min-w-max">
                        <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 p-2.5 rounded-xl shadow-lg shadow-indigo-500/20">
                            <i class="fa-solid fa-person-walking text-white text-xl"></i>
                        </div>
                        <div class="flex flex-col transition-all duration-300" :class="sidebarMinimized ? 'opacity-0 w-0' : 'opacity-100'">
                            <span class="text-white text-lg font-extrabold tracking-tight leading-none whitespace-nowrap">AxiaOrto</span>
                            <span class="text-indigo-500 text-[10px] font-bold uppercase tracking-[0.2em] mt-1 whitespace-nowrap">Prosthetic Clinic</span>
                        </div>
                    </div>
                    <!-- Close button for mobile -->
                    <button @click="sidebarOpen = false" class="ml-auto md:hidden text-slate-400 hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Nav Links -->
                <nav class="flex-1 px-4 py-6 space-y-7">
                    <!-- Dashboard -->
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" 
                           title="Dashboard"
                           class="group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <i class="fa-solid fa-grid-2 w-5 text-center text-lg flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-white' }}"></i>
                            <span class="ml-3 transition-all duration-300" :class="sidebarMinimized ? 'opacity-0 w-0 hidden' : 'opacity-100'">Dashboard</span>
                        </a>
                    </div>

                    <!-- Main Modules -->
                    <div class="space-y-2">
                        <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-4 transition-all duration-300" :class="sidebarMinimized ? 'opacity-0 h-0 overflow-hidden' : 'opacity-100'">Pemeriksaan & Medis</p>
                        
                        <a href="{{ route('patients.index') }}" 
                           title="Pasien"
                           class="group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('patients.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <i class="fa-solid fa-user-group w-5 text-center text-lg flex-shrink-0 {{ request()->routeIs('patients.*') ? 'text-white' : 'text-slate-500 group-hover:text-white' }}"></i>
                            <span class="ml-3 transition-all duration-300" :class="sidebarMinimized ? 'opacity-0 w-0 hidden' : 'opacity-100'">Pasien</span>
                        </a>

                        <a href="{{ route('consultations.index') }}" 
                           title="Konsultasi"
                           class="group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('consultations.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <i class="fa-solid fa-notes-medical w-5 text-center text-lg flex-shrink-0 {{ request()->routeIs('consultations.*') ? 'text-white' : 'text-slate-500 group-hover:text-white' }}"></i>
                            <span class="ml-3 transition-all duration-300" :class="sidebarMinimized ? 'opacity-0 w-0 hidden' : 'opacity-100'">Konsultasi</span>
                        </a>

                        <a href="{{ route('services.index') }}" 
                           title="Layanan"
                           class="group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('services.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <i class="fa-solid fa-hand-holding-heart w-5 text-center text-lg flex-shrink-0 {{ request()->routeIs('services.*') ? 'text-white' : 'text-slate-500 group-hover:text-white' }}"></i>
                            <span class="ml-3 transition-all duration-300" :class="sidebarMinimized ? 'opacity-0 w-0 hidden' : 'opacity-100'">Layanan</span>
                        </a>
                    </div>

                    <!-- Operations -->
                    <div class="space-y-2">
                        <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-4 transition-all duration-300" :class="sidebarMinimized ? 'opacity-0 h-0 overflow-hidden' : 'opacity-100'">Workshop & Keuangan</p>
                        
                        <a href="{{ route('treatment-orders.index') }}" 
                           title="Pesanan"
                           class="group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('treatment-orders.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <i class="fa-solid fa-file-medical w-5 text-center text-lg flex-shrink-0 {{ request()->routeIs('treatment-orders.*') ? 'text-white' : 'text-slate-500 group-hover:text-white' }}"></i>
                            <span class="ml-3 transition-all duration-300" :class="sidebarMinimized ? 'opacity-0 w-0 hidden' : 'opacity-100'">Pesanan</span>
                        </a>

                        <a href="{{ route('inventory.index') }}" 
                           title="Inventori"
                           class="group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('inventory.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <i class="fa-solid fa-boxes-stacked w-5 text-center text-lg flex-shrink-0 {{ request()->routeIs('inventory.*') ? 'text-white' : 'text-slate-500 group-hover:text-white' }}"></i>
                            <span class="ml-3 transition-all duration-300" :class="sidebarMinimized ? 'opacity-0 w-0 hidden' : 'opacity-100'">Inventori</span>
                        </a>

                        <a href="{{ route('payments.index') }}" 
                           title="Pembayaran"
                           class="group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('payments.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <i class="fa-solid fa-receipt w-5 text-center text-lg flex-shrink-0 {{ request()->routeIs('payments.*') ? 'text-white' : 'text-slate-500 group-hover:text-white' }}"></i>
                            <span class="ml-3 transition-all duration-300" :class="sidebarMinimized ? 'opacity-0 w-0 hidden' : 'opacity-100'">Pembayaran</span>
                        </a>
                    </div>
                </nav>

                <!-- User Profile Sidebar -->
                <div class="flex-shrink-0 flex bg-slate-900/50 p-4 border-t border-slate-800/50 overflow-hidden">
                    <div class="flex items-center min-w-max w-full px-2 py-2 rounded-xl hover:bg-slate-800/50 transition-colors cursor-pointer">
                        <img class="h-10 w-10 rounded-xl ring-2 ring-indigo-500/20 flex-shrink-0 object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=6366f1&color=fff" alt="">
                        <div class="ml-3 transition-all duration-300" :class="sidebarMinimized ? 'opacity-0 w-0 hidden' : 'opacity-100'">
                            <p class="text-sm font-bold text-white truncate max-w-[120px]">{{ auth()->user()->name ?? 'Guest' }}</p>
                            <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">{{ auth()->user()->role ?? 'Administrator' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex flex-col flex-1 min-w-0 bg-slate-50 dark:bg-slate-950 overflow-hidden transition-all duration-300">
            
            <!-- Topbar -->
            <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 h-20 flex items-center justify-between px-4 md:px-8 flex-shrink-0 z-40">
                <div class="flex items-center">
                    <!-- Mobile Hamburger -->
                    <button @click="sidebarOpen = true" type="button" class="md:hidden p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg mr-2 transition-colors">
                        <i class="fa-solid fa-bars-staggered text-xl"></i>
                    </button>
                    <!-- Desktop Minimize Toggle -->
                    <button @click="sidebarMinimized = !sidebarMinimized; localStorage.setItem('sidebarMinimized', sidebarMinimized)" 
                            type="button" class="hidden md:flex p-2.5 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl mr-4 transition-all">
                        <i class="fa-solid transition-transform duration-300" :class="sidebarMinimized ? 'fa-indent rotate-180' : 'fa-outdent'"></i>
                    </button>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">@yield('title')</h2>
                </div>

                <div class="flex items-center space-x-3 md:space-x-6">
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode; localStorage.setItem('dark', darkMode)" 
                            class="p-2.5 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all">
                        <i class="fa-solid" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 p-1 md:p-1.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <img class="h-8 w-8 rounded-lg shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=f1f5f9&color=6366f1" alt="">
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-cloak
                             class="absolute right-0 mt-2 w-56 rounded-2xl bg-white dark:bg-slate-800 shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700 divide-y divide-slate-100 dark:divide-slate-700 focus:outline-none py-1 z-[60]">
                            <div class="px-4 py-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Masuk sebagai</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->email ?? '' }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors font-medium">
                                    <i class="fa-solid fa-user-gear mr-3 text-slate-400 group-hover:text-indigo-600"></i> Profil Saya
                                </a>
                            </div>
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors font-bold">
                                        <i class="fa-solid fa-power-off mr-3 text-rose-400"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto">
                <div class="p-4 md:p-8 max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-8 right-8 flex items-center p-4 mb-4 text-white bg-emerald-500 rounded-2xl shadow-2xl shadow-emerald-500/20 z-[100] border border-white/20">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-emerald-500 bg-white rounded-lg">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="ml-3 text-sm font-medium">{{ session('success') }}</div>
            <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 p-1.5 text-white/50 hover:text-white rounded-lg inline-flex h-8 w-8">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-8 right-8 flex items-center p-4 mb-4 text-white bg-rose-500 rounded-2xl shadow-2xl shadow-rose-500/20 z-[100] border border-white/20">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-rose-500 bg-white rounded-lg">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div class="ml-3 text-sm font-medium">{{ session('error') }}</div>
            <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 p-1.5 text-white/50 hover:text-white rounded-lg inline-flex h-8 w-8">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @livewireScripts
    @stack('scripts')

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal', (event) => {
                const data = event[0];
                Swal.fire({
                    icon: data.icon || 'success',
                    title: data.title || 'Berhasil',
                    text: data.text || '',
                    toast: data.toast || false,
                    position: data.toast ? 'top-end' : 'center',
                    showConfirmButton: !data.toast,
                    timer: data.toast ? 3000 : null,
                    timerProgressBar: data.toast,
                    background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b',
                    iconColor: data.icon === 'error' ? '#ef4444' : '#6366f1',
                });
            });
        });

        // Handle Session Flash Messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b',
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: "{{ session('error') }}",
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b',
            });
        @endif
    </script>
</body>
</html>
