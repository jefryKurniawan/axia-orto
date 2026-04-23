<?php

// service Edit yang kepake

namespace App\Livewire\Services;

use App\Models\Service;
use Livewire\Component;

class ServicesEdit extends Component
{
    public Service $service;
    public $name;
    public $code;
    public $description;
    public $service_type;
    public $price;
    public $duration_days;
    public $is_active;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:services,code,' . $this->service->id,
            'service_type' => 'required|in:konsultasi,ortosis,protesis,terapi,alat',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function mount(Service $service)
    {
        $this->service = $service;
        $this->name = $service->name;
        $this->code = $service->code;
        $this->description = $service->description;
        $this->service_type = $service->service_type;
        $this->price = $service->price;
        $this->duration_days = $service->duration_days;
        $this->is_active = $service->is_active;
    }

    public function save()
    {
        $this->validate();

        $this->service->update([
            'name' => $this->name,
            'code' => $this->code,
            'service_type' => $this->service_type,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Layanan berhasil diperbarui.');
        return redirect()->route('services.index');
    }

    public function render()
    {
        return view('livewire.services.services-edit');
    }
}
