<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'uuid' => Str::uuid(),
                'code' => 'KONS-001',
                'name' => 'Konsultasi Awal',
                'description' => 'Konsultasi medis awal dengan dokter spesialis',
                'service_type' => 'konsultasi',
                'price' => 150000.00,
                'duration_days' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'code' => 'ORT-001',
                'name' => 'Orthosis Kaki',
                'description' => 'Pembuatan dan pemasangan orthosis untuk kaki',
                'service_type' => 'ortosis',
                'price' => 2500000.00,
                'duration_days' => 14,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('services')->insert($services);
    }
}
