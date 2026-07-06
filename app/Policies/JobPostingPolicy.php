<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_job_posting" permissions
 * via {@see BasePolicy}.
 */
class JobPostingPolicy extends BasePolicy
{
    protected string $prefix = 'job_posting';
}
