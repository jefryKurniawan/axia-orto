<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            [
                'uuid' => Str::uuid(),
                'medical_record_number' => 'MRN001',
                'nik' => '1234567890123456',
                'name' => 'Budi Santoso',
                'date_of_birth' => '1980-05-15',
                'gender' => 'L',
                'phone' => '081234567895',
                'address' => 'Jl. Merdeka No. 123, Jakarta',
                'emergency_contact' => '081234567896',
                'insurance_type' => 'bpjs',
                'blood_type' => 'A',
                'allergies' => 'Tidak ada',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'medical_record_number' => 'MRN002',
                'nik' => '1234567890123457',
                'name' => 'Siti Rahayu',
                'date_of_birth' => '1992-08-20',
                'gender' => 'P',
                'phone' => '081234567897',
                'address' => 'Jl. Sudirman No. 456, Jakarta',
                'emergency_contact' => '081234567898',
                'insurance_type' => 'mandiri',
                'blood_type' => 'B',
                'allergies' => 'Debu, Udang',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('patients')->insert($patients);
    }
}
