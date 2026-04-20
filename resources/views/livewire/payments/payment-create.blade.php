<div class="space-y-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('payments.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Catat Pembayaran</h1>
            <p class="text-slate-500 mt-1">Input data transaksi masuk dari pelunasan pesanan.</p>
        </div>
    </div>

    <form wire:submit="store" class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                        <i class="fa-solid fa-file-invoice mr-3 text-indigo-500"></i> Pilih Pesanan
                    </h2>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Nomor Pesanan <span class="text-rose-500">*</span></label>
                            <select wire:model.live="order_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                <option value="">Pilih Pesanan yang belum lunas</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->id }}">{{ $order->order_number }} - {{ $order->patient->name }} (Rp {{ number_format($order->total_amount, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            @error('order_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if($selected_order)
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Pasien</p>
                                <p class="text-sm font-bold text-slate-900">{{ $selected_order->patient->name }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">No. MRN</p>
                                <p class="text-sm font-bold text-slate-900">{{ $selected_order->patient->medical_record_number }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Tanggal Bayar <span class="text-rose-500">*</span></label>
                                <input wire:model="payment_date" type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Metode Pembayaran <span class="text-rose-500">*</span></label>
                                <select wire:model="payment_method" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                    <option value="cash">Tunai (Cash)</option>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="debit_card">Kartu Debit</option>
                                    <option value="credit_card">Kartu Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                        <i class="fa-solid fa-money-bill-transfer mr-3 text-emerald-500"></i> Detail Transaksi
                    </h2>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Jumlah Pembayaran (Rp) <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold">Rp</div>
                                <input wire:model="amount" type="number" class="w-full pl-12 pr-4 py-4 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-xl font-bold text-indigo-600" placeholder="0">
                            </div>
                            @error('amount') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Nomor Referensi / Bukti Transfer</label>
                            <input wire:model="reference_number" type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Contoh: TRX-12345678">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Catatan Pembayaran</label>
                            <textarea wire:model="notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Tambahkan keterangan jika perlu..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-indigo-600 rounded-3xl p-8 text-white shadow-xl shadow-indigo-500/20">
                    <h3 class="font-bold mb-2">Konfirmasi</h3>
                    <p class="text-indigo-100 text-xs mb-6">Pastikan dana telah diterima sebelum menyimpan catatan ini.</p>
                    
                    <button type="submit" class="w-full py-4 bg-white text-indigo-600 rounded-2xl font-bold hover:bg-indigo-50 transition-all shadow-lg">
                        Simpan Pembayaran
                    </button>
                    <a href="{{ route('payments.index') }}" class="block text-center mt-4 text-xs text-indigo-200 hover:text-white transition-colors">Batal dan Kembali</a>
                </div>

                <div class="p-6 bg-white rounded-3xl border border-slate-200">
                    <div class="flex items-center mb-4">
                        <div class="p-2 bg-amber-50 rounded-lg mr-3">
                            <i class="fa-solid fa-lightbulb text-amber-500"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">Tips</h4>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">Gunakan nomor referensi transfer bank untuk memudahkan rekonsiliasi keuangan di akhir bulan.</p>
                </div>
            </div>
        </div>
    </form>
</div>
