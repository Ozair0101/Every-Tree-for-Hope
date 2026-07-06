<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_sponsor_package" permissions
 * via {@see BasePolicy}.
 */
class SponsorPackagePolicy extends BasePolicy
{
    protected string $prefix = 'sponsor_package';
}
