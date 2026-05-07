<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectDocumentFactory extends Factory
{
    protected $model = ProjectDocument::class;

    public function definition(): array
    {
        return [
            'project_id'     => Project::factory(),
            'uploaded_by'    => User::factory(),
            'document_type'  => 'GAA',
            'file_name'      => fake()->word() . '.pdf',
            'file_path'      => 'projects/1/GAA/test.pdf',
            'file_size'      => 2048,
            'mime_type'      => 'application/pdf',
            'extracted_text' => null,
        ];
    }

    public function gaa(): static
    {
        return $this->state(['document_type' => 'GAA']);
    }

    public function financialPlan(): static
    {
        return $this->state(['document_type' => 'financial_plan']);
    }

    public function workPlan(?string $extractedText = ''): static
    {
        return $this->state([
            'document_type'  => 'work_plan',
            'extracted_text' => $extractedText,
        ]);
    }
}
