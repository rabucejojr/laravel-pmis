<?php

namespace Database\Seeders;

use App\Models\PhysicalAccomplishment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class PhysicalAccomplishmentSeeder extends Seeder
{
    public function run(): void
    {
        $encoder = User::where('role', 'encoder')->first();

        // Indicators per program — realistic names that could appear in a work plan
        $indicators = [
            'GIA' => [
                ['name' => 'Number of Researchers Trained',          'annual_target' => 20],
                ['name' => 'Research Outputs / Technical Reports',    'annual_target' => 4],
                ['name' => 'Technology Prototypes Developed',         'annual_target' => 2],
            ],
            'CEST' => [
                ['name' => 'Number of Beneficiaries Trained',         'annual_target' => 120],
                ['name' => 'Number of Technologies Demonstrated',     'annual_target' => 6],
                ['name' => 'Community Learning Centers Established',  'annual_target' => 2],
            ],
            'SSCP' => [
                ['name' => 'Smart Solutions Deployed',                'annual_target' => 4],
                ['name' => 'Communities Served by IoT Systems',       'annual_target' => 5],
                ['name' => 'Number of IoT Devices Installed',         'annual_target' => 50],
            ],
        ];

        // Q→month mapping and per-quarter target weight (annual broken down)
        $qMonth   = [1 => 3, 2 => 6, 3 => 9, 4 => 12];
        $qWeights = [1 => 0.20, 2 => 0.25, 3 => 0.30, 4 => 0.25];

        foreach (['GIA', 'CEST', 'SSCP'] as $code) {
            $projects = Project::whereHas('program', fn ($q) => $q->where('code', $code))->get();

            foreach ($projects as $project) {
                foreach ($indicators[$code] as $indicator) {
                    $annualTarget = $indicator['annual_target'];

                    foreach ([1, 2, 3, 4] as $q) {
                        $month  = $qMonth[$q];
                        $target = round($annualTarget * $qWeights[$q], 2);

                        // Q1–Q3 verified with ~90% accomplishment; Q4 pending (0 accomplished yet)
                        $status      = ($q <= 3) ? 'verified' : 'pending';
                        $accomplished = $status === 'verified' ? round($target * 0.90, 2) : 0;

                        PhysicalAccomplishment::updateOrCreate(
                            [
                                'project_id'     => $project->id,
                                'year'           => 2025,
                                'quarter'        => $q,
                                'month'          => $month,
                                'indicator_name' => $indicator['name'],
                            ],
                            [
                                'encoded_by'        => $encoder->id,
                                'target_value'      => $target,
                                'accomplished_value'=> $accomplished,
                                'verified_status'   => $status,
                                'verification_notes'=> null,
                            ]
                        );
                    }

                    // 2024 historical verified entries
                    foreach ([1, 2, 3, 4] as $q) {
                        $month        = $qMonth[$q];
                        $target       = round($annualTarget * $qWeights[$q], 2);
                        $accomplished = round($target * 0.95, 2);

                        PhysicalAccomplishment::updateOrCreate(
                            [
                                'project_id'     => $project->id,
                                'year'           => 2024,
                                'quarter'        => $q,
                                'month'          => $month,
                                'indicator_name' => $indicator['name'],
                            ],
                            [
                                'encoded_by'         => $encoder->id,
                                'target_value'       => $target,
                                'accomplished_value' => $accomplished,
                                'verified_status'    => 'verified',
                            ]
                        );
                    }
                }
            }
        }
    }
}
