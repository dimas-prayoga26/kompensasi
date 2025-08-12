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
        $roles = ['superAdmin', 'Dosen', 'Mahasiswa'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $users = [
            [
                'email' => 'superadmin@gmail.com',
                'password' => '1111111',
                'nim' => '1111111',
                'nip' => '1111111',
                'role' => 'superAdmin',
            ],
            [
                'email' => 'dosen@gmail.com',
                'password' => '19700000000000',
                'nim' => null,
                'nip' => '19700000000000',
                'role' => 'Dosen',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'nim' => $data['nim'],
                    'nip' => $data['nip'],
                    'password' => bcrypt($data['password']),

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

        $dosenData = [
            ['nama' => 'Eka Ismantohadi, S.Kom., M.Eng', 'nip' => '198107092021211005'],
            ['nama' => 'Iryanto, S.Si., M.Si', 'nip' => '199008012019031014'],
            ['nama' => 'Willy Permana Putra, S.T., M.Eng', 'nip' => '198610042019031004'],
            ['nama' => 'A. Lubis Ghozali, S.Kom., M.Kom', 'nip' => '198605102019031011'],
            ['nama' => 'A. Sumarudin, S.Pd., M.T., M.Sc', 'nip' => null],
            ['nama' => 'Ir. Adi Suheryadi, S.ST., M.Kom', 'nip' => '199003222019031007'],
            ['nama' => 'Muhamad Mustamiin, S.Pd.,M.Kom', 'nip' => '199205052019031011'],
            ['nama' => 'Alifia Puspaningrum, S.Pd., M.Kom', 'nip' => '199305282019032024'],
            ['nama' => 'Fachrul Pralienka Bani Muhamad, S.ST., M.Kom', 'nip' => '199204232018031001'],
            ['nama' => 'Kurnia Adi Cahyanto, S.T., M.Kom', 'nip' => '198503022018031001'],
            ['nama' => 'Dr. Ir. Mohammad Yani, S.T., M.T., M.Sc', 'nip' => '198003072021211006'],
            ['nama' => 'Esti Mulyani, S.Kom., M.Kom', 'nip' => '199003162018032001'],
            ['nama' => 'Munengsih Sari Bunga, S.Kom., M.Eng', 'nip' => '198507202019032015'],
            ['nama' => 'Moh. Ali Fikri, S.Kom., M.Kom', 'nip' => '198901182022031002'],
            ['nama' => 'Darsih, S.Kom., M.Kom', 'nip' => '198109062021212004'],
            ['nama' => 'Muhammad Anis Al Hilmi, S.Si., M.T.', 'nip' => '199002282019031012'],
            ['nama' => 'Nur Budi Nugraha, S.Kom., MT', 'nip' => '198711162022031001'],
            ['nama' => 'Robieth Sohiburoyyan, S.Si., M.Si', 'nip' => '199005172022031003'],
            ['nama' => 'Rendi, S,.Kom., M.Kom', 'nip' => '199212132022031007'],
            ['nama' => 'Yaqutina Marjani Santosa, S.Pd., M.Cs', 'nip' => '199211022022032014'],
            ['nama' => 'Robi Robiyanto, S.Kom., M.TI', 'nip' => '198707222022031001'],
            ['nama' => 'Salamet Nur Himawan, S.Si., M.Si', 'nip' => '199407022022031005'],
            ['nama' => 'Fauzan Ishlakhuddin, S.Kom., M.Cs', 'nip' => '199105222022031003'],
            ['nama' => 'Dian Pramadhana, S.Kom., M.Kom', 'nip' => '199302282022031007'],
            ['nama' => 'Riyan Farismana, S.Kom., M.Kom', 'nip' => '198905112022031005'],
            ['nama' => 'Dita Rizki Amalia, S.Pd., M.Kom', 'nip' => '198803022022032005'],
            ['nama' => 'Dr. Raswa, M.Pd', 'nip' => null],
            ['nama' => 'Sonty Lena, S.Kom., M.M., M.Kom', 'nip' => '198703182019032014'],
            ['nama' => 'Renol Burjulius, S.T., M.Kom', 'nip' => '198407092019031003'],
            ['nama' => 'Joko Irawan, S.Kom., M.Kom', 'nip' => '199107282024061001'],
            ['nama' => 'Muhammad Edi Iswanto, M.Kom', 'nip' => '199401302024061002'],
            ['nama' => 'Vera Wati, M.Kom.', 'nip' => '199409032024062002'],
        ];

        foreach ($dosenData as $dosen) {
            $nameParts = explode(', ', $dosen['nama']);
            
            $firstName = explode(' ', $nameParts[0])[0];
            
            $lastName = implode(' ', array_slice(explode(' ', $nameParts[0]), 1)) . ', ' . $nameParts[1] ?? '';

            $email = strtolower($firstName . '_' . explode(' ', $lastName)[0]) . '@polindra.co.id'; 

           $user = User::create([
                'email' => $email,
                'nip' => $dosen['nip'],
                'password' => bcrypt($dosen['nip']),
            ]);

            $user->assignRole('Dosen'); 

            DetailDosen::create([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'jenis_kelamin' => null,
                'jabatan_fungsional' => null,
                'bidang_keahlian' => null,
            ]);
        }
    }

}
