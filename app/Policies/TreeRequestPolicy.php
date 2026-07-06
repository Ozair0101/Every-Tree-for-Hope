<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_tree_request" permissions
 * via {@see BasePolicy}.
 */
class TreeRequestPolicy extends BasePolicy
{
    protected string $prefix = 'tree_request';
}
