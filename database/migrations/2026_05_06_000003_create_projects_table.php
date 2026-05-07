<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('implementing_agency');
            $table->string('location')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_approved_budget', 15, 2);
            $table->enum('status', ['active', 'completed', 'suspended', 'terminated'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
