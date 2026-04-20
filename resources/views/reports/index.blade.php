@extends('layouts.app')

@section('title', 'Laporan Analistik')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Laporan & Analistik</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">Pusat kendali data dan performa Klinik AxiaOrto.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="h-12 w-12 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center shadow-inner">
                <i class="fa-solid fa-chart-line text-xl"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Laporan Pasien -->
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 h-24 w-24 bg-indigo-50 dark:bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-all"></div>
            
            <div class="relative">
                <div class="h-14 w-14 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">Data Pasien</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">Analisis demografi, riwayat kunjungan, dan asuransi pasien.</p>
                
                <div class="space-y-3">
                    <a href="{{ route('reports.export-pdf', ['type' => 'patients']) }}" target="_blank" class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 hover:border-indigo-500 transition-all group/item">
                        <span class="text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">Cetak PDF</span>
                        <i class="fa-solid fa-file-pdf text-rose-500 text-lg group-hover/item:scale-110 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Laporan Konsultasi -->
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 h-24 w-24 bg-emerald-50 dark:bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>
            
            <div class="relative">
                <div class="h-14 w-14 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                    <i class="fa-solid fa-calendar-check text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">Konsultasi</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">Rekapitulasi jadwal konsultasi, diagnosis, dan performa dokter.</p>
                
                <div class="space-y-3">
                    <a href="{{ route('reports.export-pdf', ['type' => 'consultations']) }}" target="_blank" class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 hover:border-emerald-500 transition-all group/item">
                        <span class="text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">Cetak PDF</span>
                        <i class="fa-solid fa-file-pdf text-rose-500 text-lg group-hover/item:scale-110 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Laporan Keuangan -->
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 h-24 w-24 bg-amber-50 dark:bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-all"></div>
            
            <div class="relative">
                <div class="h-14 w-14 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                    <i class="fa-solid fa-money-bill-trend-up text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">Keuangan</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">Pendapatan klinik, metode pembayaran, dan piutang pasien.</p>
                
                <div class="space-y-3">
                    <a href="{{ route('reports.export-pdf', ['type' => 'payments']) }}" target="_blank" class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 hover:border-amber-500 transition-all group/item">
                        <span class="text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">Cetak PDF</span>
                        <i class="fa-solid fa-file-pdf text-rose-500 text-lg group-hover/item:scale-110 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="bg-slate-900 dark:bg-indigo-950/20 rounded-[3rem] p-12 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-indigo-500/10 to-transparent"></div>
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
            <div>
                <p class="text-indigo-400 text-xs font-black uppercase tracking-[0.3em] mb-3">Total Pasien</p>
                <h4 class="text-4xl font-black text-white tracking-tighter">{{ \App\Models\Patient::count() }}</h4>
            </div>
            <div class="border-x border-white/10">
                <p class="text-emerald-400 text-xs font-black uppercase tracking-[0.3em] mb-3">Konsultasi Selesai</p>
                <h4 class="text-4xl font-black text-white tracking-tighter">{{ \App\Models\Consultation::where('status', 'completed')->count() }}</h4>
            </div>
            <div>
                <p class="text-amber-400 text-xs font-black uppercase tracking-[0.3em] mb-3">Total Pendapatan</p>
                <h4 class="text-4xl font-black text-white tracking-tighter">Rp {{ number_format(\App\Models\Payment::sum('amount'), 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>
@endsection
