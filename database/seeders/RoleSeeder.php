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

        // Buat beberapa user guru
        $gurus = [
            [
                'name' => 'Guru 1',
                'email' => 'guru1@example.com',
                'password' => Hash::make('password')
            ],
            [
                'name' => 'Guru 2',
                'email' => 'guru2@example.com',
                'password' => Hash::make('password')
            ],
            [
                'name' => 'Guru 3',
                'email' => 'guru3@example.com',
                'password' => Hash::make('password')
            ]
        ];

        foreach ($gurus as $guru) {
            $user = User::create($guru);
            $user->assignRole($tutor);
        }
    }
}

