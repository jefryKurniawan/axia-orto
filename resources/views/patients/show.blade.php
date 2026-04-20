@extends('layouts.app')

@section('title', 'Profil Pasien')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="h-20 w-20 bg-indigo-600 rounded-[28px] flex items-center justify-center text-white text-3xl font-bold shadow-xl shadow-indigo-500/20">
                {{ strtoupper(substr($patient->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $patient->name }}</h1>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $patient->medical_record_number }}</span>
                    <span class="h-1 w-1 bg-slate-300 rounded-full"></span>
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ $patient->insurance_type }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('patients.edit', $patient) }}" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all">
                <i class="fa-solid fa-pen-to-square mr-2"></i> Edit Profil
            </a>
            <a href="{{ route('consultations.create', ['patient_id' => $patient->id]) }}" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-500/20 transition-all">
                <i class="fa-solid fa-stethoscope mr-2"></i> Konsultasi Baru
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar: Info -->
        <div class="space-y-8">
            <div class="bg-white rounded-[32px] p-8 border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 mb-6">Informasi Personal</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase">NIK</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $patient->nik ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase">Jenis Kelamin</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase">Usia</span>
                        <span class="text-sm font-semibold text-slate-700">{{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} Tahun</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase">Gol. Darah</span>
                        <span class="text-sm font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-lg">{{ $patient->blood_type ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Kontak & Alamat</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-phone text-slate-400 mt-1"></i>
                            <span class="text-sm font-medium text-slate-600">{{ $patient->phone ?? '-' }}</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot text-slate-400 mt-1"></i>
                            <span class="text-sm font-medium text-slate-600 leading-relaxed">{{ $patient->address ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-rose-50 rounded-[32px] p-8 border border-rose-100">
                <h3 class="text-lg font-bold text-rose-900 mb-4 flex items-center">
                    <i class="fa-solid fa-virus mr-2"></i> Alergi & Catatan
                </h3>
                <p class="text-sm text-rose-700 font-medium leading-relaxed">
                    {{ $patient->allergies ?? 'Tidak ada riwayat alergi yang tercatat.' }}
                </p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Tabs / Navigation -->
            <div class="bg-white p-2 rounded-2xl border border-slate-200 inline-flex">
                <button class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/20">Riwayat Medis</button>
                <button class="px-6 py-2.5 text-slate-500 hover:text-indigo-600 rounded-xl text-sm font-bold transition-colors">Pesanan Alat</button>
            </div>

            <!-- Recent Consultations -->
            <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900">Konsultasi Terbaru</h2>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ count($patient->consultations) }} Total</span>
                </div>
                <div class="p-0">
                    @forelse($patient->consultations()->latest()->take(5)->get() as $consultation)
                    <div class="p-8 border-b border-slate-50 last:border-0 hover:bg-slate-50/50 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mb-1">{{ $consultation->consultation_date->format('d M Y') }}</p>
                                <h4 class="text-lg font-bold text-slate-900">{{ $consultation->doctor->name }}</h4>
                            </div>
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[10px] font-bold uppercase tracking-wider">Completed</span>
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Keluhan</p>
                                <p class="text-sm text-slate-600 line-clamp-2 leading-relaxed">{{ $consultation->complaint }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Diagnosis</p>
                                <p class="text-sm text-slate-600 line-clamp-2 leading-relaxed font-bold">{{ $consultation->diagnosis }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-4">
                            <i class="fa-solid fa-notes-medical text-3xl"></i>
                        </div>
                        <p class="text-slate-500 font-medium">Belum ada riwayat konsultasi.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Orders Summary -->
            <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm p-8">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Statistik Pasien</h2>
                <div class="grid grid-cols-3 gap-6">
                    <div class="p-6 rounded-2xl bg-indigo-50 border border-indigo-100">
                        <p class="text-[10px] font-bold text-indigo-400 uppercase mb-1">Total Kunjungan</p>
                        <p class="text-3xl font-extrabold text-indigo-700">{{ count($patient->consultations) }}</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-100">
                        <p class="text-[10px] font-bold text-emerald-400 uppercase mb-1">Alat Dipesan</p>
                        <p class="text-3xl font-extrabold text-emerald-700">{{ count($patient->treatmentOrders) }}</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-amber-50 border border-amber-100">
                        <p class="text-[10px] font-bold text-amber-400 uppercase mb-1">Nilai Transaksi</p>
                        <p class="text-xl font-extrabold text-amber-700">Rp {{ number_format($patient->treatmentOrders->sum('total_amount'), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection