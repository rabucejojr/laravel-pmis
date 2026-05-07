<?php

namespace App\Policies;

use App\Models\PhysicalAccomplishment;
use App\Models\User;

class PhysicalAccomplishmentPolicy
{
    public function delete(User $user, PhysicalAccomplishment $pa): bool
    {
        return $user->isAdmin();
    }
}
