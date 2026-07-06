<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_job_category" permissions
 * via {@see BasePolicy}.
 */
class JobCategoryPolicy extends BasePolicy
{
    protected string $prefix = 'job_category';
}
