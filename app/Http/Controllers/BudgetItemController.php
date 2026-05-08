<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class BudgetItemController extends Controller
{
    public function show(Project $project): View
    {
        $project->load('program');

        return view('budget-items.show', compact('project'));
    }
}
