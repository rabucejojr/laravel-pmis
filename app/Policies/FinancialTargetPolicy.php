<?php

namespace App\Policies;

use App\Models\FinancialTarget;
use App\Models\User;

class FinancialTargetPolicy
{
    public function delete(User $user, FinancialTarget $target): bool
    {
        return $user->isAdmin();
    }
}
