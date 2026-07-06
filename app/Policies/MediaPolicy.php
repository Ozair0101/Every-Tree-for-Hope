<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_media" permissions
 * via {@see BasePolicy}.
 */
class MediaPolicy extends BasePolicy
{
    protected string $prefix = 'media';
}
