<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MasterDataSeeder extends Seeder
{
    public function run()
    {
        // Matikan foreign key check sementara agar proses truncate aman
        Schema::disableForeignKeyConstraints();

        // Bersihkan data lama agar tidak duplikat saat dijalankan ulang
        User::truncate();
        Store::truncate();

        Schema::enableForeignKeyConstraints();

        // 1. BUAT 3 DATA CABANG TOKO (STORES)
        $store1 = Store::create([
            'name' => 'Store 1 - Sydney',
        ]);

        $store2 = Store::create([
            'name' => 'Store 2 - Sydney',
        ]);

        $store3 = Store::create([
            'name' => 'Store 3 - Sydney',
        ]);

        // 2. BUAT AKUN PENGGUNA (USERS)
        
        // Super Admin (Bisa akses semua toko & menu Payroll)
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@resto.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'admin',
            'store_id' => null // Admin tidak terikat ke 1 toko khusus
        ]);

        // Manager 1 (Hanya terikat ke Store 1)
        User::create([
            'name' => 'Manager Store 1',
            'email' => 'manager1@gmail.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
            'store_id' => $store1->id 
        ]);

        // Manager 2 (Hanya terikat ke Store 2)
        User::create([
            'name' => 'Manager Store 2',
            'email' => 'manager2@gmail.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
            'store_id' => $store2->id 
        ]);

        // Manager 3 (Hanya terikat ke Store 3)
        User::create([
            'name' => 'Manager Store 3',
            'email' => 'manager3@gmail.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
            'store_id' => $store3->id 
        ]);
    }
}