<x-app-layout>
    <div class="p-6">
        <form action="{{ route('patients.update',$patient->id) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="text" name="name" value="{{ $patient->name }}" class="border p-2 w-full mb-2" required>

            <select name="gender" class="border p-2 w-full mb-2" required>
                <option value="Laki-laki" {{ $patient->gender == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ $patient->gender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>

            <input type="date" name="birth_date" value="{{ $patient->birth_date }}" class="border p-2 w-full mb-2" required>

            <input type="text" name="address" value="{{ $patient->address }}" class="border p-2 w-full mb-2">

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</x-app-layout>
