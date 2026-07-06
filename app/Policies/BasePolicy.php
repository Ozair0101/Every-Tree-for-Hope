<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

/**
 * Convention-based policy.
 *
 * Every concrete policy declares a single {@see $prefix} (the snake_case,
 * singular model key, e.g. "user", "donator"). Each ability method then maps
 * to a Spatie permission named "{ability}_{prefix}" — e.g. `view_any_user`,
 * `delete_donator`, `force_delete_expense`.
 *
 * This keeps policies to ~1 line each and lets one seeder generate every
 * permission from the same convention, so adding a new module is a two-step
 * change (new policy + register it) that scales to hundreds of modules.
 *
 * Filament v5 calls these methods automatically for navigation visibility,
 * resource page access, and Create/View/Edit/Delete/Replicate/bulk actions,
 * so the policy is the *single source of truth* — we never duplicate these
 * checks inside the resources themselves.
 */
abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Snake_case singular model key used to build permission names.
     */
    protected string $prefix;

    protected function allows(User $user, string $ability): bool
    {
        return $user->can("{$ability}_{$this->prefix}");
    }

    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'view_any');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->allows($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->allows($user, 'create');
    }

    public function update(User $user, Model $model): bool
    {
        return $this->allows($user, 'update');
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->allows($user, 'delete');
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, 'delete_any');
    }

    public function restore(User $user, Model $model): bool
    {
        return $this->allows($user, 'restore');
    }

    public function restoreAny(User $user): bool
    {
        return $this->allows($user, 'restore_any');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $this->allows($user, 'force_delete');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->allows($user, 'force_delete_any');
    }

    public function replicate(User $user, Model $model): bool
    {
        return $this->allows($user, 'replicate');
    }

    public function reorder(User $user): bool
    {
        return $this->allows($user, 'reorder');
    }
}
