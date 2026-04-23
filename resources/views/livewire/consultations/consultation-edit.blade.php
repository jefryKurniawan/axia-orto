<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('consultations.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Edit Konsultasi</h1>
            <p class="text-slate-500 mt-1">Perbarui informasi konsultasi {{ $consultation->patient->name }}</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Informasi Konsultasi</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Pasien <span class="text-rose-500">*</span></label>
                    <select wire:model="patient_id" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        <option value="">Pilih Pasien</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->medical_record_number }})</option>
                        @endforeach
                    </select>
                    @error('patient_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Dokter <span class="text-rose-500">*</span></label>
                    <select wire:model="doctor_id" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        <option value="">Pilih Dokter</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                    @error('doctor_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Tanggal Konsultasi <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" wire:model="consultation_date" required 
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                    @error('consultation_date') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Status <span class="text-rose-500">*</span></label>
                    <select wire:model="status" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        <option value="scheduled">Dijadwalkan</option>
                        <option value="in_progress">Berlangsung</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                    @error('status') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Informasi Medis</h2>
            
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Keluhan <span class="text-rose-500">*</span></label>
                    <textarea wire:model="complaint" rows="3" required 
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"></textarea>
                    @error('complaint') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Diagnosis <span class="text-rose-500">*</span></label>
                    <textarea wire:model="diagnosis" rows="3" required 
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"></textarea>
                    @error('diagnosis') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Rencana Pengobatan</label>
                    <textarea wire:model="treatment_plan" rows="3" 
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"></textarea>
                    @error('treatment_plan') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Catatan Tambahan</label>
                    <textarea wire:model="notes" rows="2" 
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"></textarea>
                    @error('notes') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Tanggal Tindak Lanjut</label>
                    <input type="date" wire:model="follow_up_date" 
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                    @error('follow_up_date') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('consultations.index') }}" class="px-8 py-4 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</a>
            <button type="submit" class="px-8 py-4 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/25 transition-all" wire:loading.attr="disabled">
                <span wire:loading.remove><i class="fa-solid fa-save mr-2"></i> Perbarui Konsultasi</span>
                <span wire:loading><i class="fa-solid fa-spinner fa-spin mr-2"></i> Menyimpan...</span>
            </button>
        </div>
    </form>
</div>