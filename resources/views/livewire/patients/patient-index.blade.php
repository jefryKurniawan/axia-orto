<div class="space-y-6 pb-20">
    <!-- Page Header: Symmetrical & Edge-to-Edge -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-6">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[8px] opacity-50"></i>
                <span class="text-slate-600 dark:text-slate-300">Manajemen Pasien</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Data Pasien</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.export-pdf', ['type' => 'patients']) }}" target="_blank"
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
            <a href="{{ route('patients.create') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-indigo-600 dark:bg-indigo-500 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-all duration-200 shadow-lg shadow-indigo-500/20 active:scale-95">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Pasien Baru
            </a>
        </div>
    </div>

    <!-- Action Bar: Compact & Professional -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Search on the Left -->
        <div class="relative flex-1 max-w-lg">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 dark:text-slate-600 text-sm"></i>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" 
                   placeholder="Cari Nama, MRN, atau NIK..." 
                   class="block w-full pl-11 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-900 dark:text-slate-200 placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 transition-all outline-none">
        </div>

        <!-- Filters on the Right -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0">
            <select wire:model.live="gender" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 outline-none cursor-pointer">
                <option value="">Semua Gender</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
            
            <select wire:model.live="insurance" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 outline-none cursor-pointer">
                <option value="">Asuransi</option>
                <option value="bpjs">BPJS</option>
                <option value="mandiri">MANDIRI</option>
                <option value="asuransi">LAINNYA</option>
            </select>

            <button wire:click="resetFilters" class="p-2.5 text-slate-400 dark:text-slate-600 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all" title="Reset Filter">
                <i class="fa-solid fa-filter-circle-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Data Table: Premium & Clean -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                        <th class="pl-6 py-4 w-10">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" 
                                       class="w-4 h-4 text-indigo-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500 focus:ring-2 transition-all cursor-pointer"
                                       x-data="{ 
                                            checkAll() {
                                                const allIds = @js($patients->pluck('id')->toArray());
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
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">No. MRN</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Identitas Pasien</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">Info Medis</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Metode Bayar</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($patients as $patient)
                    <tr wire:key="patient-{{ $patient->id }}" 
                        class="transition-colors group {{ in_array($patient->id, $selectedRows) ? 'bg-indigo-50/50 dark:bg-indigo-500/10' : 'hover:bg-slate-50 dark:hover:bg-slate-800/30' }}">
                        <td class="pl-6 py-5 whitespace-nowrap">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" 
                                       wire:model.live="selectedRows" 
                                       value="{{ $patient->id }}"
                                       class="w-4 h-4 text-indigo-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500 focus:ring-2 transition-all cursor-pointer">
                            </div>
                        </td>
                        <td class="px-4 py-5 whitespace-nowrap">
                            <span class="px-2 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[10px] font-black rounded-lg border border-indigo-100 dark:border-indigo-500/20">
                                {{ $patient->medical_record_number }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 font-black text-sm">
                                    {{ strtoupper(substr($patient->name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white leading-none">{{ $patient->name }}</p>
                                    <p class="text-[10px] font-medium text-slate-400 dark:text-slate-500 mt-1.5 uppercase">NIK: {{ $patient->nik ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="inline-flex items-center text-[10px] font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md">
                                    <i class="fa-solid fa-cake-candles mr-1.5 text-slate-400"></i>
                                    {{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} THN
                                </span>
                                <span class="inline-flex items-center text-[10px] font-black uppercase {{ $patient->gender == 'L' ? 'text-blue-500 dark:text-blue-400' : 'text-rose-500 dark:text-rose-400' }}">
                                    <i class="fa-solid {{ $patient->gender == 'L' ? 'fa-mars' : 'fa-venus' }} mr-1 text-[8px]"></i>
                                    {{ $patient->gender == 'L' ? 'LAKI-LAKI' : 'PEREMPUAN' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $insuranceColors = [
                                    'bpjs' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 ring-emerald-100 dark:ring-emerald-500/20',
                                    'mandiri' => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 ring-blue-100 dark:ring-blue-500/20',
                                    'asuransi' => 'bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 ring-purple-100 dark:ring-purple-500/20',
                                ];
                                $color = $insuranceColors[$patient->insurance_type] ?? 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 ring-slate-100 dark:ring-slate-800';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ring-1 ring-inset {{ $color }}">
                                {{ $patient->insurance_type ?? 'UMUM' }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('patients.show', $patient) }}" class="p-2 text-slate-400 dark:text-slate-600 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-lg transition-all" title="Detail">
                                    <i class="fa-solid fa-circle-info text-base"></i>
                                </a>
                                <a href="{{ route('patients.edit', $patient) }}" class="p-2 text-slate-400 dark:text-slate-600 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-lg transition-all" title="Edit">
                                    <i class="fa-solid fa-user-pen text-base"></i>
                                </a>
                                <button wire:click="deletePatient({{ $patient->id }})" 
                                        wire:confirm="Hapus data pasien ini?"
                                        class="p-2 text-slate-400 dark:text-slate-600 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-all" title="Hapus">
                                    <i class="fa-solid fa-user-xmark text-base"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="h-20 w-20 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4 border-2 border-dashed border-slate-200 dark:border-slate-700">
                                    <i class="fa-solid fa-user-slash text-3xl text-slate-300 dark:text-slate-600"></i>
                                </div>
                                <h4 class="text-slate-900 dark:text-white font-bold text-lg">Tidak Ada Data Pasien</h4>
                                <p class="text-slate-500 dark:text-slate-500 text-sm mt-1">Coba sesuaikan pencarian atau filter Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($patients->hasPages())
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800">
            {{ $patients->links() }}
        </div>
        @endif
    </div>
</div>
