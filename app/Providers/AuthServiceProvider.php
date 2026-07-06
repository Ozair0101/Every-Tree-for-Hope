<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\ContactMessage;
use App\Models\Donator;
use App\Models\Event;
use App\Models\Expense;
use App\Models\Faq;
use App\Models\InvolvementRequest;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobPosting;
use App\Models\Media;
use App\Models\Partner;
use App\Models\SponsorPackage;
use App\Models\Team;
use App\Models\TreeRequest;
use App\Models\UpcomingEvent;
use App\Models\User;
use App\Models\Voice;
use App\Models\VoiceComment;
use App\Policies\CompanyPolicy;
use App\Policies\ContactMessagePolicy;
use App\Policies\DonatorPolicy;
use App\Policies\EventPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\FaqPolicy;
use App\Policies\InvolvementRequestPolicy;
use App\Policies\JobApplicationPolicy;
use App\Policies\JobCategoryPolicy;
use App\Policies\JobPostingPolicy;
use App\Policies\MediaPolicy;
use App\Policies\PartnerPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\SponsorPackagePolicy;
use App\Policies\TeamPolicy;
use App\Policies\TreeRequestPolicy;
use App\Policies\UpcomingEventPolicy;
use App\Policies\UserPolicy;
use App\Policies\VoiceCommentPolicy;
use App\Policies\VoicePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The name of the role that bypasses every authorization check.
     */
    public const SUPER_ADMIN = 'Super Admin';

    /**
     * Explicit model → policy map.
     *
     * We register policies explicitly rather than relying on convention-based
     * auto-discovery because (a) it is unambiguous and greppable, and (b) the
     * Role/Permission models live in the Spatie namespace where auto-discovery
     * would never find our policies.
     *
     * @var array<class-string, class-string>
     */
    private array $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Company::class => CompanyPolicy::class,
        ContactMessage::class => ContactMessagePolicy::class,
        Donator::class => DonatorPolicy::class,
        Event::class => EventPolicy::class,
        Expense::class => ExpensePolicy::class,
        Faq::class => FaqPolicy::class,
        InvolvementRequest::class => InvolvementRequestPolicy::class,
        JobApplication::class => JobApplicationPolicy::class,
        JobCategory::class => JobCategoryPolicy::class,
        JobPosting::class => JobPostingPolicy::class,
        Media::class => MediaPolicy::class,
        Partner::class => PartnerPolicy::class,
        SponsorPackage::class => SponsorPackagePolicy::class,
        Team::class => TeamPolicy::class,
        TreeRequest::class => TreeRequestPolicy::class,
        UpcomingEvent::class => UpcomingEventPolicy::class,
        Voice::class => VoicePolicy::class,
        VoiceComment::class => VoiceCommentPolicy::class,
    ];

    public function boot(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        /*
         | ---------------------------------------------------------------------
         | Super Admin bypass
         | ---------------------------------------------------------------------
         | Gate::before() runs *before* any policy or ability check. Returning
         | `true` short-circuits every gate/policy in the app (including all
         | Filament resource, page, action, widget and navigation checks) so the
         | Super Admin never needs an explicit permission. Returning `null`
         | (not false) lets the normal policy pipeline continue for everyone
         | else — returning false here would DENY everything for non-super-admins.
         |
         | Security note: this is a total bypass. Grant "Super Admin" only to
         | trusted operators; it ignores every least-privilege boundary below it.
        */
        Gate::before(function ($user, string $ability): ?bool {
            return $user->hasRole(self::SUPER_ADMIN) ? true : null;
        });
    }
}
