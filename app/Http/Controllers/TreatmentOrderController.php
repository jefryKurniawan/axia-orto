<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TreatmentOrder;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Service;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class TreatmentOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = TreatmentOrder::with(['patient', 'createdBy'])
            ->latest()
            ->paginate(10);

        return view('treatment-orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::all();
        $consultations = Consultation::where('status', 'completed')->get();
        $services = Service::where('is_active', true)->get();

        return view('treatment-orders.create', compact('patients', 'consultations', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date|after:order_date',
            'notes' => 'nullable|string',
            'services' => 'required|array',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1',
            'services.*.specifications' => 'nullable|array'
        ]);

        // Generate order number
        $orderNumber = 'ORD-' . Str::upper(Str::random(8));

        // Calculate total amount
        $totalAmount = 0;
        foreach ($validated['services'] as $serviceItem) {
            $service = Service::find($serviceItem['service_id']);
            $totalAmount += $service->price * $serviceItem['quantity'];
        }

        // Create order
        $order = TreatmentOrder::create([
            'order_number' => $orderNumber,
            'patient_id' => $validated['patient_id'],
            'consultation_id' => $validated['consultation_id'],
            'order_date' => $validated['order_date'],
            'delivery_date' => $validated['delivery_date'],
            'total_amount' => $totalAmount,
            'notes' => $validated['notes'],
            'created_by' => auth()->id(),
        ]);

        // Create order items
        foreach ($validated['services'] as $serviceItem) {
            $service = Service::find($serviceItem['service_id']);

            OrderItem::create([
                'order_id' => $order->id,
                'service_id' => $serviceItem['service_id'],
                'quantity' => $serviceItem['quantity'],
                'unit_price' => $service->price,
                'total_price' => $service->price * $serviceItem['quantity'],
                'specifications' => $serviceItem['specifications'] ?? null,
            ]);
        }

        return redirect()->route('treatment-orders.show', $order)
            ->with('success', 'Order treatment berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TreatmentOrder $order)
    {
        $order->load(['patient', 'consultation', 'orderItems.service', 'createdBy']);
        return view('treatment-orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TreatmentOrder $order)
    {
        $patients = Patient::all();
        $consultations = Consultation::where('status', 'completed')->get();
        $services = Service::where('is_active', true)->get();
        $order->load(['patient', 'orderItems.service']);

        return view('treatment-orders.edit', compact('order', 'patients', 'consultations', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TreatmentOrder $order)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date|after:order_date',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,confirmed,production,ready,delivered,cancelled'
        ]);

        $order->update($validated);

        return redirect()->route('treatment-orders.show', $order)
            ->with('success', 'Order treatment berhasil diperbarui.');
    }

    public function updateStatus(Request $request, TreatmentOrder $order)
    {
        $request->validate([
            'status' => 'required|in:draft,confirmed,production,ready,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Status order berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
