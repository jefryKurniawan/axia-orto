<div class="space-y-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('treatment-orders.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Edit Pesanan #{{ $order->order_number }}</h1>
            <p class="text-slate-500 mt-1">Perbarui detail pesanan alat orthotic prosthetic pasien.</p>
        </div>
    </div>

    <form wire:submit="update" class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Order Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Patient & Date -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                        <i class="fa-solid fa-user-clock mr-3 text-indigo-500"></i> Informasi Dasar
                    </h2>
                    
                    <div class="space-y-6">
                        <div class="relative">
                            <label class="text-sm font-semibold text-slate-700">Pasien</label>
                            <div class="relative mt-2">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-user text-sm"></i>
                                </div>
                                <input type="text" value="{{ $patient_search }}" readonly
                                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-100 bg-slate-50 text-slate-500 cursor-not-allowed">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Pasien tidak dapat diubah pada pesanan yang sudah dibuat.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Tanggal Order <span class="text-rose-500">*</span></label>
                                <input wire:model="order_date" type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Estimasi Penyerahan</label>
                                <input wire:model="delivery_date" type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center">
                            <i class="fa-solid fa-list-check mr-3 text-emerald-500"></i> Detail Layanan/Alat
                        </h2>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" type="button" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition-colors">
                                <i class="fa-solid fa-plus mr-2"></i> Tambah Item
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-72 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 overflow-hidden" x-cloak>
                                <div class="p-3 border-b border-slate-100 bg-slate-50">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pilih Layanan</p>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    @foreach($services as $service)
                                    <button type="button" wire:click="addItem({{ $service->id }}); open = false" class="w-full px-4 py-3 text-left hover:bg-slate-50 border-b border-slate-50 last:border-0 transition-colors">
                                        <p class="text-sm font-semibold text-slate-900">{{ $service->name }}</p>
                                        <p class="text-[10px] text-indigo-600 font-bold">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left border-b border-slate-100">
                                    <th class="pb-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Item / Layanan</th>
                                    <th class="pb-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Qty</th>
                                    <th class="pb-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Harga</th>
                                    <th class="pb-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Subtotal</th>
                                    <th class="pb-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($items as $index => $item)
                                <tr class="group">
                                    <td class="py-4">
                                        <p class="text-sm font-semibold text-slate-900">{{ $item['name'] }}</p>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="inline-flex items-center bg-slate-50 rounded-lg p-1">
                                            <button type="button" wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})" class="h-6 w-6 flex items-center justify-center text-slate-400 hover:text-indigo-600">
                                                <i class="fa-solid fa-minus text-[10px]"></i>
                                            </button>
                                            <span class="w-8 text-center text-sm font-bold text-slate-900">{{ $item['quantity'] }}</span>
                                            <button type="button" wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})" class="h-6 w-6 flex items-center justify-center text-slate-400 hover:text-indigo-600">
                                                <i class="fa-solid fa-plus text-[10px]"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-4 text-right">
                                        <p class="text-sm text-slate-600 font-medium">Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</p>
                                    </td>
                                    <td class="py-4 text-right">
                                        <p class="text-sm font-bold text-slate-900">Rp {{ number_format($item['total_price'], 0, ',', '.') }}</p>
                                    </td>
                                    <td class="py-4 text-right">
                                        <button type="button" wire:click="removeItem({{ $index }})" class="p-2 text-slate-300 hover:text-rose-600 transition-colors">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center">
                                        <p class="text-sm text-slate-400">Belum ada item ditambahkan.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($items) > 0)
                            <tfoot>
                                <tr class="border-t border-slate-200">
                                    <td colspan="3" class="pt-6 text-right">
                                        <p class="text-sm font-bold text-slate-500 uppercase">Total Keseluruhan</p>
                                    </td>
                                    <td class="pt-6 text-right">
                                        <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($total_amount, 0, ',', '.') }}</p>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Settings -->
            <div class="space-y-8">
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                        <i class="fa-solid fa-sliders mr-3 text-amber-500"></i> Konfigurasi
                    </h2>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Status Order</label>
                            <select wire:model="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                <option value="pending">Pending</option>
                                <option value="in_progress">Dalam Proses</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Catatan Pesanan</label>
                            <textarea wire:model="notes" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Catatan spesifikasi alat, dsb..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-xl">
                    <h3 class="font-bold mb-2">Ringkasan Pembayaran</h3>
                    <p class="text-slate-400 text-xs mb-6">Pastikan data sudah benar sebelum menyimpan perubahan.</p>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Total Tagihan</span>
                            <span class="font-bold text-white">Rp {{ number_format($total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-white/10 pt-4 flex justify-between">
                            <span class="text-sm font-bold">Harus Dibayar</span>
                            <span class="text-xl font-bold text-indigo-400">Rp {{ number_format($total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
