<div class="space-y-6 pb-20">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-6">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[8px] opacity-50"></i>
                <span class="text-slate-600 dark:text-slate-300">Layanan & Harga</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Katalog Layanan</h1>
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
            <a href="{{ route('services.create') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-indigo-600 dark:bg-indigo-500 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-all duration-200 shadow-lg shadow-indigo-500/20 active:scale-95">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Layanan
            </a>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-4 flex-1">
            <div class="relative flex-1 max-w-lg">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 dark:text-slate-600 text-sm"></i>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" 
                       placeholder="Cari Nama atau Kode Layanan..." 
                       class="block w-full pl-11 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-200 placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
            </div>
            
            <button x-data="{ 
                        toggle() {
                            const allIds = @js($services->pluck('id')->toArray());
                            if ($wire.selectedRows.length === allIds.length) {
                                $wire.selectedRows = [];
                            } else {
                                $wire.selectedRows = allIds;
                            }
                        } 
                    }"
                    @click="toggle"
                    class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-500 transition-all active:scale-95">
                <span x-text="$wire.selectedRows.length === @js($services->count()) ? 'Deselect All' : 'Select All'"></span>
            </button>
        </div>

        <div class="flex items-center gap-2">
            <select wire:model.live="selectedType" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 outline-none cursor-pointer">
                <option value="">Semua Kategori</option>
                @foreach($types as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>

            <button wire:click="resetFilters" class="p-2.5 text-slate-400 dark:text-slate-600 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all" title="Reset Filter">
                <i class="fa-solid fa-filter-circle-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($services as $service)
        <div wire:key="service-{{ $service->id }}" 
             class="bg-white dark:bg-slate-900 p-6 rounded-2xl border transition-all relative group flex flex-col h-full {{ in_array($service->id, $selectedRows) ? 'border-indigo-500 ring-2 ring-indigo-500/20 shadow-lg' : 'border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl dark:hover:shadow-indigo-500/5' }}">
            
            <!-- Checkbox Overlay -->
            <div class="absolute top-4 right-4 z-10">
                <input type="checkbox" 
                       wire:model.live="selectedRows" 
                       value="{{ $service->id }}"
                       class="w-5 h-5 text-indigo-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded-lg focus:ring-indigo-500 focus:ring-2 transition-all cursor-pointer">
            </div>

            <div class="flex justify-between items-start mb-6">
                <div class="h-14 w-14 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-hand-holding-medical text-2xl"></i>
                </div>
                <div class="flex space-x-1">
                    <a href="{{ route('services.edit', $service) }}" class="p-2 text-slate-300 dark:text-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-lg transition-all" title="Edit">
                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                    </a>
                    <button wire:click="deleteService({{ $service->id }})" wire:confirm="Hapus layanan ini?" class="p-2 text-slate-300 dark:text-slate-700 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-all" title="Hapus">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </div>
            </div>
            
            <div class="flex-1">
                <div class="flex items-center space-x-2 mb-1">
                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[9px] font-black uppercase rounded tracking-tighter">{{ $service->code }}</span>
                </div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $service->name }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-3 leading-relaxed">{{ $service->description ?? 'Tidak ada deskripsi layanan.' }}</p>
            </div>

            <div class="mt-8 flex items-center justify-between border-t border-slate-50 dark:border-slate-800/50 pt-5">
                <p class="text-xl font-black text-slate-900 dark:text-white tracking-tighter">
                    <span class="text-xs text-slate-400 font-bold mr-1">Rp</span>{{ number_format($service->price, 0, ',', '.') }}
                </p>
                @php
                    $typeColors = [
                        'konsultasi' => 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400',
                        'ortosis' => 'bg-purple-100 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400',
                        'protesis' => 'bg-pink-100 dark:bg-pink-500/10 text-pink-700 dark:text-pink-400',
                        'terapi' => 'bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400',
                        'alat' => 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
                    ];
                    $color = $typeColors[$service->service_type] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $color }}">
                    {{ str_replace('_', ' ', $service->service_type) }}
                </span>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 bg-white dark:bg-slate-900 rounded-[2rem] border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center">
            <div class="h-20 w-20 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-notes-medical text-3xl text-slate-200 dark:text-slate-700"></i>
            </div>
            <h4 class="text-slate-900 dark:text-white font-black text-xl">Katalog Kosong</h4>
            <p class="text-slate-500 dark:text-slate-500 text-sm mt-1 max-w-xs text-center">Belum ada layanan yang ditambahkan atau filter tidak menemukan hasil.</p>
        </div>
        @endforelse
    </div>

    @if($services->hasPages())
    <div class="mt-10">
        {{ $services->links() }}
    </div>
    @endif
</div>
