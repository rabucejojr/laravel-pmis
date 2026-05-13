<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\SetupAccomplishment;
use App\Models\User;
use Illuminate\Database\Seeder;

class SetupAccomplishmentSeeder extends Seeder
{
    public function run(): void
    {
        $encoder = User::where('role', 'encoder')->first();

        $projects = Project::whereHas('program', fn ($q) => $q->where('code', 'SETUP'))->get();

        // Per-project KPI profiles — realistic SETUP figures
        $profiles = [
            [
                // Technology Upgrading for Surigao Coco-Based Products
                2024 => [
                    'target_num_projects'  => 3,  'actual_num_projects'  => 3,
                    'target_ifund_amount'  => 180000.00, 'actual_ifund_amount'  => 172000.00,
                    'target_gross_sales'   => 600000.00, 'actual_gross_sales'   => 625000.00,
                    'target_employment'    => 25, 'actual_employment'    => 24,
                    'target_trainings'     => 4,  'actual_trainings'     => 4,
                    'verified_status'      => 'verified',
                ],
                2025 => [
                    'target_num_projects'  => 4,  'actual_num_projects'  => 3,
                    'target_ifund_amount'  => 220000.00, 'actual_ifund_amount'  => 195000.00,
                    'target_gross_sales'   => 750000.00, 'actual_gross_sales'   => 710000.00,
                    'target_employment'    => 30, 'actual_employment'    => 28,
                    'target_trainings'     => 5,  'actual_trainings'     => 4,
                    'verified_status'      => 'pending',
                ],
            ],
            [
                // Modernization of Seaweed Processing for Coastal Communities
                2024 => [
                    'target_num_projects'  => 4,  'actual_num_projects'  => 4,
                    'target_ifund_amount'  => 280000.00, 'actual_ifund_amount'  => 275000.00,
                    'target_gross_sales'   => 900000.00, 'actual_gross_sales'   => 920000.00,
                    'target_employment'    => 35, 'actual_employment'    => 38,
                    'target_trainings'     => 6,  'actual_trainings'     => 6,
                    'verified_status'      => 'verified',
                ],
                2025 => [
                    'target_num_projects'  => 5,  'actual_num_projects'  => 4,
                    'target_ifund_amount'  => 320000.00, 'actual_ifund_amount'  => 290000.00,
                    'target_gross_sales'   => 1100000.00, 'actual_gross_sales'  => 980000.00,
                    'target_employment'    => 40, 'actual_employment'    => 37,
                    'target_trainings'     => 7,  'actual_trainings'     => 6,
                    'verified_status'      => 'pending',
                ],
            ],
        ];

        foreach ($projects as $index => $project) {
            $profile = $profiles[$index] ?? $profiles[0];

            foreach ($profile as $year => $kpis) {
                SetupAccomplishment::updateOrCreate(
                    ['project_id' => $project->id, 'year' => $year],
                    array_merge($kpis, ['encoded_by' => $encoder->id])
                );
            }
        }
    }
}
