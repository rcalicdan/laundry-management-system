<?php

namespace App\Policies;

use App\Models\LaundryService;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LaundryServicePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->id == $model->id) {
            return false;
        }

        return $user->isAdmin();
    }
}
