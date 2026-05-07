<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PhysicalAccomplishment extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'encoded_by',
        'year',
        'quarter',
        'month',
        'indicator_name',
        'target_value',
        'accomplished_value',
        // accomplishment_rate is a MySQL generated column — never in fillable
        'verified_status',
        'verification_notes',
    ];

    protected function casts(): array
    {
        return [
            'year'                => 'integer',
            'quarter'             => 'integer',
            'month'               => 'integer',
            'target_value'        => 'decimal:2',
            'accomplished_value'  => 'decimal:2',
            'accomplishment_rate' => 'decimal:2',
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

    public function scopeVerified(Builder $query)
    {
        return $query->where('verified_status', 'verified');
    }
}
