<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Prodi;
use App\Models\DetailUser;
use App\Models\DetailDosen;
use App\Models\DetailMahasiswa;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Step 1: Buat Role menggunakan Spatie
        $roles = ['superAdmin', 'Dosen', 'Mahasiswa'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Step 2: Data user khusus (manual)
        $users = [
            [
                'email' => 'superadmin@gmail.com',
                'password' => '1111111',
                'nim' => '1111111',
                'nip' => null,
                'role' => 'superAdmin',
            ],
            [
                'email' => 'dosen@gmail.com',
                'password' => '1234567',
                'nim' => null,
                'nip' => '19700000000000',
                'role' => 'Dosen',
            ],
            [
                'email' => 'mahasiswa@gmail.com',
                'password' => '7654321',
                'nim' => '7654321',
                'nip' => null,
                'role' => 'Mahasiswa',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'nim' => $data['nim'],
                    'nip' => $data['nip'],
                    'password' => Hash::make($data['password']),
                ]
            );

            if (!$user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
        }

        $prodis = [
            ['kode' => '03', 'nama' => 'Informatika', 'lama_studi' => 3, 'prefix' => 'TI1'],
            ['kode' => '04', 'nama' => 'Rekayasa Perangkat Lunak', 'lama_studi' => 4, 'prefix' => 'RPL1'],
        ];

        $tahunMasukFull = '2023';
        $tahunMasukKode = substr($tahunMasukFull, -2); // '23'
        $jumlahMahasiswaPerProdi = 90; // 30 mahasiswa per kelas * 3 kelas (A–C)

        foreach ($prodis as $prodi) {
            $prodiModel = Prodi::firstOrCreate(
                ['kode_prodi' => $prodi['kode']],
                ['nama' => $prodi['nama'], 'lama_studi' => $prodi['lama_studi']]
            );

            for ($i = 1; $i <= $jumlahMahasiswaPerProdi; $i++) {
                $nim = $tahunMasukKode . $prodi['kode'] . str_pad($i, 3, '0', STR_PAD_LEFT);
                $email = "mhs_{$prodi['kode']}{$i}@mail.com";

                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'nim' => $nim,
                        'nip' => null,
                        'password' => Hash::make($nim),
                    ]
                );

                $user->assignRole('Mahasiswa');

                $kelasHuruf = ['A', 'B', 'C'][intval(($i - 1) / 30)]; // 1–30 → A, 31–60 → B, 61–90 → C
                $kelas = $prodi['prefix'] . $kelasHuruf;

                DetailMahasiswa::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'first_name' => "Mhs{$i}",
                        'last_name' => $prodi['nama'],
                        'tahun_masuk' => $tahunMasukFull,
                        'jenis_kelamin' => rand(0, 1) ? 'Laki-laki' : 'Perempuan',
                        'prodi_id' => $prodiModel->id,
                        'kelas' => $kelas,
                    ]
                );
            }
        }

        // Seeder dosen (tetap)
        for ($i = 1; $i <= 10; $i++) {
            $nip = '1970' . str_pad($i, 10, '0', STR_PAD_LEFT);
            $email = "dosen{$i}@mail.com";

            $user = User::firstOrCreate(
                ['email' => $email],
                ['nip' => $nip, 'password' => Hash::make($nip)]
            );

            $user->assignRole('Dosen');

            DetailDosen::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => "Dosen{$i}",
                    'last_name' => "Ilmu",
                    'jenis_kelamin' => rand(0, 1) ? 'Laki-laki' : 'Perempuan',
                    'jabatan_fungsional' => ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'][rand(0, 3)],
                    'bidang_keahlian' => ['AI', 'Web', 'DB', 'Jaringan'][rand(0, 3)],
                ]
            );
        }
    }

}
