<?php

namespace App\Livewire;

use App\Models\Project;
use App\Services\BudgetItemService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\View\View;
use Livewire\Component;

class BudgetItemEditor extends Component
{
    public Project $project;

    /** PHP-computed grand total shown after the last save (or on mount). */
    public string $grandTotal = '0.00';

    /** Amber warning when budget breakdown total ≠ total_approved_budget. */
    public string $budgetWarning = '';

    public function mount(BudgetItemService $service): void
    {
        $this->grandTotal = $service->computeTotal($service->toJsonTree($this->project));
        $this->syncWarning();
    }

    /**
     * Called by Alpine.js via $wire.saveTree(items).
     * Livewire v3 JSON-decodes the array argument automatically.
     */
    public function saveTree(array $tree, BudgetItemService $service): void
    {
        if (! auth()->user()->hasRole(['admin', 'encoder'])) {
            throw new AuthorizationException('Only admins and encoders can edit the budget breakdown.');
        }

        $this->validateTree($tree);

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        $service->syncTree($this->project, $tree);

        $this->grandTotal = $service->computeTotal($tree);
        $this->syncWarning();

        session()->flash('success', 'Budget breakdown saved successfully.');
        $this->dispatch('budget-saved');
    }

    public function render(): View
    {
        return view('livewire.budget-item-editor');
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function validateTree(array $nodes, int $depth = 0): void
    {
        if ($depth > 5) {
            session()->flash('warning', 'Budget tree exceeds the recommended nesting depth of 5 levels. Consider flattening.');
        }

        foreach ($nodes as $node) {
            if (empty(trim((string) ($node['label'] ?? '')))) {
                $this->addError('tree', 'All budget items must have a label.');
                return;
            }

            if (($node['item_type'] ?? 'item') === 'item') {
                if (isset($node['quantity']) && $node['quantity'] !== null && (float) $node['quantity'] < 0) {
                    $this->addError('tree', "Quantity for \"{$node['label']}\" cannot be negative.");
                    return;
                }
                if (isset($node['unit_cost']) && $node['unit_cost'] !== null && (float) $node['unit_cost'] < 0) {
                    $this->addError('tree', "Unit cost for \"{$node['label']}\" cannot be negative.");
                    return;
                }
                if (isset($node['amount']) && $node['amount'] !== null && (float) $node['amount'] < 0) {
                    $this->addError('tree', "Amount for \"{$node['label']}\" cannot be negative.");
                    return;
                }
            }

            if (! empty($node['children'])) {
                $this->validateTree($node['children'], $depth + 1);
                if ($this->getErrorBag()->isNotEmpty()) {
                    return;
                }
            }
        }
    }

    private function syncWarning(): void
    {
        $approved = (float) $this->project->total_approved_budget;
        $computed = (float) $this->grandTotal;

        if ($approved > 0 && abs($approved - $computed) > 0.01) {
            $diff = number_format(abs($approved - $computed), 2);
            $dir  = $computed > $approved ? 'exceeds' : 'is below';
            $this->budgetWarning = sprintf(
                'Budget breakdown total (₱%s) %s the GAA-approved budget (₱%s) by ₱%s.',
                number_format($computed, 2),
                $dir,
                number_format($approved, 2),
                $diff
            );
        } else {
            $this->budgetWarning = '';
        }
    }
}
