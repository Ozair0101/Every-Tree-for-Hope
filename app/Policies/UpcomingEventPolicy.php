<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_upcoming_event" permissions
 * via {@see BasePolicy}.
 */
class UpcomingEventPolicy extends BasePolicy
{
    protected string $prefix = 'upcoming_event';
}
