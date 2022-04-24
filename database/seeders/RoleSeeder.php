<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'role_name' => "Pimpinan"
        ]);
        Role::create([
            'role_name' => "Admin Gudang"
        ]);
        Role::create([
            'role_name' => "Admin Penjualan"
        ]);
    }
}
