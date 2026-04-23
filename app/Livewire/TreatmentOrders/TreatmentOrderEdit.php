<?php

namespace App\Livewire\TreatmentOrders;

use App\Models\TreatmentOrder;
use App\Models\Service;
use Livewire\Component;

class TreatmentOrderEdit extends Component
{
    public TreatmentOrder $order;
    public $patient_search;
    public $order_date;
    public $delivery_date;
    public $status;
    public $notes;
    public $items = [];
    public $services;
    public $total_amount = 0;

    protected $rules = [
        'order_date' => 'required|date',
        'delivery_date' => 'nullable|date|after:order_date',
        'status' => 'required|in:pending,in_progress,completed,cancelled',
        'notes' => 'nullable|string',
    ];

    public function mount(TreatmentOrder $treatment_order)
    {
        $this->order = $treatment_order;
        $this->patient_search = $treatment_order->patient?->name ?? 'Pasien tidak ditemukan';
        $this->order_date = $treatment_order->order_date ? $treatment_order->order_date->format('Y-m-d') : date('Y-m-d');
        $this->delivery_date = $treatment_order->delivery_date ? $treatment_order->delivery_date->format('Y-m-d') : null;
        $this->status = $treatment_order->status ?? 'pending';
        $this->notes = $treatment_order->notes;
        $this->services = Service::where('is_active', true)->get();
        
        $this->items = [];
        foreach ($treatment_order->orderItems as $item) {
            $this->items[] = [
                'service_id' => $item->service_id,
                'name' => $item->service?->name ?? 'Unknown',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
                'specifications' => $item->specifications,
            ];
        }
        $this->calculateTotal();
    }

    public function addItem($serviceId)
    {
        $service = Service::find($serviceId);
        if (!$service) return;

        foreach ($this->items as $item) {
            if ($item['service_id'] == $serviceId) {
                session()->flash('error', 'Layanan sudah ada dalam pesanan.');
                return;
            }
        }

        $this->items[] = [
            'service_id' => $serviceId,
            'name' => $service->name,
            'quantity' => 1,
            'unit_price' => $service->price,
            'total_price' => $service->price,
            'specifications' => null,
        ];
        $this->calculateTotal();
    }

    public function updateQuantity($index, $quantity)
    {
        if ($quantity < 1) return;
        $this->items[$index]['quantity'] = $quantity;
        $this->items[$index]['total_price'] = $this->items[$index]['unit_price'] * $quantity;
        $this->calculateTotal();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total_amount = collect($this->items)->sum('total_price');
    }

    public function update()
{
    $this->validate();

    $this->order->update([
        'order_date' => $this->order_date,
        'delivery_date' => $this->delivery_date,
        'status' => $this->status,
        'notes' => $this->notes,
        'total_amount' => $this->total_amount,
    ]);

    $this->order->orderItems()->delete();
    
    foreach ($this->items as $item) {
        $this->order->orderItems()->create([
            'service_id' => $item['service_id'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'total_price' => $item['total_price'],
            'specifications' => $item['specifications'],
        ]);
    }

    session()->flash('success', 'Pesanan berhasil diperbarui.');
    return redirect()->route('treatment-orders.show', ['treatment_order' => $this->order->id]);
}

    public function render()
    {
        return view('livewire.treatment-orders.treatment-order-edit');
    }
}