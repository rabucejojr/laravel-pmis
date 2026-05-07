<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_accomplishments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('encoded_by')->constrained('users')->restrictOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('quarter');
            $table->unsignedTinyInteger('month');
            $table->string('indicator_name');
            $table->decimal('target_value', 15, 2);
            $table->decimal('accomplished_value', 15, 2)->default(0);
            // Generated stored column — never accept as user input
            $table->decimal('accomplishment_rate', 5, 2)
                ->storedAs('ROUND((accomplished_value / NULLIF(target_value, 0)) * 100, 2)')
                ->nullable();
            $table->enum('verified_status', ['pending', 'verified', 'flagged', 'rejected'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'year', 'quarter', 'month', 'indicator_name'], 'pa_project_period_indicator_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_accomplishments');
    }
};
