<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $consultations = Consultation::with(['patient', 'doctor'])
            ->latest()
            ->paginate(10);

        return view('consultations.index', compact('consultations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::all();
        $doctors = User::where('role', 'dokter')->where('is_active', true)->get();

        return view('consultations.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'consultation_date' => 'required|date',
            'complaint' => 'required|string',
            'diagnosis' => 'required|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);

        $consultation = Consultation::create($validated);

        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Konsultasi berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Consultation $consultation)
    {
        $consultation->load(['patient', 'doctor']);
        return view('consultations.show', compact('consultation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Consultation $consultation)
    {
        $patients = Patient::all();
        $doctors = User::where('role', 'dokter')->where('is_active', true)->get();

        return view('consultations.edit', compact('consultation', 'patients', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'consultation_date' => 'required|date',
            'complaint' => 'required|string',
            'diagnosis' => 'required|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);

        $consultation->update($validated);

        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Konsultasi berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Consultation $consultation)
    {
        $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);

        $consultation->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Status konsultasi berhasil diperbarui.');
    }


    public function today()
    {
        $today = Carbon::today();
        $consultations = Consultation::with(['patient', 'doctor'])
            ->whereDate('consultation_date', $today)
            ->orderBy('consultation_date')
            ->get();

        return view('consultations.today', compact('consultations'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
