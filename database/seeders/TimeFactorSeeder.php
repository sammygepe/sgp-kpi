<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeFactorSeeder extends Seeder
{
    public function run(): void
    {
        $factors = [
            ['code' => 'BD14+',   'selisih_min' => -9999, 'selisih_max' => -14,  'faktor' => 1.50, 'label' => '2 minggu lebih cepat'],
            ['code' => 'BD7-13',  'selisih_min' => -13,   'selisih_max' => -7,   'faktor' => 1.40, 'label' => '1 minggu lebih cepat'],
            ['code' => 'BD3-6',   'selisih_min' => -6,    'selisih_max' => -3,   'faktor' => 1.30, 'label' => '3-6 hari lebih cepat'],
            ['code' => 'BD2',     'selisih_min' => -2,    'selisih_max' => -2,   'faktor' => 1.20, 'label' => '2 hari lebih cepat'],
            ['code' => 'BD1',     'selisih_min' => -1,    'selisih_max' => -1,   'faktor' => 1.10, 'label' => '1 hari lebih cepat'],
            ['code' => 'D',       'selisih_min' => 0,     'selisih_max' => 0,    'faktor' => 1.00, 'label' => 'Tepat waktu'],
            ['code' => 'AD1',     'selisih_min' => 1,     'selisih_max' => 1,    'faktor' => 0.90, 'label' => 'Telat 1 hari'],
            ['code' => 'AD2',     'selisih_min' => 2,     'selisih_max' => 2,    'faktor' => 0.80, 'label' => 'Telat 2 hari'],
            ['code' => 'AD3',     'selisih_min' => 3,     'selisih_max' => 3,    'faktor' => 0.70, 'label' => 'Telat 3 hari'],
            ['code' => 'AD4-7',   'selisih_min' => 4,     'selisih_max' => 7,    'faktor' => 0.60, 'label' => 'Telat 4-7 hari (1 minggu)'],
            ['code' => 'AD8-14',  'selisih_min' => 8,     'selisih_max' => 14,   'faktor' => 0.50, 'label' => 'Telat 8-14 hari (2 minggu)'],
            ['code' => 'AD15-21', 'selisih_min' => 15,    'selisih_max' => 21,   'faktor' => 0.40, 'label' => 'Telat 15-21 hari (3 minggu)'],
            ['code' => 'AD22-30', 'selisih_min' => 22,    'selisih_max' => 30,   'faktor' => 0.30, 'label' => 'Telat 22-30 hari (1 bulan)'],
            ['code' => 'AD31-60', 'selisih_min' => 31,    'selisih_max' => 60,   'faktor' => 0.20, 'label' => 'Telat 31-60 hari (2 bulan)'],
            ['code' => 'AD61-90', 'selisih_min' => 61,    'selisih_max' => 90,   'faktor' => 0.10, 'label' => 'Telat 61-90 hari (3 bulan)'],
            ['code' => 'AD90+',   'selisih_min' => 91,    'selisih_max' => 9999, 'faktor' => 0.10, 'label' => 'Telat > 90 hari (floor)'],
        ];

        foreach ($factors as $factor) {
            DB::table('time_factors')->updateOrInsert(
                ['code' => $factor['code']],  // cek berdasarkan code
                array_merge($factor, [
                    'is_active' => true,
                    'updated_at' => now(),
                ])
            );
        }
    }
}
