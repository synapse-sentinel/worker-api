<?php

namespace App\Policies;

use App\Models\AiModel;
use App\Models\User;

class AiModelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): true
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AiModel $aiModel): true
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): true
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AiModel $aiModel): true
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AiModel $aiModel): true
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AiModel $aiModel): true
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AiModel $aiModel): true
    {
        return true;
    }
}
