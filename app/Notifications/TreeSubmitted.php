<?php

namespace App\Notifications;

use App\Models\Tree;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Sent to every admin / super-admin when a volunteer submits a new tree, so
 * moderators know there is something waiting for review. Stored on the database
 * channel and surfaced in the mobile app's notification centre.
 */
class TreeSubmitted extends Notification
{
    use Queueable;

    public function __construct(public Tree $tree) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // 'database' backs the in-app notification centre; 'expo' pushes the same
        // payload to the user's registered devices. Both, so the record survives
        // even when the push cannot be delivered.
        return ['database', 'expo'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $planter = $this->tree->user?->name ?? __('A volunteer');

        return [
            'type' => 'tree_submitted',
            'title' => __('New tree awaiting review'),
            'body' => __(':planter submitted ":species" for review.', [
                'planter' => $planter,
                'species' => $this->tree->species,
            ]),
            'tree_id' => $this->tree->id,
            'species' => $this->tree->species,
            'planter_name' => $planter,
        ];
    }
}
