<div class="space-y-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('consultations.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Jadwalkan Konsultasi</h1>
            <p class="text-slate-500 mt-1">Buat janji temu medis baru untuk pasien.</p>
        </div>
    </div>

    <form wire:submit="store" class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Basic Info -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                        <i class="fa-solid fa-user-md mr-3 text-indigo-500"></i> Detail Pemeriksaan
                    </h2>
                    
                    <div class="space-y-6">
                        <div class="relative" x-data="{ open: true }">
                            <label class="text-sm font-semibold text-slate-700">Cari Pasien <span class="text-rose-500">*</span></label>
                            <div class="relative mt-2">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                                </div>
                                <input wire:model.live.debounce.300ms="patient_search" type="text" 
                                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" 
                                       placeholder="Ketik nama atau MRN pasien...">
                            </div>
                            
                            @if(count($patients) > 0)
                            <div class="absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden">
                                @foreach($patients as $patient)
                                <button type="button" wire:click="selectPatient({{ $patient->id }})" class="w-full flex items-center px-4 py-3 hover:bg-slate-50 transition-colors text-left border-b border-slate-50 last:border-0">
                                    <div class="h-8 w-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs mr-3">
                                        {{ strtoupper(substr($patient->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $patient->name }}</p>
                                        <p class="text-[10px] text-slate-500">{{ $patient->medical_record_number }}</p>
                                    </div>
                                </button>
                                @endforeach
                            </div>
                            @endif
                            @error('patient_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Dokter Pemeriksa <span class="text-rose-500">*</span></label>
                                <select wire:model="doctor_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                    <option value="">Pilih Dokter</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                    @endforeach
                                </select>
                                @error('doctor_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Waktu & Tanggal <span class="text-rose-500">*</span></label>
                                <input wire:model="consultation_date" type="datetime-local" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                @error('consultation_date') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Keluhan Utama <span class="text-rose-500">*</span></label>
                            <textarea wire:model="complaint" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Jelaskan keluhan yang dialami pasien..."></textarea>
                            @error('complaint') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                        <i class="fa-solid fa-notes-medical mr-3 text-emerald-500"></i> Hasil Diagnosis & Rencana
                    </h2>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Diagnosis</label>
                            <textarea wire:model="diagnosis" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Hasil observasi medis..."></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Rencana Tindakan / Treatment Plan</label>
                            <textarea wire:model="treatment_plan" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Langkah medis selanjutnya..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Secondary Info -->
            <div class="space-y-8">
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                        <i class="fa-solid fa-gear mr-3 text-amber-500"></i> Pengaturan
                    </h2>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Status Konsultasi</label>
                            <select wire:model="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                <option value="scheduled">Terjadwal</option>
                                <option value="in_progress">Sedang Berlangsung</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Tanggal Follow Up</label>
                            <input wire:model="follow_up_date" type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Catatan Internal</label>
                            <textarea wire:model="notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Hanya terlihat oleh staf..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                    <div class="flex items-start">
                        <i class="fa-solid fa-circle-info text-indigo-500 mt-1 mr-3"></i>
                        <p class="text-xs text-indigo-700 leading-relaxed">Pastikan pasien sudah terdaftar di sistem sebelum menjadwalkan konsultasi. Gunakan kolom pencarian untuk menemukan pasien berdasarkan nama atau MRN.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('consultations.index') }}" class="px-8 py-4 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</a>
            <button type="submit" class="px-8 py-4 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/25 transition-all">
                <i class="fa-solid fa-calendar-check mr-2"></i> Simpan Jadwal
            </button>
        </div>
    </form>
</div>
