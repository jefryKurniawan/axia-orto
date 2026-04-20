<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Laporan - AxiaOrto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .print-container { border: none; box-shadow: none; margin: 0; padding: 0; }
        }
        @page { size: A4; margin: 1cm; }
    </style>
</head>
<body class="bg-slate-100 font-sans p-8">
    <div class="no-print mb-6 flex justify-between items-center max-w-4xl mx-auto">
        <button onclick="window.history.back()" class="px-4 py-2 bg-slate-600 text-white rounded-lg font-bold text-sm hover:bg-slate-700 transition-all">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
        </button>
        <button onclick="window.print()" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg font-bold text-sm hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all">
            <i class="fa-solid fa-print mr-2"></i> Cetak / Simpan PDF
        </button>
    </div>

    <div class="print-container bg-white border border-slate-200 shadow-xl max-w-4xl mx-auto p-12 min-h-[29.7cm]">
        <!-- Kop Surat / Header -->
        <div class="flex items-center justify-between border-b-4 border-slate-900 pb-6 mb-8">
            <div class="flex items-center">
                <div class="h-16 w-16 bg-slate-900 rounded-2xl flex items-center justify-center mr-4">
                    <span class="text-white text-3xl font-black">A</span>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tighter">AXIA ORTO</h1>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-none">Prosthetic & Orthotic Clinic</p>
                    <p class="text-[10px] font-medium text-slate-400 mt-1">Jl. Merdeka No. 123, Jakarta Selatan | Telp: (021) 1234-5678</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight">{{ $title }}</h2>
                <p class="text-xs font-bold text-slate-500 mt-1">Tanggal: {{ now()->translatedFormat('d F Y') }}</p>
                <p class="text-[10px] font-medium text-slate-400">Export ID: {{ strtoupper(uniqid('EXP-')) }}</p>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="mb-8 text-xs">
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 w-1/2">
                <p class="font-bold text-slate-400 uppercase tracking-widest mb-1">Dibuat Oleh</p>
                <p class="font-black text-slate-900">{{ auth()->user()->name }}</p>
                <p class="text-slate-500 mt-0.5">{{ auth()->user()->role ?? 'Administrator' }}</p>
            </div>
        </div>

        <!-- Table Data -->
        <table class="w-full text-left border-collapse border border-slate-200">
            <thead>
                <tr class="bg-slate-900 text-white">
                    <th class="px-3 py-3 text-[10px] font-black uppercase tracking-widest border border-slate-700 w-10">NO</th>
                    @foreach($headers as $header)
                    <th class="px-3 py-3 text-[10px] font-black uppercase tracking-widest border border-slate-700">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($data as $index => $row)
                <tr class="odd:bg-white even:bg-slate-50/50">
                    <td class="px-3 py-3 text-[10px] font-medium border border-slate-100">{{ $index + 1 }}</td>
                    @foreach($row as $cell)
                    <td class="px-3 py-3 text-[10px] font-medium border border-slate-100">{{ $cell }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer -->
        <div class="mt-20 flex justify-end">
            <div class="text-center w-64 border-t border-slate-200 pt-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-12">Penanggung Jawab</p>
                <p class="text-sm font-black text-slate-900">( {{ auth()->user()->name }} )</p>
            </div>
        </div>

        <div class="absolute bottom-8 left-12 right-12 text-center text-[8px] text-slate-300 font-medium">
            Dokumen ini dihasilkan secara otomatis oleh AxiaOrto Clinic Management System pada {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
