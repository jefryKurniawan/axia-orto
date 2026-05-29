<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncController extends Controller
{
    public function batch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'operations' => 'required|array|min:1|max:100',
            'operations.*.entity' => 'required|in:patients,consultations',
            'operations.*.action' => 'required|in:create,update,delete',
            'operations.*.data' => 'required|array',
        ]);

        $results = [];

        DB::beginTransaction();

        try {
            foreach ($validated['operations'] as $op) {
                try {
                    match ($op['entity']) {
                        'patients' => $this->syncPatient($op['action'], $op['data']),
                        'consultations' => $this->syncConsultation($op['action'], $op['data']),
                    };
                    $results[] = ['success' => true];
                } catch (\Exception $e) {
                    $results[] = ['success' => false, 'error' => $e->getMessage()];
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Sync gagal: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => ['results' => $results],
            'message' => count(array_filter($results, fn ($r) => $r['success'])) . ' item berhasil di-sync.',
        ]);
    }

    private function syncPatient(string $action, array $data): void
    {
        match ($action) {
            'create' => Patient::create(array_merge($data, [
                'uuid' => $data['uuid'] ?? (string) Str::uuid(),
            ])),
            'update' => Patient::where('uuid', $data['uuid'])->update(
                collect($data)->except(['uuid', 'id'])->toArray()
            ),
            'delete' => Patient::where('uuid', $data['uuid'])->delete(),
        };
    }

    private function syncConsultation(string $action, array $data): void
    {
        match ($action) {
            'create' => Consultation::create($data),
            'update' => Consultation::where('uuid', $data['uuid'])->update(
                collect($data)->except(['uuid', 'id'])->toArray()
            ),
            'delete' => Consultation::where('uuid', $data['uuid'])->delete(),
        };
    }
}
