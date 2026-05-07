<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('encoded_by')->constrained('users')->restrictOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('quarter');
            $table->unsignedTinyInteger('month');
            $table->string('line_item');
            $table->decimal('target_amount', 15, 2);
            $table->decimal('obligated_amount', 15, 2)->default(0);
            $table->decimal('disbursed_amount', 15, 2)->default(0);
            $table->enum('verified_status', ['pending', 'verified', 'flagged', 'rejected'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'year', 'quarter', 'month', 'line_item']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_targets');
    }
};
