<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Consultation;
use App\Models\TreatmentOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::latest()
            ->filter(request(['search']))
            ->paginate(10)
            ->withQueryString();

        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'medical_record_number' => 'required|unique:patients|max:20',
            'nik' => 'nullable|size:16',
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'insurance_type' => 'required|in:bpjs,mandiri,asuransi',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'allergies' => 'nullable|string'
        ]);

        // Generate UUID
        $validated['uuid'] = Str::uuid();

        try {
            Patient::create($validated);

            return redirect()->route('patients.index')
                ->with('success', 'Pasien berhasil didaftarkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $patient->load(['consultations.doctor', 'treatmentOrders']);

        // Get recent consultations (last 5)
        $recentConsultations = $patient->consultations()
            ->with('doctor')
            ->latest()
            ->take(5)
            ->get();

        // Get active orders
        $activeOrders = $patient->treatmentOrders()
            ->whereIn('status', ['draft', 'confirmed', 'production'])
            ->latest()
            ->get();

        return view('patients.show', compact('patient', 'recentConsultations', 'activeOrders'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'medical_record_number' => [
                'required',
                'max:20',
                Rule::unique('patients')->ignore($patient->id)
            ],
            'nik' => 'nullable|size:16',
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'insurance_type' => 'required|in:bpjs,mandiri,asuransi',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'allergies' => 'nullable|string'
        ]);

        try {
            $patient->update($validated);

            return redirect()->route('patients.show', $patient)
                ->with('success', 'Data pasien berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        try {
            // Check if patient has related records
            if ($patient->consultations()->exists()) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus pasien yang memiliki riwayat konsultasi.');
            }

            if ($patient->treatmentOrders()->exists()) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus pasien yang memiliki order treatment.');
            }

            $patient->delete();

            return redirect()->route('patients.index')
                ->with('success', 'Pasien berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function medicalHistory(Patient $patient)
    {
        $patient->load([
            'consultations.doctor',
            'treatmentOrders.orderItems.service',
            'treatmentOrders.payments'
        ]);

        // Get all consultations ordered by date
        $consultations = $patient->consultations()
            ->with('doctor')
            ->latest('consultation_date')
            ->get();

        // Get all treatment orders with related data
        $treatmentOrders = $patient->treatmentOrders()
            ->with(['orderItems.service', 'payments'])
            ->latest()
            ->get();
        // Calculate statistics
        $stats = [
            'total_consultations' => $consultations->count(),
            'total_orders' => $treatmentOrders->count(),
            'completed_orders' => $treatmentOrders->where('status', 'delivered')->count(),
            'total_spent' => $treatmentOrders->sum('total_amount')
        ];

        return view('patients.medical-history', compact(
            'patient',
            'consultations',
            'treatmentOrders',
            'stats'
        ));
    }


    /**
     * Search patients (for AJAX requests or API)
     */
    public function search(Request $request)
    {
        $search = $request->get('q');

        $patients = Patient::where('name', 'like', "%{$search}%")
            ->orWhere('medical_record_number', 'like', "%{$search}%")
            ->orWhere('nik', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'medical_record_number', 'name', 'phone']);

        return response()->json($patients);
    }

    /**
     * Calculate patient age from date of birth
     */
    private function calculateAge($dateOfBirth)
    {
        return Carbon::parse($dateOfBirth)->age;
    }

    /**
     * Get patient statistics for dashboard
     */
    public function stats()
    {
        $totalPatients = Patient::count();
        $newPatientsThisMonth = Patient::whereMonth('created_at', now()->month)->count();
        $patientsWithConsultations = Patient::has('consultations')->count();

        $ageGroups = [
            'child' => Patient::where('date_of_birth', '>', now()->subYears(12))->count(),
            'teen' => Patient::whereBetween('date_of_birth', [now()->subYears(19), now()->subYears(13)])->count(),
            'adult' => Patient::whereBetween('date_of_birth', [now()->subYears(59), now()->subYears(20)])->count(),
            'senior' => Patient::where('date_of_birth', '<', now()->subYears(60))->count(),
        ];

        return response()->json([
            'total_patients' => $totalPatients,
            'new_this_month' => $newPatientsThisMonth,
            'with_consultations' => $patientsWithConsultations,
            'age_groups' => $ageGroups
        ]);
    }
}
