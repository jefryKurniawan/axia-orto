<div class="space-y-6 pb-20">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-6">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[8px] opacity-50"></i>
                <span class="text-slate-600 dark:text-slate-300">Riwayat Pembayaran</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Manajemen Pembayaran</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.export-pdf', ['type' => 'payments']) }}" target="_blank"
               class="inline-flex items-center px-4 py-2 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-800 rounded-xl text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors shadow-sm">
                <i class="fa-solid fa-file-pdf mr-2"></i>
                Cetak PDF
            </a>
            <button wire:click="export" 
                    wire:loading.attr="disabled"
                    wire:target="export"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fa-solid fa-file-export mr-2" wire:loading.remove wire:target="export"></i>
                <i class="fa-solid fa-spinner fa-spin mr-2" wire:loading wire:target="export"></i>
                Export
            </button>
            <a href="{{ route('payments.create') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-indigo-600 dark:bg-indigo-500 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-all duration-200 shadow-lg shadow-indigo-500/20 active:scale-95">
                <i class="fa-solid fa-file-invoice-dollar mr-2"></i> Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="relative flex-1 max-w-lg">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 dark:text-slate-600 text-sm"></i>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" 
                   placeholder="Cari Nomor Order atau Pasien..." 
                   class="block w-full pl-11 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-200 placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
        </div>

        <div class="flex items-center gap-2">
            <select wire:model.live="method" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 outline-none cursor-pointer">
                <option value="">Semua Metode</option>
                <option value="cash">Tunai (Cash)</option>
                <option value="transfer">Transfer Bank</option>
                <option value="qris">QRIS</option>
                <option value="debit">Kartu Debit</option>
            </select>

            <button wire:click="resetFilters" class="p-2.5 text-slate-400 dark:text-slate-600 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all" title="Reset Filter">
                <i class="fa-solid fa-filter-circle-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800/50">
                        <th class="pl-6 py-4 w-10">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" 
                                       class="w-4 h-4 text-indigo-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500 focus:ring-2 transition-all cursor-pointer"
                                       x-data="{ 
                                            checkAll() {
                                                const allIds = @js($payments->pluck('id')->toArray());
                                                if ($el.checked) {
                                                    $wire.selectedRows = allIds;
                                                } else {
                                                    $wire.selectedRows = [];
                                                }
                                            } 
                                       }"
                                       @change="checkAll">
                            </div>
                        </th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Waktu Transaksi</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">No. Order / Pasien</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">Jumlah Bayar</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Metode</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Kasir</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                    @forelse($payments as $payment)
                    <tr wire:key="payment-{{ $payment->id }}" 
                        class="transition-colors group {{ in_array($payment->id, $selectedRows) ? 'bg-indigo-50/50 dark:bg-indigo-500/10' : 'hover:bg-slate-50/50 dark:hover:bg-indigo-500/5' }}">
                        <td class="pl-6 py-5 whitespace-nowrap">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" 
                                       wire:model.live="selectedRows" 
                                       value="{{ $payment->id }}"
                                       class="w-4 h-4 text-indigo-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500 focus:ring-2 transition-all cursor-pointer">
                            </div>
                        </td>
                        <td class="px-4 py-5 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-900 dark:text-white">{{ $payment->created_at->format('H:i') }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $payment->created_at->translatedFormat('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 font-black text-xs mr-3">
                                    {{ strtoupper(substr($payment->order->patient->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 dark:text-white leading-none">{{ $payment->order->patient->name }}</p>
                                    <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 mt-1.5 uppercase tracking-tighter">ORDER #{{ $payment->order->order_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center whitespace-nowrap">
                            <p class="text-sm font-black text-slate-900 dark:text-white">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            @php
                                $methodColors = [
                                    'cash' => 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
                                    'transfer' => 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400',
                                    'qris' => 'bg-purple-100 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400',
                                    'debit' => 'bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400',
                                ];
                                $color = $methodColors[$payment->payment_method] ?? 'bg-slate-50 text-slate-600';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $color }}">
                                {{ $payment->payment_method }}
                            </span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <p class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $payment->createdBy->name ?? 'System' }}</p>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="#" class="p-2 text-slate-300 dark:text-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-lg transition-all" title="Cetak Kwitansi">
                                    <i class="fa-solid fa-print text-sm"></i>
                                </a>
                                <a href="#" class="p-2 text-slate-300 dark:text-slate-700 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-lg transition-all" title="Detail">
                                    <i class="fa-solid fa-circle-info text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="h-20 w-20 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4 border-2 border-dashed border-slate-200 dark:border-slate-700">
                                    <i class="fa-solid fa-money-bill-transfer text-3xl text-slate-200 dark:text-slate-700"></i>
                                </div>
                                <h4 class="text-slate-900 dark:text-white font-black text-xl">Belum Ada Transaksi</h4>
                                <p class="text-slate-500 dark:text-slate-500 text-sm mt-1 max-w-xs mx-auto">Riwayat pembayaran akan muncul di sini setelah transaksi dilakukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($payments->hasPages())
        <div class="px-8 py-5 bg-slate-50/30 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
