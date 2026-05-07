<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        return [
            'code'        => 'PRG-' . str_pad($counter, 3, '0', STR_PAD_LEFT),
            'name'        => fake()->words(3, true),
            'description' => null,
            'is_active'   => true,
        ];
    }
}
