<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Atasan (Rose)
        $rose = User::create([
            'name' => 'Rose Atasan',
            'email' => 'rose@kpi.com',
            'password' => Hash::make('123'),
            'role' => 'atasan',
            'jabatan' => 'Manager Operasional',
            'nik' => 'AT001',
        ]);

        // 2. Staff 1 (Upin)
        $upin = User::create([
            'name' => 'Upin Staff',
            'email' => 'upin@kpi.com',
            'password' => Hash::make('123'),
            'role' => 'staff',
            'jabatan' => 'Staff Administrasi',
            'nik' => 'ST001',
            'supervisor_id' => $rose->id,
        ]);

        // 3. Staff 2 (Ipin)
        $ipin = User::create([
            'name' => 'Ipin Staff',
            'email' => 'ipin@kpi.com',
            'password' => Hash::make('123'),
            'role' => 'staff',
            'jabatan' => 'Staff Keuangan',
            'nik' => 'ST002',
            'supervisor_id' => $rose->id,
        ]);
    }
}
