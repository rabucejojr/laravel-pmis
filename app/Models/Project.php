<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'program_id',
        'title',
        'description',
        'implementing_agency',
        'co_implementing_agency',
        'location',
        'start_date',
        'end_date',
        'total_approved_budget',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date'            => 'date',
            'end_date'              => 'date',
            'total_approved_budget' => 'decimal:2',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function financialTargets(): HasMany
    {
        return $this->hasMany(FinancialTarget::class);
    }

    public function physicalAccomplishments(): HasMany
    {
        return $this->hasMany(PhysicalAccomplishment::class);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class)->orderBy('sort_order');
    }

    public function setupAccomplishments(): HasMany
    {
        return $this->hasMany(SetupAccomplishment::class);
    }
}
