<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_donator" permissions
 * via {@see BasePolicy}.
 */
class DonatorPolicy extends BasePolicy
{
    protected string $prefix = 'donator';
}
