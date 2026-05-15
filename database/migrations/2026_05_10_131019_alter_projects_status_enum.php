<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('active','completed','suspended','terminated','liquidated','unliquidated') NOT NULL DEFAULT 'active'");
            DB::table('projects')->where('status', 'completed')->update(['status' => 'liquidated']);
            DB::table('projects')->whereIn('status', ['suspended', 'terminated'])->update(['status' => 'unliquidated']);
            DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('active','liquidated','unliquidated') NOT NULL DEFAULT 'active'");
        } else {
            // PostgreSQL: Laravel stores enum as VARCHAR + CHECK constraint
            DB::statement('ALTER TABLE projects DROP CONSTRAINT IF EXISTS projects_status_check');
            DB::table('projects')->where('status', 'completed')->update(['status' => 'liquidated']);
            DB::table('projects')->whereIn('status', ['suspended', 'terminated'])->update(['status' => 'unliquidated']);
            DB::statement("ALTER TABLE projects ADD CONSTRAINT projects_status_check CHECK (status IN ('active', 'liquidated', 'unliquidated'))");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('active','completed','suspended','terminated') NOT NULL DEFAULT 'active'");
            DB::table('projects')->where('status', 'liquidated')->update(['status' => 'completed']);
            DB::table('projects')->where('status', 'unliquidated')->update(['status' => 'suspended']);
        } else {
            DB::statement('ALTER TABLE projects DROP CONSTRAINT IF EXISTS projects_status_check');
            DB::table('projects')->where('status', 'liquidated')->update(['status' => 'completed']);
            DB::table('projects')->where('status', 'unliquidated')->update(['status' => 'suspended']);
            DB::statement("ALTER TABLE projects ADD CONSTRAINT projects_status_check CHECK (status IN ('active', 'completed', 'suspended', 'terminated'))");
        }
    }
};
