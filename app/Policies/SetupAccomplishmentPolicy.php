<?php

namespace App\Policies;

use App\Models\SetupAccomplishment;
use App\Models\User;

class SetupAccomplishmentPolicy
{
    public function delete(User $user, SetupAccomplishment $sa): bool
    {
        return $user->isAdmin();
    }
}
