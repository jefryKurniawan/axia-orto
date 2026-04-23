<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'uuid' => Str::uuid(),
                'name' => 'Admin Axia',
                'email' => 'admin@axia.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'specialization' => null,
                'phone' => '081234567890',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Dr. John Doe',
                'email' => 'dr.john@axia.com',
                'password' => Hash::make('password123'),
                'role' => 'dokter',
                'specialization' => 'Orthosis & Prosthesis',
                'phone' => '081234567891',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Dr. Jane Smith',
                'email' => 'dr.jane@axia.com',
                'password' => Hash::make('password123'),
                'role' => 'dokter',
                'specialization' => 'Physical Therapy',
                'phone' => '081234567892',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Staf Klinik 1',
                'email' => 'staf1@axia.com',
                'password' => Hash::make('password123'),
                'role' => 'staf_klinik',
                'specialization' => null,
                'phone' => '081234567893',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('users')->insert($users);
    }
}
