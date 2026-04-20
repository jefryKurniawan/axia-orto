@extends('layouts.app')

@section('title', 'Detail Konsultasi')

@section('content')
<div class="space-y-6 pb-20">
    <!-- Breadcrumbs & Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-6">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[8px] opacity-50"></i>
                <a href="{{ route('consultations.index') }}" class="hover:text-indigo-600 transition-colors">Konsultasi</a>
                <i class="fa-solid fa-chevron-right text-[8px] opacity-50"></i>
                <span class="text-slate-600 dark:text-slate-300">Detail #{{ $consultation->id }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Informasi Konsultasi</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('consultations.edit', $consultation) }}" 
               class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                <i class="fa-solid fa-user-pen mr-2"></i> Edit Data
            </a>
            <a href="{{ route('consultations.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-slate-900 dark:bg-slate-800 text-white rounded-xl text-sm font-bold hover:bg-slate-800 dark:hover:bg-slate-700 transition-all">
                <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info Left (2 Columns) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Diagnosis & Treatment -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-50 dark:border-slate-800/50 flex items-center justify-between">
                    <h3 class="font-black text-slate-900 dark:text-white uppercase tracking-wider text-xs">Hasil Pemeriksaan & Diagnosa</h3>
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                        @if($consultation->status == 'completed') bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 
                        @elseif($consultation->status == 'cancelled') bg-rose-50 dark:bg-rose-500/10 text-rose-600 
                        @else bg-blue-50 dark:bg-blue-500/10 text-blue-600 @endif">
                        {{ $consultation->status }}
                    </span>
                </div>
                <div class="p-8 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Keluhan Utama</label>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-medium bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                                {{ $consultation->complaint }}
                            </p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Diagnosa Medis</label>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-medium bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                                {{ $consultation->diagnosis }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rencana Tindakan (Treatment Plan)</label>
                        <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-medium bg-indigo-50/30 dark:bg-indigo-500/5 p-6 rounded-2xl border border-indigo-100/50 dark:border-indigo-500/10">
                            {{ $consultation->treatment_plan ?? 'Tidak ada rencana tindakan spesifik.' }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Catatan Tambahan</label>
                        <p class="text-sm text-slate-500 dark:text-slate-400 italic">
                            {{ $consultation->notes ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Follow Up Info -->
            @if($consultation->follow_up_date)
            <div class="bg-amber-50 dark:bg-amber-500/5 rounded-2xl border border-amber-100 dark:border-amber-500/10 p-6 flex items-center space-x-4">
                <div class="h-12 w-12 bg-amber-100 dark:bg-amber-500/20 rounded-xl flex items-center justify-center text-amber-600">
                    <i class="fa-solid fa-calendar-check text-xl"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-amber-900 dark:text-amber-400 uppercase tracking-wide">Jadwal Kontrol Berikutnya</h4>
                    <p class="text-xs font-bold text-amber-700 dark:text-amber-500 mt-0.5">
                        {{ \Carbon\Carbon::parse($consultation->follow_up_date)->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Info Right (1 Column) -->
        <div class="space-y-6">
            <!-- Patient Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <h3 class="font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px] mb-6">Informasi Pasien</h3>
                <div class="flex items-center mb-6">
                    <div class="h-14 w-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 font-black text-xl">
                        {{ strtoupper(substr($consultation->patient->name, 0, 1)) }}
                    </div>
                    <div class="ml-4">
                        <p class="text-lg font-black text-slate-900 dark:text-white leading-none">{{ $consultation->patient->name }}</p>
                        <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mt-1.5">{{ $consultation->patient->medical_record_number }}</p>
                    </div>
                </div>
                <div class="space-y-4 pt-4 border-t border-slate-50 dark:border-slate-800/50">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase">Jenis Kelamin</span>
                        <span class="text-xs font-black text-slate-700 dark:text-slate-300">{{ $consultation->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase">Usia</span>
                        <span class="text-xs font-black text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($consultation->patient->date_of_birth)->age }} Tahun</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase">Kontak</span>
                        <span class="text-xs font-black text-slate-700 dark:text-slate-300">{{ $consultation->patient->phone ?? '-' }}</span>
                    </div>
                </div>
                <a href="{{ route('patients.show', $consultation->patient->uuid) }}" class="mt-6 w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-50 hover:text-indigo-600 transition-all">
                    Lihat Rekam Medis Lengkap
                </a>
            </div>

            <!-- Doctor Card -->
            <div class="bg-indigo-600 dark:bg-indigo-500 rounded-2xl shadow-xl shadow-indigo-500/20 p-6 text-white">
                <h3 class="font-black text-indigo-200 uppercase tracking-wider text-[10px] mb-6">Tenaga Medis</h3>
                <div class="flex items-center">
                    <img class="h-12 w-12 rounded-xl ring-2 ring-white/20" src="https://ui-avatars.com/api/?name={{ urlencode($consultation->doctor->name) }}&background=fff&color=4f46e5" alt="">
                    <div class="ml-4">
                        <p class="text-base font-black leading-none">{{ $consultation->doctor->name }}</p>
                        <p class="text-[10px] font-bold text-indigo-100 uppercase tracking-widest mt-1.5">{{ $consultation->doctor->specialization ?? 'Dokter Pemeriksa' }}</p>
                    </div>
                </div>
            </div>

            <!-- Meta Info -->
            <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-6 space-y-4">
                <div class="flex items-center text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-calendar-day w-6 text-indigo-500"></i>
                    <div class="ml-2">
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-50">Tanggal Konsultasi</p>
                        <p class="text-xs font-black">{{ \Carbon\Carbon::parse($consultation->consultation_date)->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-clock w-6 text-indigo-500"></i>
                    <div class="ml-2">
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-50">Waktu Entry</p>
                        <p class="text-xs font-black">{{ $consultation->created_at->format('H:i') }} WIB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
