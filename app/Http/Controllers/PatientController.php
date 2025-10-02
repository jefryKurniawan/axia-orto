<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index ()
    {
        $patients = Patient::all();
        return view('patients.index', compact('patients'));
    }

    public function create ()
    {
        return view ('patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:225',
            'gender' => 'required|string',
            'birth_date' => 'required|date',
            'address' => 'required|string',
        ]);

        Patient::create($request->all());
        return
        redirect()->route('patients.index')->with('success', 'Patient created');
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        request->validate([
            'name' => 'required|string|max:225',
            'gender' => 'required|string',
            'birth_date' => 'required|string',
            'address' => 'required|string',
        ]);

        $patient->update($request->all());
        return
        
        redirect()->route('patients.index')->with('success', 'Patient updated');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return

        redirect()->route('patients.index')->with('success', 'Patient deleted');
    }

}
