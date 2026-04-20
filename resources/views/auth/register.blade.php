<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - AxiaOrto ERP</title>
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
            <!-- Left: Illustration -->
            <div class="hidden lg:flex flex-1 bg-emerald-600 relative overflow-hidden items-center justify-center p-20 text-white">
                <div class="absolute top-0 left-0 w-full h-full opacity-10">
                    <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
                </div>
                
                <div class="relative z-10 text-center">
                    <div class="mb-10 inline-flex items-center justify-center h-20 w-20 bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl">
                        <i class="fa-solid fa-users text-4xl"></i>
                    </div>
                    <h2 class="text-4xl font-extrabold leading-tight mb-6">Bergabunglah dengan Ekosistem Medis Kami.</h2>
                    <p class="text-emerald-100 text-lg font-medium opacity-80 leading-relaxed">Dapatkan kemudahan dalam mengelola data pasien dan inventory dalam satu dashboard yang intuitif.</p>
                </div>
            </div>

            <!-- Right: Form -->
            <div class="flex-1 p-12 md:p-16">
                <div class="mb-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-8 w-8 bg-emerald-600 rounded-lg flex items-center justify-center shadow-lg shadow-emerald-500/40">
                            <i class="fa-solid fa-plus text-white text-xs"></i>
                        </div>
                        <span class="text-xl font-extrabold text-slate-900 tracking-tight">AxiaOrto<span class="text-emerald-600">.</span></span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Buat Akun Baru.</h1>
                    <p class="text-slate-500 font-medium text-sm">Silakan lengkapi data untuk memulai.</p>
                </div>

                <form action="{{ route('register') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none font-medium text-slate-900" placeholder="John Doe">
                        @error('name') <p class="text-[10px] text-rose-500 mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none font-medium text-slate-900" placeholder="nama@email.com">
                        @error('email') <p class="text-[10px] text-rose-500 mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">Password</label>
                            <input type="password" name="password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none font-medium text-slate-900" placeholder="••••••••">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">Konfirmasi</label>
                            <input type="password" name="password_confirmation" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none font-medium text-slate-900" placeholder="••••••••">
                        </div>
                    </div>
                    @error('password') <p class="text-[10px] text-rose-500 mt-1 ml-1">{{ $message }}</p> @enderror

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-emerald-600 text-white rounded-xl font-bold text-lg hover:bg-emerald-700 shadow-xl shadow-emerald-500/30 transition-all transform active:scale-[0.98]">
                            Daftar Sekarang
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-slate-500 text-sm font-medium">Sudah punya akun? <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">Masuk di sini</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>