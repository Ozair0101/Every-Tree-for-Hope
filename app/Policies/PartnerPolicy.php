<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_partner" permissions
 * via {@see BasePolicy}.
 */
class PartnerPolicy extends BasePolicy
{
    protected string $prefix = 'partner';
}
