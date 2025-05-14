<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Kriteria;
use App\Models\Nasabah;
use App\Models\Penilaian;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(RolesTableSeeder::class);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'role_id' => 1
        ]);

        Kriteria::create([
            'kode' => 'C1',
            'nama' => 'Lama Usaha',
            'bobot' => 0.30,
            'keterangan' => [
                '5' => '> 5 Tahun (5)',
                '4' => '5 Tahun (4)',
                '3' => '< 5 Tahun (3)'
            ]
        ]);

        Kriteria::create([
            'kode' => 'C2',
            'nama' => 'Penghasilan',
            'bobot' => 0.25,
            'keterangan' => [
                '5' => '> 10 Juta (5)',
                '4' => '5-10 Juta (4)',
                '3' => '2.5-4.9 Juta (3)',
                '2' => '1-2.4 Juta (2)',
                '1' => '< 1 Juta (1)'
            ]
        ]);

        Kriteria::create([
            'kode' => 'C3',
            'nama' => 'Jaminan',
            'bobot' => 0.15,
            'keterangan' => [
                '5' => 'Sertifikat Bangunan (5)',
                '4' => 'Tanah (4)',
                '3' => 'Perhiasan (3)',
                '2' => 'Kendaraan (2)',
                '1' => 'Elektronik (1)'
            ]
        ]);

        Kriteria::create([
            'kode' => 'C4',
            'nama' => 'Usaha Tambahan',
            'bobot' => 0.20,
            'keterangan' => [
                '5' => 'Ada (5)',
                '3' => 'Tidak (3)'
            ]
        ]);

        Kriteria::create([
            'kode' => 'C5',
            'nama' => 'BI Checking',
            'bobot' => 0.10,
            'keterangan' => [
                '5' => 'Baik (5)',
                '3' => 'Buruk (3)'
            ]
        ]);
    }
}
