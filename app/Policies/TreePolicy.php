<?php

namespace App\Policies;

/**
 * Full-CRUD policy for user-planted trees. Abilities map to "{ability}_tree"
 * permissions via {@see BasePolicy}; `approve_tree` / `reject_tree` are the two
 * custom moderation abilities, checked directly by the Filament resource.
 */
class TreePolicy extends BasePolicy
{
    protected string $prefix = 'tree';
}
