<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_involvement_request" permissions
 * via {@see BasePolicy}.
 */
class InvolvementRequestPolicy extends BasePolicy
{
    protected string $prefix = 'involvement_request';
}
