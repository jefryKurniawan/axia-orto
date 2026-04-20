<div class="space-y-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('patients.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Tambah Pasien</h1>
            <p class="text-slate-500 mt-1">Lengkapi formulir di bawah untuk mendaftarkan pasien baru.</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-8">
        <!-- Main Info -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                <i class="fa-solid fa-user-tag mr-3 text-indigo-500"></i> Informasi Identitas
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input wire:model="name" type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Masukkan nama sesuai KTP">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">No. Rekam Medis (MRN) <span class="text-rose-500">*</span></label>
                    <input wire:model="medical_record_number" type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all bg-slate-50">
                    @error('medical_record_number') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">NIK</label>
                    <input wire:model="nik" type="text" maxlength="16" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="16 digit nomor induk kependudukan">
                    @error('nik') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Tanggal Lahir <span class="text-rose-500">*</span></label>
                    <input wire:model="date_of_birth" type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                    @error('date_of_birth') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input wire:model="gender" type="radio" value="L" class="peer hidden">
                            <div class="py-3 text-center rounded-xl border border-slate-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 transition-all text-sm font-medium">Laki-laki</div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input wire:model="gender" type="radio" value="P" class="peer hidden">
                            <div class="py-3 text-center rounded-xl border border-slate-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-600 transition-all text-sm font-medium">Perempuan</div>
                        </label>
                    </div>
                    @error('gender') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Golongan Darah</label>
                    <select wire:model="blood_type" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        <option value="">Pilih</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                    @error('blood_type') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Medical & Contact -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fa-solid fa-file-medical mr-3 text-emerald-500"></i> Medis & Asuransi
                </h2>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Jenis Asuransi <span class="text-rose-500">*</span></label>
                        <select wire:model="insurance_type" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                            <option value="">Pilih Asuransi</option>
                            <option value="bpjs">BPJS Kesehatan</option>
                            <option value="mandiri">Mandiri (Umum)</option>
                            <option value="asuransi">Asuransi Swasta</option>
                        </select>
                        @error('insurance_type') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Alergi</label>
                        <textarea wire:model="allergies" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Contoh: Penisilin, Kacang, dsb."></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fa-solid fa-address-book mr-3 text-amber-500"></i> Kontak & Alamat
                </h2>
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">No. Telepon</label>
                            <input wire:model="phone" type="tel" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="08xxx">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Kontak Darurat</label>
                            <input wire:model="emergency_contact" type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Nama - Hubungan">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Alamat Lengkap</label>
                        <textarea wire:model="address" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Jl. Contoh No. 123..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('patients.index') }}" class="px-8 py-4 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</a>
            <button type="submit" class="px-8 py-4 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/25 transition-all">
                <i class="fa-solid fa-save mr-2"></i> Simpan Pasien
            </button>
        </div>
    </form>
</div>
