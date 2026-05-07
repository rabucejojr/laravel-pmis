<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('loggable');
            $table->foreignId('verifier_id')->constrained('users')->restrictOnDelete();
            $table->enum('action', ['verified', 'flagged', 'rejected']);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['loggable_type', 'loggable_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_logs');
    }
};
