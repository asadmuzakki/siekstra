<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Role
        $admin = Role::create(['name' => 'admin']);
        $waliMurid = Role::create(['name' => 'wali_murid']);
        $tutor = Role::create(['name' => 'tutor']);

        // Buat User dan Assign Role
        $user1 = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password')
        ]);
        $user1->assignRole($admin);

        $user2 = User::create([
            'name' => 'Guru',
            'email' => 'guru@example.com',
            'password' => Hash::make('password')
        ]);
        $user2->assignRole($tutor);
    }
}

