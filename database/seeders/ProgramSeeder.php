<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'code'        => 'SETUP',
                'name'        => 'Small Enterprise Technology Upgrading Program',
                'description' => 'Assists small enterprises in adopting improved technologies to increase productivity and competitiveness.',
                'is_active'   => true,
            ],
            [
                'code'        => 'GIA',
                'name'        => 'Grant-in-Aid',
                'description' => 'Provides financial assistance to individuals, institutions, and local government units for science and technology activities.',
                'is_active'   => true,
            ],
            [
                'code'        => 'CEST',
                'name'        => 'Community Empowerment through Science and Technology',
                'description' => 'Delivers science and technology interventions to marginalized communities to improve their quality of life.',
                'is_active'   => true,
            ],
            [
                'code'        => 'SSCP',
                'name'        => 'Smart and Sustainable Communities Program',
                'description' => 'Promotes smart and sustainable community development through science, technology, and innovation-based solutions.',
                'is_active'   => true,
            ],
        ];

        foreach ($programs as $program) {
            DB::table('programs')->updateOrInsert(
                ['code' => $program['code']],
                array_merge($program, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
