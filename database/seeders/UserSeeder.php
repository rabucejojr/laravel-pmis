<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'   => 'System Administrator',
                'email'  => 'admin@dostsdn.gov.ph',
                'role'   => 'admin',
                'office' => 'DOST-PSTO Surigao del Norte',
            ],
            [
                'name'   => 'Data Encoder',
                'email'  => 'encoder@dostsdn.gov.ph',
                'role'   => 'encoder',
                'office' => 'DOST-PSTO Surigao del Norte',
            ],
            [
                'name'   => 'Data Verifier',
                'email'  => 'verifier@dostsdn.gov.ph',
                'role'   => 'verifier',
                'office' => 'DOST-PSTO Surigao del Norte',
            ],
            [
                'name'   => 'Report Viewer',
                'email'  => 'viewer@dostsdn.gov.ph',
                'role'   => 'viewer',
                'office' => 'DOST-PSTO Surigao del Norte',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}
