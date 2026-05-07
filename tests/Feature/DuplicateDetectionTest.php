<?php

use App\Models\FinancialTarget;
use App\Models\PhysicalAccomplishment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── Financial Target duplicate ────────────────────────────────────────────────

it('storing a duplicate financial target redirects back with a validation error on line_item', function () {
    $encoder = User::factory()->encoder()->create();
    $project = Project::factory()->create();

    // First entry — unique combination
    FinancialTarget::factory()->create([
        'project_id'  => $project->id,
        'encoded_by'  => $encoder->id,
        'year'        => 2025,
        'quarter'     => 1,
        'month'       => 3,
        'line_item'   => 'Personnel Services',
        'target_amount'    => 10_000,
        'obligated_amount' => 0,
        'disbursed_amount' => 0,
    ]);

    // Attempt to create exact duplicate via POST
    $response = $this->actingAs($encoder)
        ->post(route('projects.financial-targets.store', $project), [
            'year'             => 2025,
            'quarter'          => 1,
            'month'            => 3,
            'line_item'        => 'Personnel Services',
            'target_amount'    => '20000.00',
            'obligated_amount' => '0.00',
            'disbursed_amount' => '0.00',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('line_item');
});

it('storing a financial target with a different line_item succeeds', function () {
    $encoder = User::factory()->encoder()->create();
    $project = Project::factory()->create();

    FinancialTarget::factory()->create([
        'project_id'  => $project->id,
        'encoded_by'  => $encoder->id,
        'year'        => 2025,
        'quarter'     => 1,
        'month'       => 3,
        'line_item'   => 'Personnel Services',
        'target_amount'    => 10_000,
        'obligated_amount' => 0,
        'disbursed_amount' => 0,
    ]);

    // Different line_item — should succeed
    $response = $this->actingAs($encoder)
        ->post(route('projects.financial-targets.store', $project), [
            'year'             => 2025,
            'quarter'          => 1,
            'month'            => 3,
            'line_item'        => 'MOOE',
            'target_amount'    => '5000.00',
            'obligated_amount' => '0.00',
            'disbursed_amount' => '0.00',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

// ── Physical Accomplishment duplicate ────────────────────────────────────────

it('storing a duplicate physical accomplishment redirects back with a validation error on indicator_name', function () {
    $encoder = User::factory()->encoder()->create();
    $project = Project::factory()->create();

    // First entry
    PhysicalAccomplishment::factory()->create([
        'project_id'         => $project->id,
        'encoded_by'         => $encoder->id,
        'year'               => 2025,
        'quarter'            => 1,
        'month'              => 3,
        'indicator_name'     => 'Number of beneficiaries trained',
        'target_value'       => 100,
        'accomplished_value' => 80,
    ]);

    // Duplicate attempt
    $response = $this->actingAs($encoder)
        ->post(route('projects.physical-accomplishments.store', $project), [
            'year'               => 2025,
            'quarter'            => 1,
            'month'              => 3,
            'indicator_name'     => 'Number of beneficiaries trained',
            'target_value'       => '100.00',
            'accomplished_value' => '90.00',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('indicator_name');
});

it('storing a physical accomplishment with a different indicator succeeds', function () {
    $encoder = User::factory()->encoder()->create();
    $project = Project::factory()->create();

    PhysicalAccomplishment::factory()->create([
        'project_id'         => $project->id,
        'encoded_by'         => $encoder->id,
        'year'               => 2025,
        'quarter'            => 1,
        'month'              => 3,
        'indicator_name'     => 'Number of beneficiaries trained',
        'target_value'       => 100,
        'accomplished_value' => 80,
    ]);

    // Different indicator — should succeed
    $response = $this->actingAs($encoder)
        ->post(route('projects.physical-accomplishments.store', $project), [
            'year'               => 2025,
            'quarter'            => 1,
            'month'              => 3,
            'indicator_name'     => 'Number of trainings conducted',
            'target_value'       => '10.00',
            'accomplished_value' => '8.00',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});
