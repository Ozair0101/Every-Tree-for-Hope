<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_event" permissions
 * via {@see BasePolicy}.
 */
class EventPolicy extends BasePolicy
{
    protected string $prefix = 'event';
}
