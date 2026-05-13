<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SetupAccomplishment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'encoded_by',
        'year',
        'target_num_projects',
        'target_ifund_amount',
        'target_gross_sales',
        'target_employment',
        'target_trainings',
        'actual_num_projects',
        'actual_ifund_amount',
        'actual_gross_sales',
        'actual_employment',
        'actual_trainings',
        'verified_status',
        'verification_notes',
    ];

    protected function casts(): array
    {
        return [
            'year'                => 'integer',
            'target_ifund_amount' => 'decimal:2',
            'target_gross_sales'  => 'decimal:2',
            'actual_ifund_amount' => 'decimal:2',
            'actual_gross_sales'  => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function encoder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encoded_by');
    }

    public function verificationLogs(): MorphMany
    {
        return $this->morphMany(VerificationLog::class, 'loggable')->latest();
    }

    public function scopeVerified(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('verified_status', 'verified');
    }
}
