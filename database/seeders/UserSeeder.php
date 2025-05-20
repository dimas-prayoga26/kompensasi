<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Buat roles
        $roles = ['superAdmin', 'Dosen', 'Mahasiswa'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Step 2: Buat user dan assign role
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => 'password', // Nanti di-hash
                'role' => 'superAdmin'
            ],
            [
                'name' => 'Dosen Satu',
                'email' => 'dosen@gmail.com',
                'password' => 'password',
                'role' => 'Dosen'
            ],
            [
                'name' => 'Mahasiswa Satu',
                'email' => 'mahasiswa@gmail.com',
                'password' => 'password',
                'role' => 'Mahasiswa'
            ],
        ];

        foreach ($users as $data) {
            // Cek dulu apakah user sudah ada berdasarkan email
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                ]
            );

            // Assign role
            if (!$user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
        }
    }
}
