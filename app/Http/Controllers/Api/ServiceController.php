<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search') ?? '';
        $type = $request->input('type') ?? '';
        $page = (int) $request->input('page', 1);

        $cacheKey = CacheHelper::key('services', 'list', [
            'page' => $page,
            'search' => $search,
            'type' => $type,
        ]);

        $result = CacheHelper::remember($cacheKey, 300, function () use ($search, $type) {
            $query = DB::table('services')
                ->select('id', 'uuid', 'code', 'name', 'service_type', 'price', 'duration_days', 'is_active');

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            if ($type !== '') {
                $query->where('service_type', $type);
            }

            return $query->orderBy('name')->paginate(15);
        });

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

    public function show(string $uuid): JsonResponse
    {
        $service = DB::table('services')->where('uuid', $uuid)->first();

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Layanan tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'data' => $service]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|in:konsultasi,ortosis,protesis,terapi,alat',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $service = Service::create($validated);

        CacheHelper::bumpVersion('services');

        return response()->json([
            'success' => true,
            'data' => $service,
            'message' => 'Layanan berhasil ditambahkan.',
        ], 201);
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $service = Service::where('uuid', $uuid)->first();

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Layanan tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'sometimes|required|in:konsultasi,ortosis,protesis,terapi,alat',
            'price' => 'sometimes|required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $service->update($validated);
        CacheHelper::bumpVersion('services');

        return response()->json([
            'success' => true,
            'data' => $service,
            'message' => 'Layanan berhasil diperbarui.',
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $service = Service::where('uuid', $uuid)->first();

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Layanan tidak ditemukan.'], 404);
        }

        $service->delete();
        CacheHelper::bumpVersion('services');

        return response()->json(['success' => true, 'message' => 'Layanan berhasil dihapus.']);
    }

    public function active(): JsonResponse
    {
        $cacheKey = CacheHelper::key('services', 'active');

        $services = CacheHelper::remember($cacheKey, 600, function () {
            return DB::table('services')
                ->where('is_active', true)
                ->select('id', 'uuid', 'code', 'name', 'service_type', 'price', 'duration_days')
                ->orderBy('name')
                ->get();
        });

        return response()->json(['success' => true, 'data' => $services]);
    }
}
