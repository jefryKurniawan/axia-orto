@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="space-y-8 pb-20">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('treatment-orders.index') }}" class="p-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-500 hover:text-indigo-600 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Pesanan #{{ $order->order_number }}</h1>
                    @php
                        $statusColors = [
                            'pending' => 'bg-slate-100 text-slate-600',
                            'in_progress' => 'bg-indigo-100 text-indigo-700',
                            'completed' => 'bg-emerald-100 text-emerald-700',
                            'cancelled' => 'bg-rose-100 text-rose-700',
                        ];
                    @endphp
                    <span class="px-3 py-1 {{ $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600' }} rounded-full text-[10px] font-black uppercase tracking-widest">
                        {{ $order->status }}
                    </span>
                </div>
                <p class="text-slate-500 text-xs mt-1">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }} oleh {{ $order->createdBy->name ?? 'System' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('treatment-orders.edit', $order) }}" class="px-5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all shadow-sm">
                <i class="fa-solid fa-pen-to-square mr-2 text-emerald-500"></i> Edit Pesanan
            </a>
            <button class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-all shadow-lg">
                <i class="fa-solid fa-print mr-2"></i> Cetak SPK
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Order Items -->
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight mb-6">Detail Layanan & Alat</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800">
                                <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Item / Layanan</th>
                                <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Qty</th>
                                <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Harga</th>
                                <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                            @foreach($order->orderItems as $item)
                            <tr>
                                <td class="py-5">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $item->service->name }}</p>
                                    @if($item->specifications)
                                        <p class="text-[10px] text-slate-500 mt-1">Spesifikasi: {{ json_encode($item->specifications) }}</p>
                                    @endif
                                </td>
                                <td class="py-5 text-center">
                                    <span class="text-sm font-bold text-slate-600 dark:text-slate-400">{{ $item->quantity }}</span>
                                </td>
                                <td class="py-5 text-right">
                                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </td>
                                <td class="py-5 text-right">
                                    <p class="text-sm font-black text-slate-900 dark:text-white">Rp {{ number_format($item->total_price, 0, ',', '.') }}</p>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="pt-8 text-right">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Tagihan</p>
                                </td>
                                <td class="pt-8 text-right">
                                    <p class="text-2xl font-black text-indigo-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes & Extra Info -->
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight mb-4">Catatan Klinis / Produksi</h2>
                <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed italic">
                        "{{ $order->notes ?? 'Tidak ada catatan tambahan untuk pesanan ini.' }}"
                    </p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Patient Info -->
            <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-500/20">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-indigo-200 mb-6">Informasi Pasien</h3>
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-16 w-16 bg-white/10 rounded-2xl flex items-center justify-center text-2xl font-black">
                        {{ strtoupper(substr($order->patient->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-xl font-black uppercase leading-tight">{{ $order->patient->name }}</h4>
                        <p class="text-[10px] font-bold text-indigo-200 mt-1">{{ $order->patient->medical_record_number }}</p>
                    </div>
                </div>
                <div class="space-y-4 text-xs font-bold">
                    <div class="flex justify-between border-b border-white/10 pb-4">
                        <span class="text-indigo-200">Jenis Kelamin</span>
                        <span>{{ $order->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-white/10 pb-4">
                        <span class="text-indigo-200">Kontak</span>
                        <span>{{ $order->patient->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-indigo-200">Asuransi</span>
                        <span class="uppercase">{{ $order->patient->insurance_type }}</span>
                    </div>
                </div>
                <a href="{{ route('patients.show', $order->patient) }}" class="w-full mt-8 py-3 bg-white text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest text-center block hover:bg-indigo-50 transition-all">
                    Lihat Rekam Medis
                </a>
            </div>

            <!-- Delivery Tracker -->
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Tracking Produksi</h3>
                <div class="space-y-6 relative before:absolute before:inset-0 before:ml-[1.125rem] before:-translate-x-px before:h-full before:w-0.5 before:bg-slate-100 dark:before:bg-slate-800">
                    <div class="relative flex items-center gap-4">
                        <div class="h-9 w-9 rounded-full bg-emerald-500 border-4 border-white dark:border-slate-900 flex items-center justify-center text-white z-10 shadow-lg">
                            <i class="fa-solid fa-check text-[10px]"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Order Dibuat</p>
                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $order->order_date->format('d M Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="relative flex items-center gap-4">
                        <div class="h-9 w-9 rounded-full {{ $order->status != 'pending' ? 'bg-indigo-500' : 'bg-slate-200 dark:bg-slate-800' }} border-4 border-white dark:border-slate-900 flex items-center justify-center text-white z-10">
                            <i class="fa-solid fa-industry text-[10px]"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sedang Diproses</p>
                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $order->status == 'in_progress' ? 'Aktif' : 'Menunggu' }}</p>
                        </div>
                    </div>

                    <div class="relative flex items-center gap-4">
                        <div class="h-9 w-9 rounded-full {{ $order->status == 'completed' ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-800' }} border-4 border-white dark:border-slate-900 flex items-center justify-center text-white z-10">
                            <i class="fa-solid fa-truck-ramp-box text-[10px]"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Estimasi Penyerahan</p>
                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $order->delivery_date ? $order->delivery_date->format('d M Y') : 'TBD' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-amber-50 dark:bg-amber-500/5 rounded-[2.5rem] p-8 border border-amber-100 dark:border-amber-900/30">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-amber-600 mb-4">Status Pembayaran</h3>
                @php
                    $isPaid = $order->payments->where('status', 'paid')->count() > 0;
                @endphp
                @if($isPaid)
                <div class="flex items-center gap-3 text-emerald-600">
                    <i class="fa-solid fa-circle-check text-2xl"></i>
                    <div>
                        <p class="text-sm font-black uppercase tracking-tight">Lunas</p>
                        <p class="text-[10px] font-bold opacity-80">Pembayaran diterima via Transfer</p>
                    </div>
                </div>
                @else
                <div class="flex items-center gap-3 text-amber-600">
                    <i class="fa-solid fa-circle-exclamation text-2xl"></i>
                    <div>
                        <p class="text-sm font-black uppercase tracking-tight">Menunggu Pembayaran</p>
                        <p class="text-[10px] font-bold opacity-80 text-rose-500">Tagihan Belum Dibayar</p>
                    </div>
                </div>
                <button class="w-full mt-6 py-3 bg-amber-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-700 transition-all shadow-lg shadow-amber-500/20">
                    Bayar Sekarang
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
