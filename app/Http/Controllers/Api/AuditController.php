<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $page = (int) $request->input('page', 1);
        $perPage = 20;
        $auditableType = $request->input('auditable_type') ?? '';
        $userId = $request->input('user_id') ?? '';
        $dateFrom = $request->input('date_from') ?? '';
        $dateTo = $request->input('date_to') ?? '';

        $query = AuditLog::with('user');

        if ($auditableType !== '') {
            $query->where('auditable_type', $auditableType);
        }

        if ($userId !== '') {
            $query->where('user_id', $userId);
        }

        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $result = $query->orderBy('created_at', 'desc')->paginate($perPage);

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

    public function forModel(string $type, int $id): JsonResponse
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        // Map short names to full model classes
        $modelMap = [
            'patient' => 'App\Models\Patient',
            'consultation' => 'App\Models\Consultation',
            'order' => 'App\Models\TreatmentOrder',
            'payment' => 'App\Models\Payment',
            'inventory' => 'App\Models\InventoryItem',
        ];

        $fullType = $modelMap[$type] ?? $type;

        $logs = AuditLog::with('user')
            ->forModel($fullType, $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }
}
