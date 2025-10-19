<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConsultationSeeder extends Seeder
{
    public function run(): void
    {
        $consultations = [
            [
                'uuid' => Str::uuid(),
                'patient_id' => 1, // Budi Santoso
                'doctor_id' => 2,  // Dr. John Doe
                'consultation_date' => '2024-01-15 09:00:00',
                'complaint' => 'Nyeri pada kaki sebelah kiri setelah kecelakaan',
                'diagnosis' => 'Fraktur tibia dengan kebutuhan orthosis',
                'treatment_plan' => 'Pembuatan orthosis kaki kiri, terapi fisik 2x seminggu',
                'notes' => 'Pasien memerlukan alat bantu jalan sementara',
                'follow_up_date' => '2024-02-15',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'patient_id' => 2, // Siti Rahayu
                'doctor_id' => 3,  // Dr. Jane Smith
                'consultation_date' => '2024-01-16 10:30:00',
                'complaint' => 'Kehilangan tangan kanan akibat kecelakaan kerja',
                'diagnosis' => 'Amputasi transradial kanan',
                'treatment_plan' => 'Pembuatan prostesis lengan bawah kanan',
                'notes' => 'Pasien right-handed, perlu prostesis fungsional',
                'follow_up_date' => '2024-02-20',
                'status' => 'in_progress',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('consultations')->insert($consultations);
    }
}
