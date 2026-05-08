<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetItem extends Model
{
    protected $fillable = [
        'project_id',
        'parent_id',
        'sort_order',
        'item_type',
        'label',
        'quantity',
        'unit_cost',
        'unit_label',
        'amount',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'quantity'   => 'decimal:4',
            'unit_cost'  => 'decimal:2',
            'amount'     => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    // ── Computed Amount ──────────────────────────────────────────────────────

    /**
     * Recursive BCMath computed amount.
     * group → sum of children's computedAmount()
     * item  → quantity × unit_cost if both set, else amount override
     *
     * Children must already be eager-loaded (via toTree) before calling this.
     */
    public function computedAmount(): string
    {
        if ($this->item_type === 'group') {
            $total = '0.00';
            foreach ($this->children as $child) {
                $total = bcadd($total, $child->computedAmount(), 2);
            }
            return $total;
        }

        if ($this->quantity !== null && $this->unit_cost !== null) {
            return bcmul((string) $this->quantity, (string) $this->unit_cost, 2);
        }

        return (string) ($this->amount ?? '0.00');
    }

    // ── Tree Loading ─────────────────────────────────────────────────────────

    /**
     * Load the full flat list for a project and nest it in memory in O(n).
     * Avoids recursive eager-loading for arbitrary-depth trees.
     */
    public static function toTree(int $projectId): Collection
    {
        $all = self::where('project_id', $projectId)
                   ->orderBy('sort_order')
                   ->get()
                   ->keyBy('id');

        $roots = new Collection();

        foreach ($all as $item) {
            // Pre-init children relation so computedAmount() can iterate safely
            if (! $item->relationLoaded('children')) {
                $item->setRelation('children', new Collection());
            }
        }

        foreach ($all as $item) {
            if ($item->parent_id === null) {
                $roots->push($item);
            } else {
                $parent = $all->get($item->parent_id);
                if ($parent) {
                    $parent->children->push($item);
                }
            }
        }

        return $roots;
    }
}
