<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: expand enum to include new values alongside old ones
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('active','completed','suspended','terminated','liquidated','unliquidated') NOT NULL DEFAULT 'active'");

        // Step 2: remap old values to new
        DB::table('projects')->where('status', 'completed')->update(['status' => 'liquidated']);
        DB::table('projects')->whereIn('status', ['suspended', 'terminated'])->update(['status' => 'unliquidated']);

        // Step 3: drop old values from enum
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('active','liquidated','unliquidated') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('active','completed','suspended','terminated') NOT NULL DEFAULT 'active'");

        DB::table('projects')->where('status', 'liquidated')->update(['status' => 'completed']);
        DB::table('projects')->where('status', 'unliquidated')->update(['status' => 'suspended']);
    }
};
