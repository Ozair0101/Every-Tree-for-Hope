<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_role" permissions
 * via {@see BasePolicy}.
 */
class RolePolicy extends BasePolicy
{
    protected string $prefix = 'role';
}
