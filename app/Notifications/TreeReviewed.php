<?php

namespace App\Notifications;

use App\Models\Tree;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Sent to the planter when an admin approves or rejects their tree, so they
 * learn the outcome inside the app. `approved` and `rejected` share this class
 * because the recipient and shape are identical — only the wording differs.
 */
class TreeReviewed extends Notification
{
    use Queueable;

    /**
     * @param  'approved'|'rejected'  $outcome
     */
    public function __construct(public Tree $tree, public string $outcome)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $approved = $this->outcome === 'approved';

        return [
            'type' => $approved ? 'tree_approved' : 'tree_rejected',
            'title' => $approved
                ? __('Your tree was approved')
                : __('Your tree was not approved'),
            'body' => $approved
                ? __('":species" is now live on your profile and the public map.', ['species' => $this->tree->species])
                : __('":species" was not approved.', ['species' => $this->tree->species])
                    . ($this->tree->rejection_reason ? ' ' . $this->tree->rejection_reason : ''),
            'tree_id' => $this->tree->id,
            'species' => $this->tree->species,
        ];
    }
}
