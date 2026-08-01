<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\TaskAssignmentRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'country',
        'address',
        'password',
        'profile_image',
        'cover_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine whether the user may access the given Filament panel.
     *
     * Access is now purely role-based: a user may enter the admin panel only
     * if they hold at least one role. The Super Admin bypass in
     * {@see \App\Providers\AuthServiceProvider} does not apply here — panel
     * entry is an explicit gate, not a Gate ability.
     */
    /**
     * The role every self-registered account receives.
     *
     * Holding it is what makes "normal user" a queryable fact rather than the
     * absence of one — an admin picking people to assign a task to can filter
     * for it, and an account that lost its staff role by mistake is no longer
     * indistinguishable from a member of the public.
     */
    public const VOLUNTEER_ROLE = 'Volunteer';

    /**
     * Determine whether the user may enter the Filament panel.
     *
     * Any role EXCEPT Volunteer. Before the volunteer role existed this checked
     * only that some role was held, which was correct then — registered users
     * had none. Introducing the role without this change would have handed
     * every newly registered member of the public an admin login, so the two
     * belong together.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles()->where('name', '!=', self::VOLUNTEER_ROLE)->exists();
    }

    /** A self-registered member of the public, with no staff role. */
    public function isVolunteer(): bool
    {
        return $this->hasRole(self::VOLUNTEER_ROLE) && $this->roles()->count() === 1;
    }

    /**
     * Convenience helper: is this user the all-powerful Super Admin?
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(\App\Providers\AuthServiceProvider::SUPER_ADMIN);
    }

    /**
     * Trees this user has planted and recorded (any moderation status).
     */
    public function trees(): HasMany
    {
        return $this->hasMany(Tree::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Task management
    |--------------------------------------------------------------------------
    */

    /** Every task this user is attached to, in any role. */
    public function taskAssignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    /**
     * Tasks assigned to this user — the "My Tasks" screen.
     *
     * Goes through the assignment pivot rather than a belongsToMany so the
     * assignment's own status and timestamps stay reachable without
     * `withPivot()` on every call site.
     */
    public function assignedTasks(): HasManyThrough
    {
        return $this->hasManyThrough(
            Task::class,
            TaskAssignment::class,
            'user_id',   // FK on task_assignments
            'id',        // PK on tasks
            'id',        // PK on users
            'task_id',   // FK on task_assignments pointing at tasks
        )->where('task_assignments.role', TaskAssignmentRole::ASSIGNEE->value);
    }

    /** Tasks this user created (admin/staff side). */
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function taskSubmissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class);
    }

    /** Registered devices that may receive a push. */
    public function pushTokens(): HasMany
    {
        return $this->hasMany(PushToken::class);
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    /**
     * The task module's in-app inbox.
     *
     * Deliberately NOT named `notifications()` — that name belongs to Laravel's
     * Notifiable trait and is already used by the shipped mobile endpoints for
     * tree and voice moderation. Overriding it would break them.
     */
    public function taskNotifications(): HasMany
    {
        return $this->hasMany(TaskNotification::class)->latest();
    }

    public function unreadTaskNotifications(): HasMany
    {
        return $this->taskNotifications()->where('is_read', false);
    }

    /** Progress reports this user filed from the field. */
    public function taskProgressUpdates(): HasMany
    {
        return $this->hasMany(TaskProgress::class, 'created_by');
    }

    /** Reviews this user carried out (admin/staff side). */
    public function taskReviews(): HasMany
    {
        return $this->hasMany(TaskReview::class, 'reviewed_by');
    }

    /** Everything this user did across the task module. */
    public function taskActivities(): HasMany
    {
        return $this->hasMany(TaskActivityLog::class)->latest();
    }
}
