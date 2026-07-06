<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_company" permissions
 * via {@see BasePolicy}.
 */
class CompanyPolicy extends BasePolicy
{
    protected string $prefix = 'company';
}
