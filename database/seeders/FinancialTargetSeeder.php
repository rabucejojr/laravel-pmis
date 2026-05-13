<?php

namespace Database\Seeders;

use App\Models\FinancialTarget;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialTargetSeeder extends Seeder
{
    public function run(): void
    {
        $encoder = User::where('role', 'encoder')->first();
        $programs = DB::table('programs')->pluck('id', 'code');

        // Line-item templates per program
        $lineItems = [
            'GIA'  => ['Personnel Services', 'MOOE', 'Capital Outlay'],
            'CEST' => ['Personnel Services', 'MOOE'],
            'SSCP' => ['Personnel Services', 'MOOE', 'Capital Outlay', 'Equipment Procurement'],
        ];

        // Quarter → representative month mapping
        $quarterMonth = [1 => 3, 2 => 6, 3 => 9, 4 => 12];

        foreach (['GIA', 'CEST', 'SSCP'] as $code) {
            $projects = Project::whereHas('program', fn ($q) => $q->where('code', $code))->get();

            foreach ($projects as $project) {
                $budget     = (float) $project->total_approved_budget;
                $items      = $lineItems[$code];
                $itemCount  = count($items);
                // Spread the budget roughly across line items and quarters
                $itemBudget = $budget / $itemCount;

                foreach ($items as $lineItem) {
                    foreach ([1, 2, 3, 4] as $q) {
                        $month = $quarterMonth[$q];

                        // Vary amounts by quarter (ramp-up pattern)
                        $qWeights    = [1 => 0.20, 2 => 0.25, 3 => 0.30, 4 => 0.25];
                        $target      = round($itemBudget * $qWeights[$q], 2);
                        $obligated   = round($target * 0.85, 2);
                        $disbursed   = round($obligated * 0.80, 2);

                        // Q4 still pending for review; Q1-Q3 verified
                        $status = ($q <= 3) ? 'verified' : 'pending';

                        FinancialTarget::updateOrCreate(
                            [
                                'project_id' => $project->id,
                                'year'       => 2025,
                                'quarter'    => $q,
                                'month'      => $month,
                                'line_item'  => $lineItem,
                            ],
                            [
                                'encoded_by'        => $encoder->id,
                                'target_amount'     => $target,
                                'obligated_amount'  => $status === 'verified' ? $obligated : 0,
                                'disbursed_amount'  => $status === 'verified' ? $disbursed : 0,
                                'verified_status'   => $status,
                                'verification_notes'=> null,
                            ]
                        );
                    }

                    // Additional 2024 historical verified entries (Q1–Q4)
                    foreach ([1, 2, 3, 4] as $q) {
                        $month   = $quarterMonth[$q];
                        $target  = round($itemBudget * 0.22, 2);
                        FinancialTarget::updateOrCreate(
                            [
                                'project_id' => $project->id,
                                'year'       => 2024,
                                'quarter'    => $q,
                                'month'      => $month,
                                'line_item'  => $lineItem,
                            ],
                            [
                                'encoded_by'       => $encoder->id,
                                'target_amount'    => $target,
                                'obligated_amount' => round($target * 0.90, 2),
                                'disbursed_amount' => round($target * 0.85, 2),
                                'verified_status'  => 'verified',
                            ]
                        );
                    }
                }
            }
        }
    }
}
