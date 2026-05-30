<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateReport;
use App\Models\ExportJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->input('page', 1);
        $perPage = 10;

        $query = ExportJob::where('requested_by', auth()->id())
            ->orderBy('created_at', 'desc');

        $result = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $result->items(),
            'meta' => [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'report_type' => 'required|in:revenue,patients,orders,payments',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $exportJob = ExportJob::create([
            'uuid' => Str::uuid(),
            'requested_by' => auth()->id(),
            'report_type' => $request->input('report_type'),
            'parameters' => array_filter([
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ]),
            'status' => 'pending',
        ]);

        GenerateReport::dispatch($exportJob->id);

        return response()->json([
            'success' => true,
            'data' => $exportJob,
            'message' => 'Export sedang diproses.',
        ], 202);
    }

    public function show(string $uuid): JsonResponse
    {
        $exportJob = ExportJob::where('requested_by', auth()->id())
            ->where('uuid', $uuid)
            ->first();

        if (!$exportJob) {
            return response()->json(['success' => false, 'message' => 'Export tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $exportJob,
        ]);
    }

    public function download(string $uuid): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $exportJob = ExportJob::where('requested_by', auth()->id())
            ->where('uuid', $uuid)
            ->first();

        if (!$exportJob) {
            return response()->json(['success' => false, 'message' => 'Export tidak ditemukan.'], 404);
        }

        if ($exportJob->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'Export belum selesai.'], 422);
        }

        if (!$exportJob->file_path || !Storage::disk('local')->exists($exportJob->file_path)) {
            return response()->json(['success' => false, 'message' => 'File export tidak ditemukan.'], 404);
        }

        $filename = "laporan_{$exportJob->report_type}_{$exportJob->created_at->format('Ymd')}.csv";

        return Storage::disk('local')->download($exportJob->file_path, $filename);
    }
}
