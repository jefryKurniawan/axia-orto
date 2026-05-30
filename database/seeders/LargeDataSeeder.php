<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Generates 10,000 patients for testing export performance.
 * Usage: php artisan db:seed --class=LargeDataSeeder
 */
class LargeDataSeeder extends Seeder
{
    public function run(): void
    {
        $batchSize = 500;
        $total = 10000;

        $this->command->info("Generating {$total} patients...");

        $firstNames = ['Ahmad', 'Siti', 'Budi', 'Dewi', 'Eko', 'Fitri', 'Guntur', 'Hana', 'Iwan', 'Joko', 'Kartika', 'Lukman', 'Maya', 'Nina', 'Omar', 'Putri', 'Rizki', 'Sari', 'Tono', 'Umi'];
        $lastNames = ['Santoso', 'Rahayu', 'Saputra', 'Lestari', 'Kusuma', 'Wijaya', 'Permata', 'Hidayat', 'Nugroho', 'Pratama', 'Setiawan', 'Wibowo', 'Handoko', 'Susanto', 'Kurniawan'];
        $cities = ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Malang', 'Medan', 'Makassar', 'Denpasar', 'Palembang'];
        $insuranceTypes = ['bpjs', 'mandiri', 'asuransi'];
        $bloodTypes = ['A', 'B', 'AB', 'O'];

        for ($offset = 0; $offset < $total; $offset += $batchSize) {
            $batch = [];
            $count = min($batchSize, $total - $offset);

            for ($i = 0; $i < $count; $i++) {
                $idx = $offset + $i + 1;
                $batch[] = [
                    'uuid' => (string) Str::uuid(),
                    'medical_record_number' => 'MRN-' . str_pad($idx, 6, '0', STR_PAD_LEFT),
                    'nik' => '3273' . str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT),
                    'name' => $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)],
                    'date_of_birth' => Carbon::now()->subYears(rand(5, 80))->subMonths(rand(0, 11))->subDays(rand(0, 28))->format('Y-m-d'),
                    'gender' => rand(0, 1) ? 'L' : 'P',
                    'phone' => '081' . rand(10000000, 99999999) . str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT),
                    'address' => 'Jl. ' . $cities[array_rand($cities)] . ' No. ' . rand(1, 200) . ', ' . $cities[array_rand($cities)],
                    'insurance_type' => $insuranceTypes[array_rand($insuranceTypes)],
                    'blood_type' => $bloodTypes[array_rand($bloodTypes)],
                    'allergies' => rand(0, 3) === 0 ? 'Debu, Lateks' : null,
                    'created_at' => Carbon::now()->subDays(rand(0, 365)),
                    'updated_at' => now(),
                ];
            }

            DB::table('patients')->insert($batch);
            $this->command->info("  Inserted " . ($offset + $count) . " / {$total}");
        }

        $this->command->info("Done. Total patients: " . DB::table('patients')->count());
    }
}
