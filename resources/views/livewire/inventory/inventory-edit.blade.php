<div class="space-y-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('inventory.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Edit Item Inventori</h1>
            <p class="text-slate-500 mt-1">Perbarui detail stok dan harga untuk {{ $item->name }}.</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-8">
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                <i class="fa-solid fa-box mr-3 text-indigo-500"></i> Informasi Barang
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Nama Barang <span class="text-rose-500">*</span></label>
                    <input wire:model="name" type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Kode Barang <span class="text-rose-500">*</span></label>
                    <input wire:model="code" type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all bg-slate-50">
                    @error('code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Kategori <span class="text-rose-500">*</span></label>
                    <select wire:model="category" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        <option value="material">Material</option>
                        <option value="component">Komponen</option>
                        <option value="tool">Alat</option>
                    </select>
                    @error('category') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Satuan <span class="text-rose-500">*</span></label>
                    <input wire:model="unit" type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                    @error('unit') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fa-solid fa-layer-group mr-3 text-emerald-500"></i> Manajemen Stok
                </h2>
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Stok Saat Ini</label>
                        <input wire:model="quantity" type="number" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        @error('quantity') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Stok Minimum</label>
                        <input wire:model="reorder_level" type="number" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        @error('reorder_level') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fa-solid fa-tags mr-3 text-amber-500"></i> Harga & Nilai
                </h2>
                <div class="grid grid-cols-1 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Harga (Rp)</label>
                        <input wire:model="price" type="number" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        @error('price') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Deskripsi Barang</label>
                <textarea wire:model="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"></textarea>
                @error('description') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('inventory.index') }}" class="px-8 py-4 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</a>
            <button type="submit" class="px-8 py-4 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/25 transition-all">
                <i class="fa-solid fa-save mr-2"></i> Perbarui Item
            </button>
        </div>
    </form>
</div>