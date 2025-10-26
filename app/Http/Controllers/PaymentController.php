<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\TreatmentOrder;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with(['order', 'createdBy'])
            ->latest()
            ->paginate(10);

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $orders = TreatmentOrder::where('status', '!=', 'cancelled')
            ->whereDoesntHave('payments', function ($query) {
                $query->where('status', 'completed');
            })
            ->get();

        return view('payments.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:treatment_orders,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer,debit_card,credit_card',
            'amount' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        Payment::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('payments.index')
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['order.patient', 'createdBy']);
        return view('payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed'
        ]);

        $payment->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function byOrder(TreatmentOrder $order)
    {
        $payments = Payment::where('order_id', $order->id)
            ->with('createdBy')
            ->latest()
            ->get();

        return view('payments.by-order', compact('payments', 'order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
