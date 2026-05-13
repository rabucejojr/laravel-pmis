<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $programs = DB::table('programs')->pluck('id', 'code');

        $projects = [
            // SETUP projects
            [
                'program_id'            => $programs['SETUP'],
                'title'                 => 'Technology Upgrading for Surigao Coco-Based Products',
                'description'           => 'Introduction of improved processing technologies for coconut-based products in Surigao del Norte.',
                'implementing_agency'   => 'DOST-PSTO Surigao del Norte',
                'location'              => 'Surigao City, Surigao del Norte',
                'start_date'            => '2024-01-01',
                'end_date'              => '2025-12-31',
                'total_approved_budget' => 500000.00,
                'status'                => 'active',
            ],
            [
                'program_id'            => $programs['SETUP'],
                'title'                 => 'Modernization of Seaweed Processing for Coastal Communities',
                'description'           => 'Provision of modern drying and processing equipment for seaweed enterprises.',
                'implementing_agency'   => 'DOST-PSTO Surigao del Norte',
                'location'              => 'Dapa, Siargao Island',
                'start_date'            => '2024-03-01',
                'end_date'              => '2026-02-28',
                'total_approved_budget' => 750000.00,
                'status'                => 'active',
            ],
            // GIA projects
            [
                'program_id'            => $programs['GIA'],
                'title'                 => 'Development of Indigenous Vegetable Production Technologies',
                'description'           => 'Research and development on improved production methods for indigenous vegetables in Surigao del Norte.',
                'implementing_agency'   => 'Surigao del Norte State University',
                'location'              => 'Surigao City, Surigao del Norte',
                'start_date'            => '2023-07-01',
                'end_date'              => '2025-06-30',
                'total_approved_budget' => 1200000.00,
                'status'                => 'active',
            ],
            [
                'program_id'            => $programs['GIA'],
                'title'                 => 'Aquaculture Technology Transfer for Fisherfolk of Siargao',
                'description'           => 'Training and technology transfer program for sustainable aquaculture practices.',
                'implementing_agency'   => 'Siargao Island State Polytechnic College',
                'location'              => 'Del Carmen, Siargao Island',
                'start_date'            => '2023-01-01',
                'end_date'              => '2024-12-31',
                'total_approved_budget' => 980000.00,
                'status'                => 'liquidated',
            ],
            // CEST projects
            [
                'program_id'            => $programs['CEST'],
                'title'                 => 'CEST for Organic Vegetable Production in Upland Communities',
                'description'           => 'Deployment of organic farming technologies to upland farmers in Surigao del Norte.',
                'implementing_agency'   => 'DOST-PSTO Surigao del Norte',
                'location'              => 'Malimono, Surigao del Norte',
                'start_date'            => '2024-06-01',
                'end_date'              => '2025-05-31',
                'total_approved_budget' => 300000.00,
                'status'                => 'active',
            ],
            [
                'program_id'            => $programs['CEST'],
                'title'                 => 'Barangay Science and Technology Centers Establishment',
                'description'           => 'Setting up S&T centers in selected barangays to provide technology demonstrations and livelihood training.',
                'implementing_agency'   => 'DOST-PSTO Surigao del Norte',
                'location'              => 'Placer, Surigao del Norte',
                'start_date'            => '2025-01-01',
                'end_date'              => '2026-12-31',
                'total_approved_budget' => 450000.00,
                'status'                => 'active',
            ],
            // SSCP projects
            [
                'program_id'            => $programs['SSCP'],
                'title'                 => 'Smart Water Management System for Surigao City',
                'description'           => 'IoT-based water quality monitoring and distribution management system for Surigao City water utility.',
                'implementing_agency'   => 'DOST-PSTO Surigao del Norte',
                'location'              => 'Surigao City, Surigao del Norte',
                'start_date'            => '2024-01-01',
                'end_date'              => '2026-12-31',
                'total_approved_budget' => 2000000.00,
                'status'                => 'active',
            ],
            [
                'program_id'            => $programs['SSCP'],
                'title'                 => 'IoT-Based Agricultural Monitoring for Surigao del Norte',
                'description'           => 'Deployment of sensor networks and data analytics platform for precision agriculture in upland farming communities.',
                'implementing_agency'   => 'DOST-PSTO Surigao del Norte',
                'location'              => 'Tubod, Surigao del Norte',
                'start_date'            => '2024-06-01',
                'end_date'              => '2026-05-31',
                'total_approved_budget' => 1500000.00,
                'status'                => 'active',
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                [
                    'program_id' => $project['program_id'],
                    'title'      => $project['title'],
                ],
                array_merge($project, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
