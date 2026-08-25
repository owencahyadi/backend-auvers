<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Super Admin (Bisa akses semua toko & menu Payroll)
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@resto.com',
            'password' => Hash::make('rahasia123'), 
            'role' => 'admin',
            'store_id' => null // Admin tidak terikat ke 1 toko khusus
        ]);

        // 2. Manager 1 (Hanya bisa akses Toko 1)
        User::create([
            'name' => 'Manager Store 1',
            'email' => 'manager1@gmail.com',
            'password' => Hash::make('manager123'), 
            'role' => 'manager',
            'store_id' => 1 // ID Toko 1
        ]);

        // 3. Manager 2 (Hanya bisa akses Toko 2)
        User::create([
            'name' => 'Manager Store 2',
            'email' => 'manager2@gmail.com',
            'password' => Hash::make('manager123'), 
            'role' => 'manager',
            'store_id' => 2 // ID Toko 2
        ]);
    }
}