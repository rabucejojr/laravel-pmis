<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setup_accomplishments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('encoded_by')->constrained('users')->restrictOnDelete();
            $table->unsignedSmallInteger('year');
            // targets
            $table->unsignedInteger('target_num_projects')->default(0);
            $table->decimal('target_ifund_amount', 15, 2)->default(0);
            $table->decimal('target_gross_sales',  15, 2)->default(0);
            $table->unsignedInteger('target_employment')->default(0);
            $table->unsignedInteger('target_trainings')->default(0);
            // actuals
            $table->unsignedInteger('actual_num_projects')->default(0);
            $table->decimal('actual_ifund_amount', 15, 2)->default(0);
            $table->decimal('actual_gross_sales',  15, 2)->default(0);
            $table->unsignedInteger('actual_employment')->default(0);
            $table->unsignedInteger('actual_trainings')->default(0);
            // verification
            $table->enum('verified_status', ['pending', 'verified', 'flagged', 'rejected'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'year'], 'sa_project_year_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setup_accomplishments');
    }
};
