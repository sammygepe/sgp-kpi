<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KpiSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah sudah ada baris
        if (DB::table('kpi_settings')->count() > 0) {
            return; // Sudah ada, tidak perlu insert lagi
        }

        DB::table('kpi_settings')->insert([
            'grace_period_days' => 30,
            'grace_period_december_days' => 7,
            'max_time_factor_floor' => 0.10,
            'auto_close_enabled' => true,
            'updated_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
