<?php

namespace App\Policies;

/**
 * Full-CRUD policy. All abilities map to "{ability}_voice" permissions
 * via {@see BasePolicy}.
 */
class VoicePolicy extends BasePolicy
{
    protected string $prefix = 'voice';
}
