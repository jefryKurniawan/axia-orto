@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Halo, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
            <p class="text-slate-500 mt-1">Ini ringkasan aktivitas klinik Anda hari ini.</p>
        </div>
        <div class="flex items-center gap-3 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm">
            <div class="h-10 w-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-xs uppercase">
                {{ now()->format('M') }}
            </div>
            <div class="pr-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hari Ini</p>
                <p class="text-sm font-bold text-slate-900">{{ now()->format('d F Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-stethoscope text-xl"></i>
                </div>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">+12%</span>
            </div>
            <p class="text-sm font-medium text-slate-500">Konsultasi Hari Ini</p>
            <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['today_consultations'] }}</h3>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-clipboard-list text-xl"></i>
                </div>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">+5%</span>
            </div>
            <p class="text-sm font-medium text-slate-500">Pemesanan Aktif</p>
            <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['active_orders'] }}</h3>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-lg">Bulan Ini</span>
            </div>
            <p class="text-sm font-medium text-slate-500">Pendapatan</p>
            <h3 class="text-2xl font-bold text-slate-900 mt-1">Rp {{ number_format($stats['monthly_revenue'], 0, ',', '.') }}</h3>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-boxes-stacked text-xl"></i>
                </div>
                <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-lg">Penting!</span>
            </div>
            <p class="text-sm font-medium text-slate-500">Stok Menipis</p>
            <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['low_stock_items'] }}</h3>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Recent Consultations -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900">Konsultasi Terbaru</h2>
                    <a href="{{ route('consultations.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pasien</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dokter</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($recentConsultations as $consultation)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-[10px] mr-3">
                                            {{ strtoupper(substr($consultation->patient->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-semibold text-slate-900">{{ $consultation->patient->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-slate-600">{{ $consultation->doctor->name }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs text-slate-500">{{ $consultation->consultation_date->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'completed' => 'bg-emerald-50 text-emerald-700',
                                            'in_progress' => 'bg-amber-50 text-amber-700',
                                            'scheduled' => 'bg-blue-50 text-blue-700',
                                        ];
                                        $color = $statusColors[$consultation->status] ?? 'bg-slate-50 text-slate-600';
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide {{ $color }}">
                                        {{ str_replace('_', ' ', $consultation->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-slate-900 rounded-3xl p-8 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-2xl font-bold mb-2">Butuh Bantuan?</h3>
                    <p class="text-slate-400 text-sm max-w-md mb-6">Pelajari cara menggunakan fitur-fitur baru di AxiaOrto ERP melalui dokumentasi panduan pengguna kami.</p>
                    <button class="px-6 py-3 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 transition-all">Buka Panduan</button>
                </div>
                <div class="absolute right-0 bottom-0 opacity-10">
                    <i class="fa-solid fa-graduation-cap text-[120px] -mr-4 -mb-4"></i>
                </div>
            </div>
        </div>

        <!-- Right: Activity & Quick Links -->
        <div class="space-y-8">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-6">Jadwal Mendatang</h2>
                <div class="space-y-4">
                    @forelse($todaySchedule as $schedule)
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="flex-shrink-0 w-10 text-center">
                            <p class="text-xs font-bold text-indigo-600">{{ $schedule->consultation_date->format('H:i') }}</p>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-slate-900">{{ $schedule->patient->name }}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">{{ $schedule->doctor->name }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <p class="text-sm text-slate-400 italic">Tidak ada jadwal mendesak.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-6">Aksi Cepat</h2>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('patients.create') }}" class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-indigo-50 hover:border-indigo-100 transition-all text-center">
                        <i class="fa-solid fa-user-plus text-indigo-600 mb-2"></i>
                        <p class="text-[10px] font-bold text-slate-900">Tambah Pasien</p>
                    </a>
                    <a href="{{ route('consultations.create') }}" class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-emerald-50 hover:border-emerald-100 transition-all text-center">
                        <i class="fa-solid fa-calendar-plus text-emerald-600 mb-2"></i>
                        <p class="text-[10px] font-bold text-slate-900">Jadwal Baru</p>
                    </a>
                    <a href="{{ route('treatment-orders.create') }}" class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-amber-50 hover:border-amber-100 transition-all text-center">
                        <i class="fa-solid fa-clipboard-check text-amber-600 mb-2"></i>
                        <p class="text-[10px] font-bold text-slate-900">Buat Order</p>
                    </a>
                    <a href="{{ route('inventory.create') }}" class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-rose-50 hover:border-rose-100 transition-all text-center">
                        <i class="fa-solid fa-box-open text-rose-600 mb-2"></i>
                        <p class="text-[10px] font-bold text-slate-900">Input Barang</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection