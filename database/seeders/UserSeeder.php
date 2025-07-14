<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Prodi;
use App\Models\DetailUser;
use App\Models\DetailDosen;
use Illuminate\Support\Str;
use App\Models\DetailMahasiswa;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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

        // // Data Prodi dan Mahasiswa
        // $prodis = [
        //     ['kode' => '03', 'nama' => 'Informatika', 'lama_studi' => 6, 'prefix' => 'TI'],
        //     ['kode' => '05', 'nama' => 'Rekayasa Perangkat Lunak', 'lama_studi' => 8, 'prefix' => 'RPL'],
        // ];

        // $tahunSekarang = (int) date('Y');
        // $kelasList = Kelas::all();

        // foreach ($prodis as $prodi) {
        //     $prodiModel = Prodi::firstOrCreate(
        //         ['kode_prodi' => $prodi['kode']],
        //         ['nama' => $prodi['nama'], 'lama_studi' => $prodi['lama_studi']]
        //     );

        //     $kelasProdi = $kelasList->filter(function ($kelas) use ($prodi) {
        //         return Str::startsWith($kelas->nama, $prodi['prefix']);
        //     })->values();

        //     if ($kelasProdi->isEmpty()) {
        //         $this->command->warn("Tidak ada kelas ditemukan untuk prodi {$prodi['nama']} dengan prefix {$prodi['prefix']}");
        //         continue;
        //     }

        //     foreach ($kelasProdi as $kIndex => $kelas) {
        //         if (!preg_match('/^(TI|RPL)(\d)[A-Z]$/', $kelas->nama, $matches)) {
        //             $this->command->warn("Format kelas salah: {$kelas->nama}");
        //             continue;
        //         }

        //         $tingkat = (int) $matches[2];
        //         $tahunMasukFull = $tahunSekarang - ($tingkat - 1);
        //         $tahunMasukKode = substr($tahunMasukFull, -2);

        //         for ($j = 1; $j <= 30; $j++) {
        //             $i = ($kIndex * 30) + $j;

        //             $nim = $tahunMasukKode . $prodi['kode'] . str_pad($i, 3, '0', STR_PAD_LEFT);
        //             $email = "mhs_{$prodi['kode']}{$i}@mail.com";

        //             $user = User::firstOrCreate(
        //                 ['email' => $email],
        //                 [
        //                     'nim' => $nim,
        //                     'nip' => null,
        //                     'password' => Hash::make($nim),
        //                 ]
        //             );

        //             $user->assignRole('Mahasiswa');

        //             DetailMahasiswa::firstOrCreate(
        //                 ['user_id' => $user->id],
        //                 [
        //                     'first_name' => "Mhs{$i}",
        //                     'last_name' => $prodi['nama'],
        //                     'tahun_masuk' => $tahunMasukFull,
        //                     'jenis_kelamin' => rand(0, 1) ? 'Laki-laki' : 'Perempuan',
        //                     'prodi_id' => $prodiModel->id,
        //                     'kelas' => $kelas->nama,
        //                 ]
        //             );
        //         }
        //     }
        // }

        // Seeder dosen
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
