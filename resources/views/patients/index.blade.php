<x-app-layout>
    <div class="p-6">
        <a href="{{ route('patients.create') }}" class="btn btn-primary mb-4">Tambah Pasien</a>

        <table class="table-auto w-full border" id="patientTable">
            <thead>
                <tr>
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Nama</th>
                    <th class="border p-2">Jenis Kelamin</th>
                    <th class="border p-2">Tanggal Lahir</th>
                    <th class="border p-2">Alamat</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($patients as $p)
                <tr>
                    <td class="border p-2">{{ $p->id }}</td>
                    <td class="border p-2">{{ $p->name }}</td>
                    <td class="border p-2">{{ $p->gender }}</td>
                    <td class="border p-2">{{ $p->birth_date }}</td>
                    <td class="border p-2">{{ $p->address }}</td>
                    <td class="border p-2">
                        <a href="{{ route('patients.edit',$p->id) }}" class="text-blue-600">Edit</a>
                        <form action="{{ route('patients.destroy',$p->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 ml-2 deleteBtn">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).on('click','.deleteBtn',function(e){
            if(!confirm('Yakin hapus pasien ini?')) {
                e.preventDefault();
            }
        });
    </script>
</x-app-layout>
