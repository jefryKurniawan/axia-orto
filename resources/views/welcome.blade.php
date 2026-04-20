<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Axia Orto - Klinik Ortotik Prostetik</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Scripts / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .parallax-wrapper {
            perspective: 10px;
            height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Marquee Animation */
        .marquee-content {
            animation: marquee 20s linear infinite;
        }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Custom Gradients */
        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gradient-primary {
            background-image: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        }
        .dark .gradient-primary {
            background-image: linear-gradient(135deg, #60a5fa 0%, #c084fc 100%);
        }

        /* Hide Scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 antialiased selection:bg-blue-500 selection:text-white transition-colors duration-300">

    <!-- Navbar -->
    <nav x-data="{ scrolled: false, mobileMenuOpen: false }" @scroll.window="scrolled = (window.pageYOffset > 20)" 
         :class="scrolled ? 'glass shadow-lg dark:shadow-blue-900/10' : 'bg-transparent'" 
         class="fixed w-full z-50 transition-all duration-300 border-b border-transparent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="#" class="text-2xl font-extrabold tracking-tighter">
                        <span :class="scrolled ? 'text-slate-900 dark:text-white' : 'text-white'">Axia</span><span class="text-transparent bg-clip-text gradient-primary">Orto</span>
                    </a>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-1 items-center backdrop-blur-md px-2 py-1.5 rounded-full border shadow-sm transition-colors duration-300"
                     :class="scrolled ? 'bg-white/80 dark:bg-slate-900/80 border-gray-200 dark:border-slate-700' : 'bg-white/10 border-white/20'">
                    <div class="relative group">
                        <button class="px-4 py-2 rounded-full text-sm font-bold transition-all flex items-center"
                                :class="scrolled ? 'text-slate-800 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800' : 'text-white hover:bg-white/20'">
                            Layanan
                            <svg class="w-4 h-4 ml-1 opacity-70 group-hover:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <!-- Dropdown -->
                        <div class="absolute left-0 mt-2 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-left scale-95 group-hover:scale-100">
                            <div class="bg-white dark:bg-slate-900 rounded-2xl p-2 shadow-xl border border-gray-200 dark:border-slate-700">
                                <a href="#layanan" class="block px-4 py-3 rounded-xl text-sm font-bold text-slate-800 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Kaki & Tangan Palsu</a>
                                <a href="#layanan" class="block px-4 py-3 rounded-xl text-sm font-bold text-slate-800 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Alat Bantu (Ortosis)</a>
                                <a href="#layanan" class="block px-4 py-3 rounded-xl text-sm font-bold text-slate-800 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Konsultasi Medis</a>
                            </div>
                        </div>
                    </div>
                    <a href="#galeri" class="px-4 py-2 rounded-full text-sm font-bold transition-all"
                       :class="scrolled ? 'text-slate-800 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800' : 'text-white hover:bg-white/20'">Galeri</a>
                    <a href="#faq" class="px-4 py-2 rounded-full text-sm font-bold transition-all"
                       :class="scrolled ? 'text-slate-800 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800' : 'text-white hover:bg-white/20'">FAQ</a>
                    <a href="#lokasi" class="px-4 py-2 rounded-full text-sm font-bold transition-all"
                       :class="scrolled ? 'text-slate-800 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800' : 'text-white hover:bg-white/20'">Lokasi</a>
                </div>

                <!-- Right Actions -->
                <div class="hidden md:flex items-center space-x-4">
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode" class="p-2.5 rounded-full shadow-sm border transition-colors"
                            :class="scrolled ? 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700 border-gray-200 dark:border-slate-700' : 'bg-white/10 text-white border-white/20 hover:bg-white/20'">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        <svg x-show="darkMode" style="display: none;" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </button>
                    
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 rounded-full text-sm font-bold text-white gradient-primary hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5 transition-all">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-full text-sm font-bold text-white gradient-primary hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5 transition-all">Masuk</a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center gap-2">
                    <button @click="darkMode = !darkMode" class="p-2 rounded-full shadow-sm border"
                            :class="scrolled ? 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-gray-200 dark:border-slate-700' : 'bg-white/10 text-white border-white/20'">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        <svg x-show="darkMode" style="display: none;" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2" :class="scrolled ? 'text-slate-900 dark:text-white' : 'text-white'">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen" style="display: none;" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Dropdown -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800 shadow-xl">
            <div class="px-4 pt-4 pb-8 space-y-2">
                <a href="#layanan" class="block px-4 py-3 rounded-xl text-base font-bold text-slate-800 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800">Layanan</a>
                <a href="#galeri" class="block px-4 py-3 rounded-xl text-base font-bold text-slate-800 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800">Galeri</a>
                <a href="#faq" class="block px-4 py-3 rounded-xl text-base font-bold text-slate-800 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800">FAQ</a>
                <a href="#lokasi" class="block px-4 py-3 rounded-xl text-base font-bold text-slate-800 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800">Lokasi</a>
                <div class="pt-4 px-4">
                    <a href="{{ route('login') }}" class="block w-full text-center px-6 py-3 rounded-full text-base font-bold text-white gradient-primary">Masuk / Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Parallax Wrapper for Main Content -->
    <main x-data="{ scrollY: 0 }" @scroll.window="scrollY = window.scrollY" class="relative overflow-hidden">
        
        <!-- Hero Section -->
        <section class="relative min-h-[100svh] flex items-center justify-center pt-20 overflow-hidden bg-slate-900">
            <!-- Dynamic Background -->
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1584515933487-779824d29309?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                     alt="Background" 
                     class="w-full h-full object-cover opacity-50 dark:opacity-30"
                     :style="`transform: translateY(${scrollY * 0.5}px) scale(1.1);`">
                <!-- Gradients overlay -->
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-900/60 to-gray-50 dark:to-slate-950"></div>
                <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-[128px] opacity-50 animate-blob"></div>
                <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-[128px] opacity-50 animate-blob animation-delay-2000"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center" 
                 :style="`transform: translateY(${scrollY * -0.2}px); opacity: ${1 - scrollY/700}`">
                
                <div data-aos="zoom-out" data-aos-duration="1000">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass text-sm font-bold text-white mb-8 border border-white/20 shadow-[0_0_15px_rgba(59,130,246,0.5)]">
                        <span class="relative flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        Klinik Spesialis Ortotik Prostetik
                    </span>
                </div>
                
                <h1 class="text-5xl sm:text-7xl lg:text-8xl font-extrabold tracking-tighter text-white mb-8 leading-[1.1]" data-aos="fade-up" data-aos-delay="100">
                    Bebas Bergerak <br>
                    <span class="text-transparent bg-clip-text gradient-primary">Tanpa Batas.</span>
                </h1>
                
                <p class="mt-8 text-xl sm:text-2xl text-slate-200 max-w-3xl mx-auto mb-12 font-medium" data-aos="fade-up" data-aos-delay="200">
                    Kembalikan kemandirian Anda dengan prostesis & ortosis presisi tinggi. Dirancang khusus untuk kenyamanan dan gaya hidup modern.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4 sm:gap-6 mt-12" data-aos="fade-up" data-aos-delay="300">
                    <a href="#layanan" class="group relative px-8 py-4 rounded-full text-lg font-bold text-white gradient-primary overflow-hidden shadow-[0_0_30px_rgba(59,130,246,0.3)] hover:shadow-[0_0_40px_rgba(59,130,246,0.6)] transition-all hover:-translate-y-1">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            Mulai Perjalanan Anda
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Infinite Marquee -->
        <div class="bg-blue-600 dark:bg-blue-900 text-white py-4 overflow-hidden relative flex border-y border-blue-500 dark:border-blue-800" :style="`transform: translateX(${scrollY * -0.1}px)`">
            <div class="flex whitespace-nowrap marquee-content items-center text-xl font-bold uppercase tracking-widest">
                <span class="mx-8">• PENGUKURAN PRESISI 3D</span>
                <span class="mx-8">• MATERIAL RINGAN & KUAT</span>
                <span class="mx-8">• DESAIN ERGONOMIS</span>
                <span class="mx-8">• KONSULTASI GRATIS</span>
                <span class="mx-8">• TEKNOLOGI TERKINI</span>
                <span class="mx-8">• PENGUKURAN PRESISI 3D</span>
                <span class="mx-8">• MATERIAL RINGAN & KUAT</span>
                <span class="mx-8">• DESAIN ERGONOMIS</span>
                <span class="mx-8">• KONSULTASI GRATIS</span>
                <span class="mx-8">• TEKNOLOGI TERKINI</span>
            </div>
        </div>

        <!-- Bento Grid Services -->
        <section id="layanan" class="py-40 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-24" data-aos="fade-up">
                    <h2 class="text-sm font-bold text-blue-600 dark:text-blue-400 tracking-widest uppercase mb-4">Layanan Kami</h2>
                    <h3 class="text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white">Dibuat khusus untuk Anda.</h3>
                </div>

                <!-- Bento Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    
                    <!-- Large Feature: Kaki Palsu -->
                    <div class="md:col-span-2 relative rounded-3xl overflow-hidden group bg-white dark:bg-slate-900 shadow-2xl border border-gray-200 dark:border-slate-800" data-aos="fade-up">
                        <div class="absolute inset-0">
                            <img src="https://images.unsplash.com/photo-1555505019-8c3f1c4aba5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Kaki Palsu" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        </div>
                        <div class="relative h-full min-h-[450px] p-8 md:p-12 flex flex-col justify-end">
                            <div class="bg-slate-900/80 backdrop-blur-md p-8 rounded-2xl border border-white/10 w-full md:w-3/4 shadow-2xl">
                                <div class="mb-4 inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-600 text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <h4 class="text-3xl font-extrabold text-white mb-4">Prostesis Bawah & Atas</h4>
                                <p class="text-slate-200 text-lg leading-relaxed">Kaki dan tangan tiruan modern. Menggunakan soket yang nyaman dan sendi hidrolik/mekanik canggih untuk pergerakan alami.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Small Feature 1: Ortosis -->
                    <div class="relative rounded-3xl overflow-hidden group bg-white dark:bg-slate-900 shadow-2xl border border-gray-200 dark:border-slate-800" data-aos="fade-up" data-aos-delay="100">
                        <div class="absolute inset-0">
                            <img src="https://images.unsplash.com/photo-1584432810601-6c7f27d2362b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Ortosis" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        </div>
                        <div class="relative h-full min-h-[450px] md:min-h-0 p-8 flex flex-col justify-end text-white">
                            <div class="bg-slate-900/80 backdrop-blur-md p-6 rounded-2xl border border-white/10 shadow-2xl w-full">
                                <h4 class="text-2xl font-extrabold text-white mb-3">Ortosis (Alat Bantu)</h4>
                                <p class="text-slate-200 font-medium leading-relaxed">AFO, KAFO, Korset skoliosis, dan sepatu koreksi ortopedi untuk memperbaiki postur.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Small Feature 2: Konsultasi -->
                    <div class="relative rounded-3xl overflow-hidden group bg-white dark:bg-slate-900 shadow-2xl border border-gray-200 dark:border-slate-800 p-10" data-aos="fade-up" data-aos-delay="200">
                        <div class="relative h-full flex flex-col justify-center">
                            <div class="mb-8 w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-sm">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                            </div>
                            <h4 class="text-3xl font-extrabold text-slate-900 dark:text-white mb-4">Asesmen Klinis</h4>
                            <p class="text-slate-600 dark:text-slate-400 mb-8 text-lg font-medium">Konsultasi komprehensif bersama ahli Prostetik & Ortotik untuk solusi paling akurat.</p>
                            <a href="#lokasi" class="inline-flex items-center text-base font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                Buat Janji Temu <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Wide Feature: Material -->
                    <div class="md:col-span-2 relative rounded-3xl overflow-hidden bg-slate-900 text-white shadow-2xl p-10 md:p-14 border border-slate-800" data-aos="fade-up" data-aos-delay="300">
                        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-500 via-slate-900 to-slate-900"></div>
                        <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                            <div class="flex-1">
                                <h4 class="text-4xl font-extrabold mb-6">Material Premium & Kuat</h4>
                                <p class="text-slate-300 text-xl mb-8 leading-relaxed">Kami menggunakan serat karbon, resin impor, dan silikon medis yang ringan namun tahan beban berat untuk aktivitas ekstrem sekalipun.</p>
                                <div class="flex flex-wrap gap-4">
                                    <span class="px-5 py-2.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-sm font-bold tracking-wider">Carbon Fiber</span>
                                    <span class="px-5 py-2.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-sm font-bold tracking-wider">Medical Silicone</span>
                                    <span class="px-5 py-2.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-sm font-bold tracking-wider">Titanium Joints</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Dynamic Expanded Carousel -->
        <section id="galeri" class="py-40 bg-white dark:bg-slate-950 border-y border-gray-100 dark:border-slate-900" x-data="{ 
            active: 0,
            items: [
                { img: 'https://images.unsplash.com/photo-1574007557106-a979eb118c7c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80', title: 'Workshop Modern', desc: 'Fasilitas produksi canggih untuk hasil presisi.' },
                { img: 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80', title: 'Casting & Modifikasi', desc: 'Pengukuran akurat menyesuaikan anatomi stump pasien.' },
                { img: 'https://images.unsplash.com/photo-1551076805-e1869033e561?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80', title: 'Sesi Fitting Terpandu', desc: 'Pelatihan jalan dan adaptasi penggunaan alat bantu.' },
                { img: 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80', title: 'Ruang Klinik Nyaman', desc: 'Privasi dan kenyamanan pasien adalah prioritas utama kami.' },
                { img: 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80', title: 'Detail Ortosis', desc: 'Pembuatan AFO (Ankle Foot Orthosis) custom untuk terapi kaki.' },
                { img: 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80', title: 'Hasil Akhir Estetis', desc: 'Cosmesis (cover) yang menyerupai warna dan tekstur kulit asli.' },
                { img: 'https://images.unsplash.com/photo-1584515933487-779824d29309?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80', title: 'Prostesis Olahraga', desc: 'Kaki palsu berbahan karbon untuk aktivitas lari dan olahraga.' },
                { img: 'https://images.unsplash.com/photo-1516549655169-df83a0774514?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80', title: 'Konsultasi Medis', desc: 'Diskusi komprehensif untuk menentukan alat bantu terbaik.' }
            ],
            next() { this.active = this.active === this.items.length - 1 ? 0 : this.active + 1 },
            prev() { this.active = this.active === 0 ? this.items.length - 1 : this.active - 1 }
        }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-sm font-bold text-blue-600 dark:text-blue-400 tracking-widest uppercase mb-4" data-aos="fade-up">Portofolio</h2>
                    <h3 class="text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white" data-aos="fade-up" data-aos-delay="100">Galeri Kerja Kami</h3>
                </div>

                <!-- Big Carousel Container -->
                <div class="relative w-full h-[700px] rounded-[2.5rem] overflow-hidden shadow-2xl group" data-aos="zoom-in" data-aos-delay="200">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="absolute inset-0 transition-all duration-700 ease-in-out"
                             :class="active === index ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 z-0'">
                            <img :src="item.img" :alt="item.title" class="w-full h-full object-cover">
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/30 to-transparent"></div>
                            
                            <div class="absolute bottom-0 left-0 p-10 md:p-20 w-full max-w-4xl">
                                <span class="inline-block px-4 py-2 mb-6 text-sm font-bold uppercase tracking-widest text-white bg-black/40 backdrop-blur-md rounded-full border border-white/20" x-text="`0${index + 1} / 0${items.length}`"></span>
                                <h3 class="text-4xl md:text-6xl font-extrabold text-white mb-6 drop-shadow-lg" x-text="item.title"></h3>
                                <p class="text-xl md:text-2xl text-slate-200 font-medium drop-shadow-md" x-text="item.desc"></p>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Left/Right Buttons Inside Image -->
                    <button @click="prev()" class="absolute left-6 top-1/2 transform -translate-y-1/2 w-14 h-14 rounded-full bg-white/20 backdrop-blur-md border border-white/40 flex items-center justify-center text-white hover:bg-white hover:text-blue-600 transition-all z-20 opacity-0 group-hover:opacity-100 shadow-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button @click="next()" class="absolute right-6 top-1/2 transform -translate-y-1/2 w-14 h-14 rounded-full bg-white/20 backdrop-blur-md border border-white/40 flex items-center justify-center text-white hover:bg-white hover:text-blue-600 transition-all z-20 opacity-0 group-hover:opacity-100 shadow-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
                
                <!-- Thumbnails Strip -->
                <div class="flex justify-center gap-4 mt-10 overflow-x-auto pb-6 pt-2 no-scrollbar px-4" data-aos="fade-up" data-aos-delay="300">
                    <template x-for="(item, index) in items" :key="index">
                        <button @click="active = index" class="relative flex-shrink-0 w-36 h-24 rounded-2xl overflow-hidden border-4 transition-all duration-300"
                                :class="active === index ? 'border-blue-500 shadow-[0_0_20px_rgba(59,130,246,0.4)] scale-110 z-10' : 'border-transparent opacity-60 hover:opacity-100 hover:scale-105'">
                            <img :src="item.img" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>
        </section>

        <!-- Expanded FAQ -->
        <section id="faq" class="py-40 bg-gray-50 dark:bg-slate-900" x-data="{ selected: null }">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-24" data-aos="fade-up">
                    <h2 class="text-sm font-bold text-blue-600 dark:text-blue-400 tracking-widest uppercase mb-4">FAQ</h2>
                    <h3 class="text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white">Pertanyaan Sering Diajukan</h3>
                </div>

                <div class="space-y-6">
                    @php
                        $faqs = [
                            ['q' => 'Berapa lama proses pembuatan kaki/tangan palsu?', 'a' => 'Proses pembuatan memakan waktu rata-rata 1 hingga 3 minggu. Ini mencakup proses pengukuran (casting), pembuatan cetakan positif, fabrikasi soket, hingga tahap fitting (pengepasan) dan penyelarasan akhir (alignment).'],
                            ['q' => 'Apakah ada jaminan atau garansi untuk alat bantu yang dibuat?', 'a' => 'Tentu. Kami memberikan garansi servis dan perbaikan komponen selama jangka waktu tertentu (tergantung jenis material). Kami juga memberikan sesi fitting lanjutan secara gratis untuk memastikan soket benar-benar nyaman dipakai sehari-hari.'],
                            ['q' => 'Berapa perkiraan biaya pembuatan prostesis/ortosis?', 'a' => 'Biaya sangat bervariasi bergantung pada jenis amputasi, tingkat aktivitas pengguna, dan komponen yang dipilih (lokal atau impor seperti Ottobock, Ossur, dll). Silakan hubungi kami untuk konsultasi awal agar kami bisa memberikan estimasi harga yang akurat.'],
                            ['q' => 'Apakah Axia Orto menerima pesanan dari luar kota Magetan?', 'a' => 'Ya, kami melayani pasien dari seluruh Indonesia. Untuk pasien luar kota, kami dapat menjadwalkan sesi pengukuran yang padat, dan jika diperlukan, kami bisa merekomendasikan penginapan terdekat selama masa fitting.'],
                            ['q' => 'Bagaimana cara merawat kaki palsu agar awet?', 'a' => 'Bersihkan bagian dalam soket setiap hari dengan lap lembap dan sabun ringan, keringkan dengan baik. Ganti kaos kaki stump (silicone/gel liner) secara rutin. Hindari merendam komponen sendi mekanik ke dalam air kecuali jenisnya memang didesain tahan air (waterproof).'],
                            ['q' => 'Apakah saya bisa berlari menggunakan kaki palsu dari Axia Orto?', 'a' => 'Bisa! Jika Anda memiliki gaya hidup aktif, kami dapat merancang kaki palsu dengan komponen khusus olahraga (sport blade) atau telapak kaki berbahan karbon (carbon foot) yang memiliki daya pantul energi tinggi (energy return).'],
                        ];
                    @endphp

                    @foreach($faqs as $index => $faq)
                    <div class="bg-white dark:bg-slate-950 border border-gray-200 dark:border-slate-800 rounded-3xl overflow-hidden transition-all duration-300 shadow-sm" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                        <button @click="selected !== {{ $index }} ? selected = {{ $index }} : selected = null" class="flex justify-between items-center w-full px-10 py-8 text-left focus:outline-none bg-white dark:bg-slate-950 hover:bg-gray-50 dark:hover:bg-slate-900 transition-colors">
                            <span class="font-extrabold text-xl text-slate-900 dark:text-slate-100 pr-6">{{ $faq['q'] }}</span>
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-50 dark:bg-slate-800 flex items-center justify-center text-blue-600 dark:text-blue-400 transition-transform duration-300 shadow-sm" :class="selected === {{ $index }} ? 'rotate-180 bg-blue-600 text-white dark:bg-blue-600 dark:text-white' : ''">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </button>
                        <div class="overflow-hidden transition-all duration-500 ease-in-out bg-gray-50 dark:bg-slate-900/50 max-h-0" :style="selected === {{ $index }} ? 'max-height: 400px;' : 'max-height: 0;'">
                            <div class="px-10 pb-8 pt-6 text-slate-600 dark:text-slate-300 text-lg font-medium leading-relaxed border-t border-gray-100 dark:border-slate-800">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Symmetric Footer & Location -->
        <footer id="lokasi" class="bg-slate-950 text-white pt-40 pb-16 border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Symmetric Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-24 items-center">
                    
                    <!-- Left: Contact & CTA -->
                    <div class="flex flex-col justify-center text-center lg:text-left" data-aos="fade-right">
                        <h2 class="text-5xl md:text-6xl font-extrabold mb-8 leading-tight">Kunjungi Klinik <br><span class="text-blue-500">Axia Orto</span></h2>
                        <p class="text-slate-400 text-xl mb-12 max-w-xl mx-auto lg:mx-0 leading-relaxed">Percayakan mobilitas Anda pada ahli yang tersertifikasi. Kami siap membantu merancang alat bantu yang paling sesuai untuk Anda.</p>
                        
                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-6 mb-16">
                            <!-- Symmetrical WA Button (Restored proper sizing) -->
                            <a href="https://wa.me/6285816375213" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-4 bg-green-500 hover:bg-green-600 text-white rounded-full font-bold text-lg transition-all shadow-[0_0_20px_rgba(34,197,94,0.3)] hover:shadow-[0_0_30px_rgba(34,197,94,0.5)] hover:-translate-y-1">
                                <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.668.598 1.216.774 1.391.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.082 21.226c-1.396 0-2.738-.346-3.953-1.002l-4.41 1.157 1.183-4.296c-.722-1.258-1.106-2.709-1.106-4.22 0-4.662 3.791-8.452 8.454-8.452 4.661 0 8.451 3.79 8.451 8.452 0 4.661-3.79 8.451-8.451 8.451z"/></svg>
                                Hubungi via WhatsApp
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 text-left max-w-xl mx-auto lg:mx-0 bg-slate-900 p-8 rounded-3xl border border-slate-800">
                            <div>
                                <h4 class="text-blue-500 text-sm font-bold uppercase tracking-wider mb-3">Alamat</h4>
                                <p class="text-white font-semibold text-lg leading-relaxed">Jl. Raya Gonggang Bulukerto, Tawang, Janggan, Kec. Poncol, Magetan, Jawa Timur 63362</p>
                            </div>
                            <div>
                                <h4 class="text-blue-500 text-sm font-bold uppercase tracking-wider mb-3">Jam Buka</h4>
                                <p class="text-white font-semibold text-lg leading-relaxed">Senin - Sabtu:<br>08.00 - 16.00 WIB<br><br><span class="text-red-400">Minggu: Tutup</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Map -->
                    <div class="relative h-[600px] w-full rounded-[2.5rem] overflow-hidden bg-slate-900 border border-slate-800 p-3 shadow-2xl" data-aos="fade-left">
                        <div class="w-full h-full rounded-[2rem] overflow-hidden relative bg-slate-800">
                            <!-- Fixed Map Rendering URL -->
                            <iframe src="https://maps.google.com/maps?q=Axia+Orto+Klinik+Ortotik+Prostetik+Magetan&t=&z=15&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="absolute inset-0 z-10 filter dark:opacity-80"></iframe>
                            
                            <!-- Kept only one button, floating nicely at bottom right -->
                            <a href="https://maps.app.goo.gl/cjaJ78D9DdhbASdF9" target="_blank" class="absolute bottom-6 right-6 px-8 py-4 bg-blue-600 text-white text-base font-bold rounded-full shadow-2xl hover:bg-blue-700 transition-colors flex items-center gap-3 z-20 hover:-translate-y-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                Buka di Maps
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bottom Footer -->
                <div class="pt-10 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6">
                    <p class="text-slate-500 text-base font-semibold">© {{ date('Y') }} Axia Orto. Hak cipta dilindungi undang-undang.</p>
                    <div class="flex space-x-8">
                        <a href="#" class="text-slate-500 hover:text-white transition-colors">
                            <span class="sr-only">Instagram</span>
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path></svg>
                        </a>
                        <a href="#" class="text-slate-500 hover:text-white transition-colors">
                            <span class="sr-only">Facebook</span>
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>

    </main>

    <!-- Initialize AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                once: true,
                offset: 50,
                duration: 800,
                easing: 'ease-out-cubic',
            });
            // Update AOS on scroll due to custom layout
            window.addEventListener('scroll', () => {
                AOS.refresh();
            });
        });
    </script>
</body>
</html>