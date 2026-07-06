<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_expense" permissions
 * via {@see BasePolicy}.
 */
class ExpensePolicy extends BasePolicy
{
    protected string $prefix = 'expense';
}
