@extends('layouts.app')

@section('title', 'Riwayat Medis - ' . $patient->name)

@section('content')
<div class="space-y-8 pb-20">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-slate-800 pb-8">
        <div class="flex items-center gap-5">
            <div class="h-16 w-16 bg-slate-900 dark:bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-xl">
                {{ strtoupper(substr($patient->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">{{ $patient->name }}</h1>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mt-1">Rekam Medis Terintegrasi • {{ $patient->medical_record_number }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all shadow-sm">
                <i class="fa-solid fa-print mr-2"></i> Cetak Rekam Medis
            </button>
            <a href="{{ route('patients.show', $patient) }}" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-all shadow-lg">
                <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Profil
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Kunjungan</p>
            <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ $stats['total_consultations'] }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Order Alat</p>
            <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ $stats['total_orders'] }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Order Selesai</p>
            <h3 class="text-2xl font-black text-emerald-600">{{ $stats['completed_orders'] }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Investasi Medis</p>
            <h3 class="text-2xl font-black text-indigo-600">Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Timeline (Hulu ke Hilir) -->
        <div class="lg:col-span-2 space-y-8">
            <h2 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <i class="fa-solid fa-timeline text-indigo-500"></i> Timeline Perawatan Pasien
            </h2>

            <div class="relative space-y-8 before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
                @foreach($consultations as $consult)
                <div class="relative flex items-start gap-8 group">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-slate-900 border-2 border-indigo-500 shadow-lg z-10 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-stethoscope text-indigo-500 text-sm"></i>
                    </div>
                    <div class="flex-1 bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">{{ $consult->consultation_date->format('d F Y') }}</span>
                                <h4 class="text-lg font-black text-slate-900 dark:text-white uppercase">{{ $consult->doctor->name }}</h4>
                            </div>
                            <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg text-[10px] font-black uppercase tracking-wider">Konsultasi</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Diagnosis</p>
                                <p class="font-bold text-slate-700 dark:text-slate-300">{{ $consult->diagnosis }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Rencana Perawatan</p>
                                <p class="text-slate-600 dark:text-slate-400">{{ $consult->treatment_plan }}</p>
                            </div>
                        </div>

                        <!-- Linked Orders -->
                        @php
                            $linkedOrders = $treatmentOrders->where('consultation_id', $consult->id);
                        @endphp
                        @if($linkedOrders->count() > 0)
                        <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 space-y-4">
                            @foreach($linkedOrders as $order)
                            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                                <div class="flex justify-between items-center mb-2">
                                    <h5 class="text-xs font-black text-slate-900 dark:text-white uppercase">Order: {{ $order->order_number }}</h5>
                                    <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded text-[9px] font-black uppercase">{{ $order->status }}</span>
                                </div>
                                <ul class="space-y-1">
                                    @foreach($order->orderItems as $item)
                                    <li class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                        <i class="fa-solid fa-check text-emerald-500 text-[8px]"></i>
                                        {{ $item->service->name }} (Rp {{ number_format($item->total_price, 0, ',', '.') }})
                                    </li>
                                    @endforeach
                                </ul>
                                
                                <!-- Payment Status -->
                                @if($order->payments->count() > 0)
                                <div class="mt-3 flex items-center gap-2 text-[9px] font-black text-emerald-600 uppercase">
                                    <i class="fa-solid fa-circle-check"></i> Sudah Dibayar
                                </div>
                                @else
                                <div class="mt-3 flex items-center gap-2 text-[9px] font-black text-rose-600 uppercase">
                                    <i class="fa-solid fa-circle-exclamation"></i> Belum Dibayar
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-8">
            <!-- Data Pengukuran (MVP #2) -->
            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                <div class="absolute -right-4 -top-4 h-24 w-24 bg-white/5 rounded-full blur-2xl"></div>
                <h3 class="text-lg font-black uppercase tracking-tight mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-ruler-combined text-indigo-400"></i> Data Pengukuran
                </h3>
                
                @php
                    $measurements = $patient->measurements ?? collect();
                @endphp
                
                @if($measurements->count() > 0)
                <div class="space-y-4">
                    <!-- Data measurements logic here -->
                </div>
                @else
                <div class="text-center py-8">
                    <div class="h-12 w-12 bg-white/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-drafting-compass text-slate-400"></i>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">Belum ada data pengukuran ortotik yang tercatat untuk pasien ini.</p>
                </div>
                @endif
                
                <button class="w-full mt-6 py-3 bg-indigo-600 hover:bg-indigo-700 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    Input Pengukuran Baru
                </button>
            </div>

            <!-- Catatan Tambahan -->
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase mb-4">Kondisi Umum</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-rose-50 dark:bg-rose-500/5 rounded-2xl border border-rose-100 dark:border-rose-900/30">
                        <p class="text-[10px] font-black text-rose-600 uppercase mb-1">Alergi</p>
                        <p class="text-sm text-rose-800 dark:text-rose-400 font-bold">{{ $patient->allergies ?? 'Tidak Ada' }}</p>
                    </div>
                    <div class="p-4 bg-indigo-50 dark:bg-indigo-500/5 rounded-2xl border border-indigo-100 dark:border-indigo-900/30">
                        <p class="text-[10px] font-black text-indigo-600 uppercase mb-1">Asuransi</p>
                        <p class="text-sm text-indigo-800 dark:text-indigo-400 font-bold uppercase">{{ $patient->insurance_type }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
