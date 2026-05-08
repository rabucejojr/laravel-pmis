<?php

namespace App\Services;

use App\Models\BudgetItem;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class BudgetItemService
{
    public static function make(): self
    {
        return app(self::class);
    }

    // ── Public API ───────────────────────────────────────────────────────────

    /**
     * Sync the full budget tree from the Alpine.js payload to the DB.
     *
     * Each node shape expected:
     * {
     *   db_id      : int|null,
     *   item_type  : 'group'|'item',
     *   label      : string,
     *   quantity   : float|null,
     *   unit_cost  : float|null,
     *   unit_label : string|null,
     *   amount     : float|null,
     *   remarks    : string|null,
     *   children   : []
     * }
     */
    public function syncTree(Project $project, array $tree): void
    {
        DB::transaction(function () use ($project, $tree) {
            $survivingIds = [];
            $this->collectDbIds($tree, $survivingIds);

            // Delete rows removed in the UI (cascade handles their children)
            BudgetItem::where('project_id', $project->id)
                ->when(
                    ! empty($survivingIds),
                    fn ($q) => $q->whereNotIn('id', $survivingIds)
                )
                ->delete();

            $this->upsertNodes($project->id, $tree, null, 0);
        });
    }

    /**
     * Return the project's budget tree as a nested array for Alpine.js initialisation.
     * Each node carries a `computed_amount` key (PHP-computed, for initial display).
     */
    public function toJsonTree(Project $project): array
    {
        $roots = BudgetItem::toTree($project->id);
        return $this->nodesToArray($roots);
    }

    /**
     * Recursive BCMath grand total over an Alpine-format tree array.
     * Used server-side after save to produce the verified summary.
     */
    public function computeTotal(array $items): string
    {
        $total = '0.00';
        foreach ($items as $node) {
            if (($node['item_type'] ?? 'item') === 'group') {
                $total = bcadd($total, $this->computeTotal($node['children'] ?? []), 2);
            } else {
                $qty  = isset($node['quantity'])  && $node['quantity']  !== null ? (string) $node['quantity']  : null;
                $cost = isset($node['unit_cost']) && $node['unit_cost'] !== null ? (string) $node['unit_cost'] : null;

                if ($qty !== null && $cost !== null) {
                    $total = bcadd($total, bcmul($qty, $cost, 2), 2);
                } else {
                    $total = bcadd($total, (string) ($node['amount'] ?? '0'), 2);
                }
            }
        }
        return $total;
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function collectDbIds(array $nodes, array &$ids): void
    {
        foreach ($nodes as $node) {
            if (! empty($node['db_id'])) {
                $ids[] = (int) $node['db_id'];
            }
            if (! empty($node['children'])) {
                $this->collectDbIds($node['children'], $ids);
            }
        }
    }

    private function upsertNodes(int $projectId, array $nodes, ?int $parentDbId, int $sortStart): void
    {
        foreach ($nodes as $index => $node) {
            $attrs = [
                'project_id' => $projectId,
                'parent_id'  => $parentDbId,
                'sort_order' => $sortStart + $index,
                'item_type'  => $node['item_type'] ?? 'item',
                'label'      => $node['label'] ?? '',
                'quantity'   => isset($node['quantity'])  && $node['quantity']  !== '' ? $node['quantity']  : null,
                'unit_cost'  => isset($node['unit_cost']) && $node['unit_cost'] !== '' ? $node['unit_cost'] : null,
                'unit_label' => isset($node['unit_label']) && $node['unit_label'] !== '' ? $node['unit_label'] : null,
                'amount'     => isset($node['amount'])   && $node['amount']   !== '' ? $node['amount']   : null,
                'remarks'    => isset($node['remarks'])  && $node['remarks']  !== '' ? $node['remarks']  : null,
            ];

            if (! empty($node['db_id'])) {
                BudgetItem::where('id', (int) $node['db_id'])
                           ->where('project_id', $projectId)
                           ->update($attrs);
                $savedId = (int) $node['db_id'];
            } else {
                $savedId = BudgetItem::create($attrs)->id;
            }

            if (! empty($node['children'])) {
                $this->upsertNodes($projectId, $node['children'], $savedId, 0);
            }
        }
    }

    private function nodesToArray($nodes): array
    {
        $out = [];
        foreach ($nodes as $item) {
            $out[] = [
                'db_id'           => $item->id,
                'item_type'       => $item->item_type,
                'label'           => $item->label,
                'quantity'        => $item->quantity,
                'unit_cost'       => $item->unit_cost,
                'unit_label'      => $item->unit_label ?? '',
                'amount'          => $item->amount,
                'remarks'         => $item->remarks ?? '',
                'computed_amount' => $item->computedAmount(),
                'children'        => $this->nodesToArray($item->children ?? []),
            ];
        }
        return $out;
    }
}
