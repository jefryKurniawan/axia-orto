<div class="space-y-6 pb-20">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-6">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[8px] opacity-50"></i>
                <span class="text-slate-600 dark:text-slate-300">Inventori</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Manajemen Stok Barang</h1>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="export" 
                    wire:loading.attr="disabled"
                    wire:target="export"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fa-solid fa-file-export mr-2" wire:loading.remove wire:target="export"></i>
                <i class="fa-solid fa-spinner fa-spin mr-2" wire:loading wire:target="export"></i>
                Export
            </button>
            <a href="{{ route('inventory.create') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-indigo-600 dark:bg-indigo-500 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-all duration-200 shadow-lg shadow-indigo-500/20 active:scale-95">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Item
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Item -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Total Item</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-2">{{ $stats['total_items'] }}</h3>
                </div>
                <div class="h-14 w-14 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-box-archive text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Stok Menipis</p>
                    <h3 class="text-3xl font-black {{ $stats['low_stock'] > 0 ? 'text-rose-600' : 'text-slate-900 dark:text-white' }} mt-2">{{ $stats['low_stock'] }}</h3>
                </div>
                <div class="h-14 w-14 {{ $stats['low_stock'] > 0 ? 'bg-rose-50 dark:bg-rose-500/10 text-rose-600' : 'bg-slate-50 dark:bg-slate-800 text-slate-400' }} rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Nilai Aset -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Nilai Aset</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-2">Rp {{ number_format($stats['asset_value'], 0, ',', '.') }}</h3>
                </div>
                <div class="h-14 w-14 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-trend-up text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="relative flex-1 max-w-lg">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 dark:text-slate-600 text-sm"></i>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" 
                   placeholder="Cari Kode atau Nama Barang..." 
                   class="block w-full pl-11 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-200 placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
        </div>

        <div class="flex items-center gap-2">
            <select wire:model.live="category" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 outline-none cursor-pointer">
                <option value="">Semua Kategori</option>
                <option value="material">Material</option>
                <option value="component">Komponen</option>
                <option value="tool">Alat</option>
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
                                                const allIds = @js($items->pluck('id')->toArray());
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
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Informasi Barang</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Kategori</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">Stok</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Harga Satuan</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                    @forelse($items as $item)
                    <tr wire:key="item-{{ $item->id }}" 
                        class="transition-colors group {{ in_array($item->id, $selectedRows) ? 'bg-indigo-50/50 dark:bg-indigo-500/10' : 'hover:bg-slate-50/50 dark:hover:bg-indigo-500/5' }}">
                        <td class="pl-6 py-5 whitespace-nowrap">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" 
                                       wire:model.live="selectedRows" 
                                       value="{{ $item->id }}"
                                       class="w-4 h-4 text-indigo-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500 focus:ring-2 transition-all cursor-pointer">
                            </div>
                        </td>
                        <td class="px-4 py-5 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 font-black text-xs mr-3">
                                    {{ strtoupper(substr($item->item_code, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 dark:text-white leading-none">{{ $item->name }}</p>
                                    <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 mt-1.5 uppercase tracking-tighter">{{ $item->item_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                {{ $item->category }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center whitespace-nowrap">
                            <div class="inline-flex flex-col items-center">
                                <p class="text-sm font-black {{ $item->current_stock <= $item->min_stock ? 'text-rose-600' : 'text-slate-900 dark:text-white' }}">
                                    {{ $item->current_stock }}
                                </p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $item->unit }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <p class="text-xs font-bold text-slate-900 dark:text-slate-200">Rp {{ number_format($item->cost_price, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <button wire:click="toggleStatus({{ $item->id }})" class="focus:outline-none">
                                @if($item->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-500">
                                        Non-aktif
                                    </span>
                                @endif
                            </button>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('inventory.edit', $item) }}" class="p-2 text-slate-300 dark:text-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-lg transition-all" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                <button wire:click="deleteItem({{ $item->id }})" wire:confirm="Hapus item ini?" class="p-2 text-slate-300 dark:text-slate-700 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-all" title="Hapus">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="h-20 w-20 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4 border-2 border-dashed border-slate-200 dark:border-slate-700">
                                    <i class="fa-solid fa-box-open text-3xl text-slate-200 dark:text-slate-700"></i>
                                </div>
                                <h4 class="text-slate-900 dark:text-white font-black text-xl">Stok Kosong</h4>
                                <p class="text-slate-500 dark:text-slate-500 text-sm mt-1 max-w-xs mx-auto">Belum ada item inventori yang terdaftar dalam sistem.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($items->hasPages())
        <div class="px-8 py-5 bg-slate-50/30 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>
