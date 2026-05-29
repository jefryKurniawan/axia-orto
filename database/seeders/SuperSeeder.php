<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Consultation;
use App\Models\TreatmentOrder;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\InventoryItem;
use App\Models\ProductionTracking;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuperSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Doctors & Staff
        $doctors = [];
        $doctorNames = ['dr. Andi Pratama', 'dr. Siti Aminah', 'dr. Budi Kusuma', 'dr. Maria Ulfa'];
        foreach ($doctorNames as $name) {
            $doctors[] = User::updateOrCreate(
                ['email' => strtolower(str_replace(['.', ' '], ['', ''], $name)) . '@axiaorto.com'],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $name,
                    'password' => bcrypt('password'),
                    'role' => 'dokter'
                ]
            );
        }

        // 2. Create Services (Katalog)
        $services = [
            ['code' => 'AFO-01', 'name' => 'Ankle Foot Orthosis (Standard)', 'service_type' => 'ortosis', 'price' => 2500000],
            ['code' => 'KAFO-01', 'name' => 'Knee Ankle Foot Orthosis', 'service_type' => 'ortosis', 'price' => 5000000],
            ['code' => 'PRO-LEG-01', 'name' => 'Protesis Kaki Bawah Lutut', 'service_type' => 'protesis', 'price' => 12000000],
            ['code' => 'CONS-01', 'name' => 'Konsultasi & Assessment', 'service_type' => 'konsultasi', 'price' => 150000],
            ['code' => 'REPAIR-01', 'name' => 'Perbaikan Alat', 'service_type' => 'alat', 'price' => 500000],
        ];
        foreach ($services as $svc) {
            Service::updateOrCreate(['code' => $svc['code']], array_merge($svc, ['uuid' => (string) Str::uuid()]));
        }
        $allServices = Service::all();

        // 3. Create Patients
        $patients = [];
        for ($i = 1; $i <= 20; $i++) {
            $patients[] = Patient::create([
                'uuid' => (string) Str::uuid(),
                'medical_record_number' => 'MRN-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nik' => '32730' . rand(1000000000, 9999999999),
                'name' => $this->getPatientName(),
                'date_of_birth' => Carbon::now()->subYears(rand(5, 70))->subMonths(rand(1, 12)),
                'gender' => rand(0, 1) ? 'L' : 'P',
                'phone' => '0812' . rand(10000000, 99999999),
                'address' => 'Jl. Kebahagiaan No. ' . rand(1, 100) . ', Jakarta',
                'insurance_type' => rand(0, 1) ? 'bpjs' : 'mandiri',
                'blood_type' => ['A', 'B', 'AB', 'O'][rand(0, 3)],
                'allergies' => rand(0, 1) ? 'Debu, Latex' : 'Tidak ada',
            ]);
        }

        // 4. Create Flow: Consultation -> Order -> Production -> Payment
        foreach ($patients as $index => $patient) {
            $consult = Consultation::create([
                'uuid' => (string) Str::uuid(),
                'patient_id' => $patient->id,
                'doctor_id' => $doctors[array_rand($doctors)]->id,
                'consultation_date' => Carbon::now()->subDays(rand(1, 60)),
                'complaint' => 'Kesulitan berjalan, membutuhkan alat bantu ' . ($index % 2 == 0 ? 'AFO' : 'Protesis'),
                'diagnosis' => 'Gait abnormality / Limb loss',
                'treatment_plan' => 'Pembuatan alat bantu kustom',
                'status' => 'completed',
            ]);

            // Add Measurements (MVP #2)
            $measurementTypes = ['Limb Length', 'Circumference (Proximal)', 'Circumference (Distal)', 'Joint Angle'];
            foreach ($measurementTypes as $mType) {
                DB::table('patient_measurements')->insert([
                    'patient_id' => $patient->id,
                    'consultation_id' => $consult->id,
                    'measurement_type' => $mType,
                    'value' => rand(10, 100),
                    'unit' => 'cm',
                    'measured_at' => $consult->consultation_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (rand(0, 1)) {
                $order = TreatmentOrder::create([
                    'uuid' => (string) Str::uuid(),
                    'patient_id' => $patient->id,
                    'consultation_id' => $consult->id,
                    'order_number' => 'ORD-' . Carbon::now()->format('Ym') . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'order_date' => $consult->consultation_date->format('Y-m-d'),
                    'total_amount' => 0,
                    'status' => ['pending', 'in_progress', 'completed'][rand(0, 2)],
                    'created_by' => $doctors[0]->id,
                ]);

                $svc = $allServices->random();
                DB::table('order_items')->insert([
                    'treatment_order_id' => $order->id,
                    'service_id' => $svc->id,
                    'quantity' => 1,
                    'unit_price' => $svc->price,
                    'total_price' => $svc->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $order->update(['total_amount' => $svc->price]);

                DB::table('production_trackings')->insert([
                    'uuid' => (string) Str::uuid(),
                    'treatment_order_id' => $order->id,
                    'step' => 'assembly',
                    'status' => 'in_progress',
                    'notes' => 'Sedang dalam pengerjaan teknisi.',
                    'assigned_to' => $doctors[0]->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (rand(0, 1)) {
                    DB::table('payments')->insert([
                        'uuid' => (string) Str::uuid(),
                        'treatment_order_id' => $order->id,
                        'payment_number' => 'INV-' . Carbon::now()->format('Ym') . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                        'payment_date' => Carbon::parse($order->order_date)->addDays(2),
                        'amount' => $order->total_amount,
                        'method' => ['cash', 'transfer'][rand(0, 1)],
                        'status' => 'paid',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 5. Inventory Items
        $materials = [
            ['Polypropylene Sheet', 'bahan_baku', 50, 'pcs'],
            ['Strapping Velcro', 'bahan_baku', 100, 'meter'],
            ['EVA Foam 3mm', 'bahan_baku', 20, 'sheet'],
            ['Resin Protesis', 'bahan_baku', 15, 'kg'],
            ['Kaki Palsu SACH Foot', 'komponen', 10, 'pcs'],
        ];
        foreach ($materials as $m) {
            DB::table('inventory_items')->insert([
                'uuid' => (string) Str::uuid(),
                'code' => strtoupper(substr($m[0], 0, 3)) . rand(100, 999),
                'name' => $m[0],
                'category' => $m[1],
                'quantity' => $m[2],
                'reorder_level' => 5,
                'unit' => $m[3],
                'price' => rand(50000, 500000),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Bump cache versions so fresh data is served
        $modules = ['patients', 'consultations', 'services', 'dashboard'];
        foreach ($modules as $module) {
            DB::table('cache_versions')->updateOrInsert(
                ['module_name' => $module],
                ['version' => DB::raw('version + 1')]
            );
        }
    }

    private function getPatientName() {
        $first = ['Ahmad', 'Siti', 'Budi', 'Dewi', 'Eko', 'Fitri', 'Guntur', 'Hana', 'Iwan', 'Joko'];
        $last = ['Santoso', 'Rahayu', 'Saputra', 'Lestari', 'Kusuma', 'Wijaya', 'Permata', 'Hidayat'];
        return $first[array_rand($first)] . ' ' . $last[array_rand($last)];
    }
}
