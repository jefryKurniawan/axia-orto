<x-layouts.app>
    <div>
        <div class="mb-6">
            <a href="{{ route('patients.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Pasien
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-indigo-600">
                <h2 class="text-xl font-semibold text-white">Tambah Pasien Baru</h2>
            </div>
            
            <form wire:submit.prevent="store" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Medical Record & NIK -->
                    <div class="col-span-1 md:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekam Medis</label>
                                <input type="text" wire:model="medical_record_number"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIK (Opsional)</label>
                                <input type="text" wire:model="nik"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="16 digit NIK">
                                @error('nik') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Nama pasien">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="date_of_birth"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('date_of_birth') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select wire:model="gender"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        @error('gender') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="tel" wire:model="phone"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="081234567890">
                        @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Address & Emergency Contact -->
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea wire:model="address"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            rows="3" placeholder="Alamat lengkap pasien"></textarea>
                        @error('address') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Darurat</label>
                        <input type="text" wire:model="emergency_contact"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Nama dan nomor telepon kontak darurat">
                        @error('emergency_contact') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Medical Information -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Asuransi</label>
                        <select wire:model="insurance_type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="mandiri">Mandiri</option>
                            <option value="bpjs">BPJS</option>
                            <option value="asuransi">Asuransi Swasta</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                        <select wire:model="blood_type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Pilih golongan darah</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alergi (Opsional)</label>
                        <textarea wire:model="allergies"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            rows="2" placeholder="Sebutkan alergi obat atau makanan"></textarea>
                        @error('allergies') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('patients.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Simpan Pasien
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>