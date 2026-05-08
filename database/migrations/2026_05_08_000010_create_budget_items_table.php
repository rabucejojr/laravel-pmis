<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('budget_items')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->enum('item_type', ['group', 'item'])->default('item');
            $table->string('label');
            $table->decimal('quantity',  12, 4)->nullable();
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->string('unit_label', 50)->nullable();
            $table->decimal('amount',    15, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'parent_id', 'sort_order'], 'budget_items_tree_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
