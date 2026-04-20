<?php

namespace App\Livewire\Services;

use App\Models\Service;
use Livewire\Component;

class ServiceCreate extends Component
{
    public $name;
    public $code;
    public $description;
    public $service_type = 'konsultasi';
    public $price = 0;
    public $duration_days = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|unique:services,code',
        'service_type' => 'required|in:konsultasi,ortosis,protesis,terapi,alat',
        'price' => 'required|numeric|min:0',
        'duration_days' => 'required|integer|min:0',
        'description' => 'nullable|string',
    ];

    public function mount()
    {
        $this->generateCode();
    }

    public function updatedServiceType()
    {
        $this->generateCode();
    }

    public function generateCode()
    {
        $prefix = match ($this->service_type) {
            'konsultasi' => 'KONS',
            'ortosis' => 'ORT',
            'protesis' => 'PROT',
            'terapi' => 'TER',
            'alat' => 'ALT',
            default => 'SRV',
        };

        $count = Service::where('service_type', $this->service_type)->count() + 1;
        $this->code = "{$prefix}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function save()
    {
        $this->validate();

        Service::create([
            'name' => $this->name,
            'code' => $this->code,
            'service_type' => $this->service_type,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'description' => $this->description,
            'is_active' => true,
        ]);

        session()->flash('success', 'Layanan berhasil ditambahkan.');
        return redirect()->route('services.index');
    }

    public function render()
    {
        return view('livewire.services.service-create');
    }
}
