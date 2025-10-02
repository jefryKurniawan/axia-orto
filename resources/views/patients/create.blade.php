<x-app-layout>
    <div class="p-6">
        <form action="{{ route('patients.store') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Nama" class="border p-2 w-full mb-2" required>

            <select name="gender" class="border p-2 w-full mb-2" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>

            <input type="date" name="birth_date" class="border p-2 w-full mb-2" required>

            <input type="text" name="address" placeholder="Alamat" class="border p-2 w-full mb-2">

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</x-app-layout>
