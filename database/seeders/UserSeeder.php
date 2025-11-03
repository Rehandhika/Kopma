<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['nim' => '00000000'],
            [
                'name' => 'Super Administrator',
                'email' => 'admin@sikopma.test',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // Ketua
        $ketua = User::firstOrCreate(
            ['nim' => '11111111'],
            [
                'name' => 'Ketua KOPMA',
                'email' => 'ketua@sikopma.test',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $ketua->assignRole('Ketua');

        // Wakil Ketua
        $wakilKetua = User::firstOrCreate(
            ['nim' => '22222222'],
            [
                'name' => 'Wakil Ketua KOPMA',
                'email' => 'wakil@sikopma.test',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $wakilKetua->assignRole('Wakil Ketua');

        // BPH Members
        $bphMembers = [
            ['nim' => '33333333', 'name' => 'BPH 1', 'email' => 'bph1@sikopma.test'],
            ['nim' => '44444444', 'name' => 'BPH 2', 'email' => 'bph2@sikopma.test'],
            ['nim' => '55555555', 'name' => 'BPH 3', 'email' => 'bph3@sikopma.test'],
        ];

        foreach ($bphMembers as $member) {
            $user = User::firstOrCreate(
                ['nim' => $member['nim']],
                [
                    'name' => $member['name'],
                    'email' => $member['email'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                ]
            );
            $user->assignRole('BPH');
        }

        // Regular Members
        $regularMembers = [
            ['nim' => '66666666', 'name' => 'Anggota 1', 'email' => 'anggota1@sikopma.test'],
            ['nim' => '77777777', 'name' => 'Anggota 2', 'email' => 'anggota2@sikopma.test'],
            ['nim' => '88888888', 'name' => 'Anggota 3', 'email' => 'anggota3@sikopma.test'],
            ['nim' => '99999999', 'name' => 'Anggota 4', 'email' => 'anggota4@sikopma.test'],
            ['nim' => '10101010', 'name' => 'Anggota 5', 'email' => 'anggota5@sikopma.test'],
            ['nim' => '11111112', 'name' => 'Anggota 6', 'email' => 'anggota6@sikopma.test'],
            ['nim' => '12121212', 'name' => 'Anggota 7', 'email' => 'anggota7@sikopma.test'],
            ['nim' => '13131313', 'name' => 'Anggota 8', 'email' => 'anggota8@sikopma.test'],
            ['nim' => '14141414', 'name' => 'Anggota 9', 'email' => 'anggota9@sikopma.test'],
            ['nim' => '15151515', 'name' => 'Anggota 10', 'email' => 'anggota10@sikopma.test'],
        ];

        foreach ($regularMembers as $member) {
            $user = User::firstOrCreate(
                ['nim' => $member['nim']],
                [
                    'name' => $member['name'],
                    'email' => $member['email'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                ]
            );
            $user->assignRole('Anggota');
        }
    }
}
