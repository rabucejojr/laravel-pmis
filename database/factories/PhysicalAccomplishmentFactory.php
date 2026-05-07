<?php

namespace Database\Factories;

use App\Models\PhysicalAccomplishment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhysicalAccomplishmentFactory extends Factory
{
    protected $model = PhysicalAccomplishment::class;

    public function definition(): array
    {
        return [
            'project_id'         => Project::factory(),
            'encoded_by'         => User::factory(),
            'year'               => 2025,
            'quarter'            => 1,
            'month'              => 3,
            'indicator_name'     => 'Number of beneficiaries trained',
            'target_value'       => 100.00,
            'accomplished_value' => 80.00,
            'verified_status'    => 'pending',
        ];
    }

    public function verified(): static
    {
        return $this->state(['verified_status' => 'verified']);
    }

    public function pending(): static
    {
        return $this->state(['verified_status' => 'pending']);
    }

    // target_value=100, accomplished_value=200 → rate=200%
    public function overAchieved(): static
    {
        return $this->state([
            'target_value'       => 100.00,
            'accomplished_value' => 200.00,
        ]);
    }
}
