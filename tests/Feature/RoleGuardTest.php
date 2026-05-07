<?php

use App\Models\FinancialTarget;
use App\Models\PhysicalAccomplishment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── Verification Queue access ─────────────────────────────────────────────────

it('admin can access the verification queue', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('verification.index'))
        ->assertOk();
});

it('verifier can access the verification queue', function () {
    $verifier = User::factory()->verifier()->create();

    $this->actingAs($verifier)
        ->get(route('verification.index'))
        ->assertOk();
});

it('encoder cannot access the verification queue', function () {
    $encoder = User::factory()->encoder()->create();

    $this->actingAs($encoder)
        ->get(route('verification.index'))
        ->assertForbidden();
});

it('viewer cannot access the verification queue', function () {
    $viewer = User::factory()->viewer()->create();

    $this->actingAs($viewer)
        ->get(route('verification.index'))
        ->assertForbidden();
});

// ── User Management access ────────────────────────────────────────────────────

it('admin can access user management', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk();
});

it('encoder cannot access user management', function () {
    $encoder = User::factory()->encoder()->create();

    $this->actingAs($encoder)
        ->get(route('users.index'))
        ->assertForbidden();
});

it('verifier cannot access user management', function () {
    $verifier = User::factory()->verifier()->create();

    $this->actingAs($verifier)
        ->get(route('users.index'))
        ->assertForbidden();
});

it('viewer cannot access user management', function () {
    $viewer = User::factory()->viewer()->create();

    $this->actingAs($viewer)
        ->get(route('users.index'))
        ->assertForbidden();
});

// ── Financial Target create/store access ──────────────────────────────────────

it('encoder can POST to create a financial target', function () {
    $encoder = User::factory()->encoder()->create();
    $project = Project::factory()->create();

    $this->actingAs($encoder)
        ->post(route('projects.financial-targets.store', $project), [
            'year'             => 2025,
            'quarter'          => 1,
            'month'            => 3,
            'line_item'        => 'MOOE',
            'target_amount'    => '10000.00',
            'obligated_amount' => '5000.00',
            'disbursed_amount' => '2000.00',
        ])
        ->assertRedirect();
});

it('viewer cannot POST to create a financial target', function () {
    $viewer  = User::factory()->viewer()->create();
    $project = Project::factory()->create();

    $this->actingAs($viewer)
        ->post(route('projects.financial-targets.store', $project), [
            'year'             => 2025,
            'quarter'          => 1,
            'month'            => 3,
            'line_item'        => 'MOOE',
            'target_amount'    => '10000.00',
            'obligated_amount' => '5000.00',
            'disbursed_amount' => '2000.00',
        ])
        ->assertForbidden();
});

// ── Physical Accomplishment create/store access ───────────────────────────────

it('encoder can POST to create a physical accomplishment', function () {
    $encoder = User::factory()->encoder()->create();
    $project = Project::factory()->create();

    $this->actingAs($encoder)
        ->post(route('projects.physical-accomplishments.store', $project), [
            'year'               => 2025,
            'quarter'            => 1,
            'month'              => 3,
            'indicator_name'     => 'Number of beneficiaries',
            'target_value'       => '100.00',
            'accomplished_value' => '80.00',
        ])
        ->assertRedirect();
});

it('viewer cannot POST to create a physical accomplishment', function () {
    $viewer  = User::factory()->viewer()->create();
    $project = Project::factory()->create();

    $this->actingAs($viewer)
        ->post(route('projects.physical-accomplishments.store', $project), [
            'year'               => 2025,
            'quarter'            => 1,
            'month'              => 3,
            'indicator_name'     => 'Number of beneficiaries',
            'target_value'       => '100.00',
            'accomplished_value' => '80.00',
        ])
        ->assertForbidden();
});

// ── Viewer data scoping: cannot see non-verified entries ──────────────────────

it('viewer Livewire table query only returns verified financial targets', function () {
    $viewer  = User::factory()->viewer()->create();
    $encoder = User::factory()->encoder()->create();
    $project = Project::factory()->create();

    FinancialTarget::factory()->create([
        'project_id'      => $project->id,
        'encoded_by'      => $encoder->id,
        'year'            => 2025, 'quarter' => 1, 'month' => 3,
        'line_item'       => 'MOOE',
        'target_amount'   => 1000,
        'verified_status' => 'pending',
    ]);

    FinancialTarget::factory()->create([
        'project_id'      => $project->id,
        'encoded_by'      => $encoder->id,
        'year'            => 2025, 'quarter' => 2, 'month' => 4,
        'line_item'       => 'Capital Outlay',
        'target_amount'   => 2000,
        'verified_status' => 'verified',
    ]);

    // Viewer should only see the verified entry at the query level
    $query = \App\Models\FinancialTarget::query()
        ->when(! $viewer->hasRole(['admin', 'encoder', 'verifier']),
            fn ($q) => $q->where('verified_status', 'verified')
        );

    expect($query->count())->toBe(1)
        ->and($query->first()->verified_status)->toBe('verified');
});

it('viewer Livewire table query only returns verified physical accomplishments', function () {
    $viewer  = User::factory()->viewer()->create();
    $encoder = User::factory()->encoder()->create();
    $project = Project::factory()->create();

    PhysicalAccomplishment::factory()->create([
        'project_id'         => $project->id,
        'encoded_by'         => $encoder->id,
        'year'               => 2025, 'quarter' => 1, 'month' => 3,
        'indicator_name'     => 'Pending Indicator',
        'target_value'       => 100,
        'accomplished_value' => 80,
        'verified_status'    => 'flagged',
    ]);

    PhysicalAccomplishment::factory()->create([
        'project_id'         => $project->id,
        'encoded_by'         => $encoder->id,
        'year'               => 2025, 'quarter' => 2, 'month' => 4,
        'indicator_name'     => 'Verified Indicator',
        'target_value'       => 100,
        'accomplished_value' => 90,
        'verified_status'    => 'verified',
    ]);

    $query = \App\Models\PhysicalAccomplishment::query()
        ->when(! $viewer->hasRole(['admin', 'encoder', 'verifier']),
            fn ($q) => $q->where('verified_status', 'verified')
        );

    expect($query->count())->toBe(1)
        ->and($query->first()->verified_status)->toBe('verified');
});
