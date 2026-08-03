<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin (akun admin khusus)
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@kpi.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'jabatan' => 'Super Administrator',
            'nik' => 'ADM001',
        ]);

        // 2. Atasan (Rose) - tetap atasan biasa
        $rose = User::create([
            'name' => 'Rose Atasan',
            'email' => 'rose@kpi.com',
            'password' => Hash::make('123'),
            'role' => 'atasan', // <-- TETAP ATASAN
            'jabatan' => 'Manager Operasional',
            'nik' => 'AT001',
        ]);

        // 3. Staff 1 (Upin) - supervisor Rose
        $upin = User::create([
            'name' => 'Upin Staff',
            'email' => 'upin@kpi.com',
            'password' => Hash::make('123'),
            'role' => 'staff',
            'jabatan' => 'Staff Administrasi',
            'nik' => 'ST001',
            'supervisor_id' => $rose->id,
        ]);

        // 4. Staff 2 (Ipin) - supervisor Rose
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
