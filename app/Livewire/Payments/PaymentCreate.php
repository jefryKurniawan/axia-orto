<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use App\Models\TreatmentOrder;
use Livewire\Component;

class PaymentCreate extends Component
{
    public $order_id;
    public $payment_date;
    public $payment_method = 'transfer';
    public $amount = 0;
    public $reference_number;
    public $notes;

    public $selected_order;

    protected $rules = [
        'order_id' => 'required|exists:treatment_orders,id',
        'payment_date' => 'required|date',
        'payment_method' => 'required|in:cash,transfer,debit_card,credit_card',
        'amount' => 'required|numeric|min:1',
        'reference_number' => 'nullable|string|max:100',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->payment_date = now()->format('Y-m-d');
    }

    public function updatedOrderId($value)
    {
        $this->selected_order = TreatmentOrder::with('patient')->find($value);
        if ($this->selected_order) {
            $this->amount = $this->selected_order->total_amount;
        }
    }

    public function store()
    {
        $this->validate();

        Payment::create([
            'order_id' => $this->order_id,
            'payment_date' => $this->payment_date,
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'reference_number' => $this->reference_number,
            'notes' => $this->notes,
            'status' => 'completed',
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', 'Pembayaran berhasil dicatat.');
        return redirect()->route('payments.index');
    }

    public function render()
    {
        $orders = TreatmentOrder::where('status', '!=', 'cancelled')
            ->whereDoesntHave('payments', function ($query) {
                $query->where('status', 'completed');
            })
            ->latest()
            ->get();

        return view('livewire.payments.payment-create', [
            'orders' => $orders
        ]);
    }
}
