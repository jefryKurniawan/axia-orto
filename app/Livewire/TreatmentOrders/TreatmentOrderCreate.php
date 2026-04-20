<?php

namespace App\Livewire\TreatmentOrders;

use App\Models\TreatmentOrder;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Consultation;
use App\Models\OrderItem;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class TreatmentOrderCreate extends Component
{
    public $patient_search = '';
    public $patient_id;
    public $consultation_id;
    public $order_date;
    public $delivery_date;
    public $notes;
    public $status = 'pending';

    public $items = []; // Array of {service_id, quantity, unit_price, total_price}
    public $total_amount = 0;

    protected $rules = [
        'patient_id' => 'required|exists:patients,id',
        'order_date' => 'required|date',
        'delivery_date' => 'nullable|date|after_or_equal:order_date',
        'items' => 'required|array|min:1',
        'items.*.service_id' => 'required|exists:services,id',
        'items.*.quantity' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $this->order_date = now()->format('Y-m-d');
        $this->delivery_date = now()->addDays(7)->format('Y-m-d');
    }

    public function selectPatient($id)
    {
        $patient = Patient::find($id);
        $this->patient_id = $id;
        $this->patient_search = $patient->name . ' (' . $patient->medical_record_number . ')';
        
        // Find latest consultation for this patient
        $consultation = Consultation::where('patient_id', $id)->latest()->first();
        if ($consultation) {
            $this->consultation_id = $consultation->id;
        }
    }

    public function addItem($serviceId)
    {
        $service = Service::find($serviceId);
        
        // Check if item already exists
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

    public function updateQuantity($index, $quantity)
    {
        if ($quantity < 1) $quantity = 1;
        $this->items[$index]['quantity'] = $quantity;
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

    public function store()
    {
        $this->validate();

        DB::transaction(function () {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(TreatmentOrder::count() + 1, 4, '0', STR_PAD_LEFT);

            $order = TreatmentOrder::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'order_number' => $orderNumber,
                'patient_id' => $this->patient_id,
                'consultation_id' => $this->consultation_id,
                'order_date' => $this->order_date,
                'delivery_date' => $this->delivery_date,
                'total_amount' => $this->total_amount,
                'status' => $this->status,
                'notes' => $this->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($this->items as $item) {
                OrderItem::create([
                    'treatment_order_id' => $order->id,
                    'service_id' => $item['service_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
            }
        });

        session()->flash('success', 'Pesanan berhasil dibuat.');
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

        return view('livewire.treatment-orders.treatment-order-create', [
            'patients' => $patients,
            'services' => $services,
        ]);
    }

    public function updatedPatientSearch()
    {
        $this->patient_id = null;
    }
}
