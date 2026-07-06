<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_user" permissions
 * via {@see BasePolicy}.
 */
class UserPolicy extends BasePolicy
{
    protected string $prefix = 'user';
}
