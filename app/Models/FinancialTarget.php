<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class FinancialTarget extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'encoded_by',
        'year',
        'quarter',
        'month',
        'line_item',
        'target_amount',
        'obligated_amount',
        'disbursed_amount',
        'verified_status',
        'verification_notes',
    ];

    protected function casts(): array
    {
        return [
            'year'              => 'integer',
            'quarter'           => 'integer',
            'month'             => 'integer',
            'target_amount'     => 'decimal:2',
            'obligated_amount'  => 'decimal:2',
            'disbursed_amount'  => 'decimal:2',
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

    public function scopeVerified(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('verified_status', 'verified');
    }
}
