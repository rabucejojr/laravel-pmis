<?php

namespace Database\Factories;

use App\Models\FinancialTarget;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialTargetFactory extends Factory
{
    protected $model = FinancialTarget::class;

    public function definition(): array
    {
        return [
            'project_id'       => Project::factory(),
            'encoded_by'       => User::factory(),
            'year'             => 2025,
            'quarter'          => 1,
            'month'            => 3,
            'line_item'        => 'Personnel Services',
            'target_amount'    => 50_000.00,
            'obligated_amount' => 30_000.00,
            'disbursed_amount' => 20_000.00,
            'verified_status'  => 'pending',
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
}
