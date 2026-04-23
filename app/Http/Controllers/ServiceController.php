<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::latest()->paginate(10);
        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:services',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|in:konsultasi,ortosis,protesis,terapi,alat',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        Service::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Layanan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'code' => ['required', Rule::unique('services')->ignore($service->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|in:konsultasi,ortosis,protesis,terapi,alat',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $service->update($validated);

        return redirect()->route('services.show', $service)
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function toggleStatus(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);

        $status = $service->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()
            ->with('success', "Layanan berhasil $status.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
