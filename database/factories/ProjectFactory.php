<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'program_id'            => Program::factory(),
            'title'                 => fake()->sentence(4, false),
            'description'           => null,
            'implementing_agency'   => 'DOST-SDN',
            'location'              => fake()->city(),
            'start_date'            => '2024-01-01',
            'end_date'              => '2026-12-31',
            'total_approved_budget' => 1_000_000.00,
            'status'                => 'active',
        ];
    }

    public function withTimeline(string $start, string $end): static
    {
        return $this->state(['start_date' => $start, 'end_date' => $end]);
    }

    public function withBudget(float $amount): static
    {
        return $this->state(['total_approved_budget' => $amount]);
    }
}
