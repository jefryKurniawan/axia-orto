<div class="space-y-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('services.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Tambah Layanan</h1>
            <p class="text-slate-500 mt-1">Daftarkan jenis layanan medis atau produk workshop baru.</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-8">
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                <i class="fa-solid fa-hand-holding-medical mr-3 text-indigo-500"></i> Detail Layanan
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Nama Layanan <span class="text-rose-500">*</span></label>
                    <input wire:model="name" type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Contoh: Pembuatan AFO Single Upright">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Kode Layanan <span class="text-rose-500">*</span></label>
                    <input wire:model="code" type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all bg-slate-50">
                    @error('code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Kategori Layanan <span class="text-rose-500">*</span></label>
                    <select wire:model.live="service_type" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        <option value="konsultasi">Konsultasi</option>
                        <option value="ortosis">Ortosis</option>
                        <option value="protesis">Protesis</option>
                        <option value="terapi">Terapi</option>
                        <option value="alat">Alat Bantu</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Harga (Rp) <span class="text-rose-500">*</span></label>
                    <input wire:model="price" type="number" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                    @error('price') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Estimasi Durasi (Hari)</label>
                    <input wire:model="duration_days" type="number" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                    @error('duration_days') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-8 space-y-2">
                <label class="text-sm font-semibold text-slate-700">Deskripsi Layanan</label>
                <textarea wire:model="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Penjelasan singkat mengenai layanan ini..."></textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('services.index') }}" class="px-8 py-4 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</a>
            <button type="submit" class="px-8 py-4 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/25 transition-all">
                <i class="fa-solid fa-save mr-2"></i> Simpan Layanan
            </button>
        </div>
    </form>
</div>
