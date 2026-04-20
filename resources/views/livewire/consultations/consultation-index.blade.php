<div class="space-y-6 pb-20">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-6">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[8px] opacity-50"></i>
                <span class="text-slate-600 dark:text-slate-300">Daftar Konsultasi</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Manajemen Konsultasi</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.export-pdf', ['type' => 'consultations']) }}" target="_blank"
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
            <a href="{{ route('consultations.create') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-indigo-600 dark:bg-indigo-500 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-all duration-200 shadow-lg shadow-indigo-500/20 active:scale-95">
                <i class="fa-solid fa-plus mr-2"></i> Konsultasi Baru
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
                   placeholder="Cari Pasien, Dokter, atau Keluhan..." 
                   class="block w-full pl-11 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-200 placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
        </div>

        <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0">
            <select wire:model.live="selectedStatus" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 outline-none cursor-pointer min-w-[140px]">
                <option value="">Semua Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            
            <select wire:model.live="selectedDoctor" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 outline-none cursor-pointer min-w-[140px]">
                <option value="">Semua Dokter</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                @endforeach
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
                                                const allIds = @js($consultations->pluck('id')->toArray());
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
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Waktu & Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Identitas Pasien</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Dokter</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Keluhan</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                    @forelse($consultations as $consultation)
                    <tr wire:key="consultation-{{ $consultation->id }}" 
                        class="transition-colors group {{ in_array($consultation->id, $selectedRows) ? 'bg-indigo-50/50 dark:bg-indigo-500/10' : 'hover:bg-slate-50/50 dark:hover:bg-indigo-500/5' }}">
                        <td class="pl-6 py-5 whitespace-nowrap">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" 
                                       wire:model.live="selectedRows" 
                                       value="{{ $consultation->id }}"
                                       class="w-4 h-4 text-indigo-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500 focus:ring-2 transition-all cursor-pointer">
                            </div>
                        </td>
                        <td class="px-4 py-5 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($consultation->consultation_date)->format('H:i') }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ \Carbon\Carbon::parse($consultation->consultation_date)->translatedFormat('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 font-black text-xs mr-3">
                                    {{ strtoupper(substr($consultation->patient->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 dark:text-white leading-none">{{ $consultation->patient->name }}</p>
                                    <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 mt-1.5 uppercase tracking-tighter">MRN: {{ $consultation->patient->medical_record_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <p class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $consultation->doctor->name }}</p>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-500 line-clamp-1 max-w-xs leading-relaxed italic">"{{ $consultation->complaint }}"</p>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'scheduled' => 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400',
                                    'in_progress' => 'bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400',
                                    'completed' => 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
                                    'cancelled' => 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400',
                                ];
                                $color = $statusColors[$consultation->status] ?? 'bg-slate-50 text-slate-600';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $color }}">
                                {{ str_replace('_', ' ', $consultation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('consultations.show', $consultation) }}" class="p-2 text-slate-300 dark:text-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-lg transition-all" title="Detail">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('consultations.edit', $consultation) }}" class="p-2 text-slate-300 dark:text-slate-700 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-lg transition-all" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                <button wire:click="deleteConsultation({{ $consultation->id }})" wire:confirm="Hapus jadwal konsultasi ini?" class="p-2 text-slate-300 dark:text-slate-700 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-all" title="Hapus">
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
                                    <i class="fa-solid fa-calendar-xmark text-3xl text-slate-200 dark:text-slate-700"></i>
                                </div>
                                <h4 class="text-slate-900 dark:text-white font-black text-xl">Tidak Ada Jadwal</h4>
                                <p class="text-slate-500 dark:text-slate-500 text-sm mt-1 max-w-xs mx-auto">Silakan buat jadwal konsultasi baru untuk memulai rekam medis pasien.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($consultations->hasPages())
        <div class="px-8 py-5 bg-slate-50/30 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800">
            {{ $consultations->links() }}
        </div>
        @endif
    </div>
</div>
