<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_faq" permissions
 * via {@see BasePolicy}.
 */
class FaqPolicy extends BasePolicy
{
    protected string $prefix = 'faq';
}
