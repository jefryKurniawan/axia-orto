<?php

namespace App\Livewire\TreatmentOrders;

use App\Models\TreatmentOrder;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Consultation;
use App\Models\OrderItem;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class TreatmentOrderEdit extends Component
{
    public $order;
    public $patient_search = '';
    public $patient_id;
    public $consultation_id;
    public $order_date;
    public $delivery_date;
    public $notes;
    public $status;

    public $items = []; 
    public $total_amount = 0;

    protected $rules = [
        'patient_id' => 'required|exists:patients,id',
        'order_date' => 'required|date',
        'delivery_date' => 'nullable|date|after_or_equal:order_date',
        'items' => 'required|array|min:1',
        'items.*.service_id' => 'required|exists:services,id',
        'items.*.quantity' => 'required|integer|min:1',
    ];

    public function mount(TreatmentOrder $order)
    {
        $order->load(['patient', 'orderItems.service']);
        
        if (!$order->patient) {
            session()->flash('error', 'Data pasien tidak ditemukan.');
            return redirect()->route('treatment-orders.index');
        }

        $this->order = $order;
        $this->patient_id = $order->patient_id;
        $this->patient_search = $order->patient->name . ' (' . $order->patient->medical_record_number . ')';
        $this->consultation_id = $order->consultation_id;
        $this->order_date = $order->order_date ? $order->order_date->format('Y-m-d') : now()->format('Y-m-d');
        $this->delivery_date = $order->delivery_date ? $order->delivery_date->format('Y-m-d') : null;
        $this->notes = $order->notes;
        $this->status = $order->status;
        $this->total_amount = $order->total_amount;

        foreach ($order->orderItems as $item) {
            $this->items[] = [
                'service_id' => $item->service_id,
                'name' => $item->service->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
            ];
        }
    }

    public function addItem($serviceId)
    {
        $service = Service::find($serviceId);
        
        foreach ($this->items as $index => $item) {
            if ($item['service_id'] == $serviceId) {
                $this->items[$index]['quantity']++;
                $this->calculateTotals();
                return;
            }
        }

        $this->items[] = [
            'service_id' => $service->id,
            'name' => $service->name,
            'quantity' => 1,
            'unit_price' => $service->price,
            'total_price' => $service->price,
        ];

        $this->calculateTotals();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->total_amount = 0;
        foreach ($this->items as $index => $item) {
            $this->items[$index]['total_price'] = $item['quantity'] * $item['unit_price'];
            $this->total_amount += $this->items[$index]['total_price'];
        }
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function () {
            $this->order->update([
                'patient_id' => $this->patient_id,
                'consultation_id' => $this->consultation_id,
                'order_date' => $this->order_date,
                'delivery_date' => $this->delivery_date,
                'total_amount' => $this->total_amount,
                'status' => $this->status,
                'notes' => $this->notes,
            ]);

            // Simple way: delete old items and recreate
            $this->order->orderItems()->delete();

            foreach ($this->items as $item) {
                OrderItem::create([
                    'treatment_order_id' => $this->order->id,
                    'service_id' => $item['service_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
            }
        });

        session()->flash('success', 'Pesanan berhasil diperbarui.');
        return redirect()->route('treatment-orders.index');
    }

    public function render()
    {
        $patients = [];
        if (strlen($this->patient_search) > 2 && !$this->patient_id) {
            $patients = Patient::where('name', 'like', '%' . $this->patient_search . '%')
                ->orWhere('medical_record_number', 'like', '%' . $this->patient_search . '%')
                ->take(5)
                ->get();
        }

        $services = Service::where('is_active', true)->get();

        return view('livewire.treatment-orders.treatment-order-edit', [
            'patients' => $patients,
            'services' => $services,
        ]);
    }
}
