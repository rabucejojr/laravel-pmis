<?php

use App\Models\FinancialTarget;
use App\Models\PhysicalAccomplishment;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\User;
use App\Services\VerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── Helpers ──────────────────────────────────────────────────────────────────

function makeProject(array $attrs = []): Project
{
    return Project::factory()->create(array_merge([
        'start_date'            => '2024-01-01',
        'end_date'              => '2026-12-31',
        'total_approved_budget' => 500_000.00,
    ], $attrs));
}

function makeEncoder(): User
{
    return User::factory()->encoder()->create();
}

// ── Financial Target — Rule 1: period within timeline ────────────────────────

it('FT passes when entry period is within project timeline', function () {
    $project = makeProject(['start_date' => '2024-01-01', 'end_date' => '2026-12-31']);
    $encoder = makeEncoder();

    ProjectDocument::factory()->gaa()->create(['project_id' => $project->id, 'uploaded_by' => $encoder->id]);

    $ft = FinancialTarget::factory()->create([
        'project_id'       => $project->id,
        'encoded_by'       => $encoder->id,
        'year'             => 2025,
        'month'            => 6,
        'target_amount'    => 100.00,
        'obligated_amount' => 80.00,
        'disbursed_amount' => 60.00,
    ]);

    $flags = app(VerificationService::class)->checkFinancialTarget($ft);

    expect($flags)->toBeEmpty();
});

it('FT flags when entry period is before project start date', function () {
    $project = makeProject(['start_date' => '2025-01-01', 'end_date' => '2026-12-31']);
    $encoder = makeEncoder();

    $ft = FinancialTarget::factory()->create([
        'project_id'       => $project->id,
        'encoded_by'       => $encoder->id,
        'year'             => 2024,
        'month'            => 6,
        'target_amount'    => 100.00,
        'obligated_amount' => 0.00,
        'disbursed_amount' => 0.00,
    ]);

    $flags = app(VerificationService::class)->checkFinancialTarget($ft);

    expect($flags)->not->toBeEmpty()
        ->and(implode(' ', $flags))->toContain('outside the project timeline');
});

it('FT flags when entry period is after project end date', function () {
    $project = makeProject(['start_date' => '2024-01-01', 'end_date' => '2024-12-31']);
    $encoder = makeEncoder();

    $ft = FinancialTarget::factory()->create([
        'project_id'       => $project->id,
        'encoded_by'       => $encoder->id,
        'year'             => 2025,
        'month'            => 3,
        'target_amount'    => 100.00,
        'obligated_amount' => 0.00,
        'disbursed_amount' => 0.00,
    ]);

    $flags = app(VerificationService::class)->checkFinancialTarget($ft);

    expect(implode(' ', $flags))->toContain('outside the project timeline');
});

// ── Financial Target — Rule 2: cumulative ≤ total_approved_budget ────────────

it('FT flags when cumulative target for line item exceeds budget', function () {
    $project = makeProject(['total_approved_budget' => 100_000.00]);
    $encoder = makeEncoder();

    // First entry uses 60k of the 100k budget
    FinancialTarget::factory()->create([
        'project_id'       => $project->id,
        'encoded_by'       => $encoder->id,
        'year'             => 2025,
        'quarter'          => 1,
        'month'            => 1,
        'line_item'        => 'Capital Outlay',
        'target_amount'    => 60_000.00,
        'obligated_amount' => 0.00,
        'disbursed_amount' => 0.00,
    ]);

    // Second entry pushes cumulative to 120k — over budget
    $ft = FinancialTarget::factory()->create([
        'project_id'       => $project->id,
        'encoded_by'       => $encoder->id,
        'year'             => 2025,
        'quarter'          => 2,
        'month'            => 4,
        'line_item'        => 'Capital Outlay',
        'target_amount'    => 60_000.00,
        'obligated_amount' => 0.00,
        'disbursed_amount' => 0.00,
    ]);

    $flags = app(VerificationService::class)->checkFinancialTarget($ft);

    expect(implode(' ', $flags))->toContain('total approved budget');
});

it('FT passes when cumulative target is within budget', function () {
    $project = makeProject(['total_approved_budget' => 500_000.00]);
    $encoder = makeEncoder();

    ProjectDocument::factory()->gaa()->create(['project_id' => $project->id, 'uploaded_by' => $encoder->id]);

    $ft = FinancialTarget::factory()->create([
        'project_id'       => $project->id,
        'encoded_by'       => $encoder->id,
        'year'             => 2025,
        'month'            => 3,
        'line_item'        => 'MOOE',
        'target_amount'    => 100_000.00,
        'obligated_amount' => 80_000.00,
        'disbursed_amount' => 60_000.00,
    ]);

    $flags = app(VerificationService::class)->checkFinancialTarget($ft);

    expect($flags)->toBeEmpty();
});

// ── Financial Target — Rule 3: obligated ≤ target ────────────────────────────

it('FT flags when obligated amount exceeds target amount', function () {
    $project = makeProject();
    $encoder = makeEncoder();

    $ft = FinancialTarget::factory()->create([
        'project_id'       => $project->id,
        'encoded_by'       => $encoder->id,
        'year'             => 2025,
        'month'            => 3,
        'target_amount'    => 50_000.00,
        'obligated_amount' => 70_000.00,
        'disbursed_amount' => 0.00,
    ]);

    $flags = app(VerificationService::class)->checkFinancialTarget($ft);

    expect(implode(' ', $flags))->toContain('Obligated amount');
});

// ── Financial Target — Rule 4: disbursed ≤ obligated ─────────────────────────

it('FT flags when disbursed amount exceeds obligated amount', function () {
    $project = makeProject();
    $encoder = makeEncoder();

    $ft = FinancialTarget::factory()->create([
        'project_id'       => $project->id,
        'encoded_by'       => $encoder->id,
        'year'             => 2025,
        'month'            => 3,
        'target_amount'    => 100_000.00,
        'obligated_amount' => 40_000.00,
        'disbursed_amount' => 60_000.00,
    ]);

    $flags = app(VerificationService::class)->checkFinancialTarget($ft);

    expect(implode(' ', $flags))->toContain('Disbursed amount');
});

// ── Financial Target — Rule 5: GAA or financial_plan doc must exist ───────────

it('FT flags when no GAA or financial_plan document exists', function () {
    $project = makeProject();
    $encoder = makeEncoder();

    $ft = FinancialTarget::factory()->create([
        'project_id'       => $project->id,
        'encoded_by'       => $encoder->id,
        'year'             => 2025,
        'month'            => 3,
        'target_amount'    => 10_000.00,
        'obligated_amount' => 5_000.00,
        'disbursed_amount' => 2_000.00,
    ]);

    $flags = app(VerificationService::class)->checkFinancialTarget($ft);

    expect(implode(' ', $flags))->toContain('No GAA or Financial Plan document');
});

it('FT does not flag missing document when GAA document exists', function () {
    $project = makeProject(['total_approved_budget' => 500_000.00]);
    $encoder = makeEncoder();

    ProjectDocument::factory()->gaa()->create([
        'project_id'  => $project->id,
        'uploaded_by' => $encoder->id,
    ]);

    $ft = FinancialTarget::factory()->create([
        'project_id'       => $project->id,
        'encoded_by'       => $encoder->id,
        'year'             => 2025,
        'month'            => 3,
        'target_amount'    => 10_000.00,
        'obligated_amount' => 8_000.00,
        'disbursed_amount' => 5_000.00,
    ]);

    $flags = app(VerificationService::class)->checkFinancialTarget($ft);

    $flagText = implode(' ', $flags);
    expect($flagText)->not->toContain('No GAA or Financial Plan document');
});

// ── Physical Accomplishment — Rule 1: period within timeline ──────────────────

it('PA flags when entry period is outside project timeline', function () {
    $project = makeProject(['start_date' => '2025-01-01', 'end_date' => '2025-12-31']);
    $encoder = makeEncoder();

    $pa = PhysicalAccomplishment::factory()->create([
        'project_id'         => $project->id,
        'encoded_by'         => $encoder->id,
        'year'               => 2024,
        'month'              => 6,
        'target_value'       => 100.00,
        'accomplished_value' => 80.00,
    ]);

    $flags = app(VerificationService::class)->checkPhysicalAccomplishment($pa);

    expect(implode(' ', $flags))->toContain('outside the project timeline');
});

// ── Physical Accomplishment — Rule 3: rate > 150% ────────────────────────────

it('PA flags when accomplishment rate exceeds 150 percent', function () {
    $project = makeProject();
    $encoder = makeEncoder();

    // target=100, accomplished=201 → rate=201%
    $pa = PhysicalAccomplishment::factory()->create([
        'project_id'         => $project->id,
        'encoded_by'         => $encoder->id,
        'year'               => 2025,
        'month'              => 3,
        'target_value'       => 100.00,
        'accomplished_value' => 201.00,
    ]);

    // Refresh from DB so the generated column is read
    $pa->refresh();

    $flags = app(VerificationService::class)->checkPhysicalAccomplishment($pa);

    expect(implode(' ', $flags))->toContain('150%');
});

it('PA does not flag rate at or below 150 percent', function () {
    $project = makeProject();
    $encoder = makeEncoder();

    ProjectDocument::factory()->workPlan('Number of beneficiaries trained')->create([
        'project_id'  => $project->id,
        'uploaded_by' => $encoder->id,
    ]);

    // target=100, accomplished=150 → rate=150%
    $pa = PhysicalAccomplishment::factory()->create([
        'project_id'         => $project->id,
        'encoded_by'         => $encoder->id,
        'year'               => 2025,
        'month'              => 3,
        'indicator_name'     => 'Number of beneficiaries trained',
        'target_value'       => 100.00,
        'accomplished_value' => 150.00,
    ]);

    $pa->refresh();

    $flags = app(VerificationService::class)->checkPhysicalAccomplishment($pa);

    $flagText = implode(' ', $flags);
    expect($flagText)->not->toContain('150%');
});

// ── Physical Accomplishment — Rule 4: indicator in work_plan ─────────────────

it('PA flags when no work_plan document exists for the project', function () {
    $project = makeProject();
    $encoder = makeEncoder();

    $pa = PhysicalAccomplishment::factory()->create([
        'project_id'         => $project->id,
        'encoded_by'         => $encoder->id,
        'year'               => 2025,
        'month'              => 3,
        'indicator_name'     => 'Number of beneficiaries trained',
        'target_value'       => 100.00,
        'accomplished_value' => 80.00,
    ]);

    $flags = app(VerificationService::class)->checkPhysicalAccomplishment($pa);

    expect(implode(' ', $flags))->toContain('No Work Plan document');
});

it('PA flags when work_plan document text has not been extracted', function () {
    $project = makeProject();
    $encoder = makeEncoder();

    // Work plan doc exists but extracted_text is null
    ProjectDocument::factory()->workPlan(null)->create([
        'project_id'  => $project->id,
        'uploaded_by' => $encoder->id,
    ]);

    $pa = PhysicalAccomplishment::factory()->create([
        'project_id'         => $project->id,
        'encoded_by'         => $encoder->id,
        'year'               => 2025,
        'month'              => 3,
        'indicator_name'     => 'Number of beneficiaries trained',
        'target_value'       => 100.00,
        'accomplished_value' => 80.00,
    ]);

    $flags = app(VerificationService::class)->checkPhysicalAccomplishment($pa);

    expect(implode(' ', $flags))->toContain('not been extracted yet');
});

it('PA flags when indicator name is not found in work_plan extracted text', function () {
    $project = makeProject();
    $encoder = makeEncoder();

    ProjectDocument::factory()->workPlan('Only mentions other activities here.')->create([
        'project_id'  => $project->id,
        'uploaded_by' => $encoder->id,
    ]);

    $pa = PhysicalAccomplishment::factory()->create([
        'project_id'         => $project->id,
        'encoded_by'         => $encoder->id,
        'year'               => 2025,
        'month'              => 3,
        'indicator_name'     => 'Number of beneficiaries trained',
        'target_value'       => 100.00,
        'accomplished_value' => 80.00,
    ]);

    $flags = app(VerificationService::class)->checkPhysicalAccomplishment($pa);

    expect(implode(' ', $flags))->toContain('was not found in any Work Plan document');
});

it('PA passes when indicator name is found case-insensitively in work_plan text', function () {
    $project = makeProject();
    $encoder = makeEncoder();

    ProjectDocument::factory()->workPlan('NUMBER OF BENEFICIARIES TRAINED during the project period.')->create([
        'project_id'  => $project->id,
        'uploaded_by' => $encoder->id,
    ]);

    $pa = PhysicalAccomplishment::factory()->create([
        'project_id'         => $project->id,
        'encoded_by'         => $encoder->id,
        'year'               => 2025,
        'month'              => 3,
        'indicator_name'     => 'Number of beneficiaries trained',
        'target_value'       => 100.00,
        'accomplished_value' => 80.00,
    ]);

    $flags = app(VerificationService::class)->checkPhysicalAccomplishment($pa);

    expect(implode(' ', $flags))->not->toContain('was not found in any Work Plan document');
});
