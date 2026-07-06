<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Contact messages are inbound-only: they are received from the public site,
 * never created or edited inside the panel — staff can only read and delete.
 */
class ContactMessagePolicy extends BasePolicy
{
    protected string $prefix = 'contact_message';

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Model $model): bool
    {
        return false;
    }

    public function restore(User $user, Model $model): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, Model $model): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
