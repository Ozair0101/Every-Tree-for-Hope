<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_team" permissions
 * via {@see BasePolicy}.
 */
class TeamPolicy extends BasePolicy
{
    protected string $prefix = 'team';
}
