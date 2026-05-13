<?php

namespace App\Services;

use App\Models\FinancialTarget;
use App\Models\PhysicalAccomplishment;
use App\Models\SetupAccomplishment;
use Carbon\Carbon;

class VerificationService
{
    /**
     * Run all automated checks on a FinancialTarget.
     *
     * @return string[]  Flag messages; empty array means all checks passed.
     */
    public function checkFinancialTarget(FinancialTarget $target): array
    {
        $target->loadMissing('project.documents');
        $project = $target->project;
        $flags   = [];

        // Rule 1: entry period is within project timeline
        if (! $this->periodWithinTimeline($target->year, $target->month, $project)) {
            $flags[] = sprintf(
                'Entry period %s/%02d falls outside the project timeline (%s – %s).',
                $target->year,
                $target->month,
                $project->start_date->format('Y-m'),
                $project->end_date->format('Y-m')
            );
        }

        // Rule 2: cumulative target_amount for line item ≤ total_approved_budget
        $cumulative = FinancialTarget::where('project_id', $project->id)
            ->where('line_item', $target->line_item)
            ->sum('target_amount');

        if ((float) $cumulative > (float) $project->total_approved_budget) {
            $flags[] = sprintf(
                'Cumulative target for "%s" (₱%s) exceeds the total approved budget (₱%s).',
                $target->line_item,
                number_format($cumulative, 2),
                number_format($project->total_approved_budget, 2)
            );
        }

        // Rule 3: obligated_amount ≤ target_amount
        if ((float) $target->obligated_amount > (float) $target->target_amount) {
            $flags[] = sprintf(
                'Obligated amount (₱%s) exceeds target amount (₱%s).',
                number_format($target->obligated_amount, 2),
                number_format($target->target_amount, 2)
            );
        }

        // Rule 4: disbursed_amount ≤ obligated_amount
        if ((float) $target->disbursed_amount > (float) $target->obligated_amount) {
            $flags[] = sprintf(
                'Disbursed amount (₱%s) exceeds obligated amount (₱%s).',
                number_format($target->disbursed_amount, 2),
                number_format($target->obligated_amount, 2)
            );
        }

        // Rule 5: at least one GAA or financial_plan document exists for the project
        $hasBudgetDoc = $project->documents
            ->whereIn('document_type', ['GAA', 'financial_plan'])
            ->isNotEmpty();

        if (! $hasBudgetDoc) {
            $flags[] = 'No GAA or Financial Plan document has been uploaded for this project.';
        }

        return $flags;
    }

    /**
     * Run all automated checks on a PhysicalAccomplishment.
     *
     * @return string[]  Flag messages; empty array means all checks passed.
     */
    public function checkPhysicalAccomplishment(PhysicalAccomplishment $pa): array
    {
        $pa->loadMissing('project.documents');
        $project = $pa->project;
        $flags   = [];

        // Rule 1: entry period is within project timeline
        if (! $this->periodWithinTimeline($pa->year, $pa->month, $project)) {
            $flags[] = sprintf(
                'Entry period %s/%02d falls outside the project timeline (%s – %s).',
                $pa->year,
                $pa->month,
                $project->start_date->format('Y-m'),
                $project->end_date->format('Y-m')
            );
        }

        // Rule 2: accomplished_value ≥ 0
        if ((float) $pa->accomplished_value < 0) {
            $flags[] = sprintf(
                'Accomplished value (%.2f) cannot be negative.',
                $pa->accomplished_value
            );
        }

        // Rule 3: accomplishment_rate > 150% → flag for review (do not reject)
        $rate = (float) $pa->accomplishment_rate;
        if ($rate > 150.0) {
            $flags[] = sprintf(
                'Accomplishment rate (%.2f%%) exceeds 150%% — please verify the target and accomplished values.',
                $rate
            );
        }

        // Rule 4: indicator_name found in work_plan document extracted_text
        $workPlanDocs = $project->documents->where('document_type', 'work_plan');

        if ($workPlanDocs->isEmpty()) {
            $flags[] = 'No Work Plan document has been uploaded for this project; indicator name could not be verified.';
        } else {
            $docsWithText = $workPlanDocs->filter(fn ($d) => ! empty($d->extracted_text));

            if ($docsWithText->isEmpty()) {
                $flags[] = 'Work Plan document text has not been extracted yet; indicator name could not be verified.';
            } else {
                $needle = mb_strtolower($pa->indicator_name);
                $found  = $docsWithText->contains(
                    fn ($doc) => str_contains(mb_strtolower($doc->extracted_text), $needle)
                );

                if (! $found) {
                    $flags[] = sprintf(
                        'Indicator name "%s" was not found in any Work Plan document for this project.',
                        $pa->indicator_name
                    );
                }
            }
        }

        return $flags;
    }

    /**
     * Run all automated checks on a SetupAccomplishment.
     *
     * @return string[]  Flag messages; empty array means all checks passed.
     */
    public function checkSetupAccomplishment(SetupAccomplishment $entry): array
    {
        $entry->loadMissing('project');
        $project = $entry->project;
        $flags   = [];

        // Rule 1: year within project timeline
        $startYear = $project->start_date->year;
        $endYear   = $project->end_date->year;
        if ($entry->year < $startYear || $entry->year > $endYear) {
            $flags[] = sprintf(
                'Year %d is outside the project timeline (%d–%d).',
                $entry->year,
                $startYear,
                $endYear
            );
        }

        // Rule 2: flag (not reject) if any count actual > 200% of target
        $countChecks = [
            'num_projects' => 'Number of Projects',
            'employment'   => 'Employment Generated',
            'trainings'    => 'Trainings Conducted',
        ];

        foreach ($countChecks as $key => $label) {
            $target = (int) $entry->{"target_{$key}"};
            $actual = (int) $entry->{"actual_{$key}"};
            if ($target > 0 && $actual > ($target * 2)) {
                $flags[] = "{$label}: actual ({$actual}) exceeds 200% of target ({$target}) — please verify.";
            }
        }

        return $flags;
    }

    /**
     * Check whether the given year/month falls within the project's start and end dates.
     */
    private function periodWithinTimeline(int $year, int $month, $project): bool
    {
        $entryDate    = Carbon::create($year, $month, 1);
        $startOfMonth = $project->start_date->copy()->startOfMonth();
        $endOfMonth   = $project->end_date->copy()->endOfMonth();

        return $entryDate->between($startOfMonth, $endOfMonth);
    }
}
