<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AxiaOrto ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 antialiased">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-5xl w-full bg-white rounded-[40px] shadow-2xl shadow-indigo-500/10 flex overflow-hidden border border-slate-100">
            <!-- Left: Form -->
            <div class="flex-1 p-12 md:p-16 lg:p-20">
                <div class="mb-12">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="h-10 w-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/40">
                            <i class="fa-solid fa-tooth text-white"></i>
                        </div>
                        <span class="text-2xl font-extrabold text-slate-900 tracking-tight">AxiaOrto<span class="text-indigo-600">.</span></span>
                    </div>
                    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Selamat Datang.</h1>
                    <p class="text-slate-500 font-medium">Silakan masuk untuk mengelola klinik Anda.</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium text-slate-900" placeholder="nama@email.com">
                        </div>
                        @error('email') <p class="text-xs text-rose-500 mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between ml-1">
                            <label class="text-sm font-bold text-slate-700">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors">Lupa Password?</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </div>
                            <input type="password" name="password" required class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium text-slate-900" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center ml-1">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="peer hidden">
                            <div class="h-5 w-5 border-2 border-slate-200 rounded-md peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center">
                                <i class="fa-solid fa-check text-[10px] text-white opacity-0 peer-checked:opacity-100"></i>
                            </div>
                            <span class="ml-3 text-sm font-semibold text-slate-500 group-hover:text-slate-700 transition-colors">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg hover:bg-indigo-700 shadow-xl shadow-indigo-500/30 transition-all transform active:scale-[0.98]">
                        Masuk Sekarang
                    </button>
                </form>

                <div class="mt-12 text-center">
                    <p class="text-slate-500 text-sm font-medium">Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-600 font-bold hover:underline">Daftar di sini</a></p>
                </div>
            </div>

            <!-- Right: Illustration/Image -->
            <div class="hidden lg:flex flex-1 bg-indigo-600 relative overflow-hidden items-center justify-center p-20 text-white">
                <div class="absolute top-0 left-0 w-full h-full opacity-10">
                    <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
                    <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-white rounded-full translate-x-1/3 translate-y-1/3 blur-3xl"></div>
                </div>
                
                <div class="relative z-10 text-center">
                    <div class="mb-10 inline-flex items-center justify-center h-20 w-20 bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl">
                        <i class="fa-solid fa-shield-halved text-4xl"></i>
                    </div>
                    <h2 class="text-4xl font-extrabold leading-tight mb-6">Kelola Klinik dengan Lebih Cerdas & Cepat.</h2>
                    <p class="text-indigo-100 text-lg font-medium opacity-80 leading-relaxed">Platform terintegrasi untuk pendaftaran pasien, rekam medis, hingga manajemen workshop orthotic prosthetic.</p>
                </div>
                
                <div class="absolute bottom-10 left-10 right-10 flex justify-between items-center text-xs font-bold text-indigo-300 tracking-widest uppercase">
                    <span>AxiaOrto v2.0</span>
                    <span>© 2024</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>