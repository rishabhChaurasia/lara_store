<?php

namespace App\Policies;

use App\Models\User;

class CartPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only allow admins to view abandoned cart statistics
        return $user->role === 'admin';
    }
}
