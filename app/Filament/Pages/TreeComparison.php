<?php

namespace App\Filament\Pages;

use App\Models\Tree;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

/**
 * Before and after, side by side.
 *
 * The whole point of a tree-planting programme is the change over time, and
 * until now nothing in the panel showed it: the planting photo lived on the
 * tree record and the follow-up sat beside it in a column nobody rendered.
 *
 * Deliberately its own page rather than a column on TreeResource. A comparison
 * is looked at, not filtered and sorted — squeezing two photographs into a
 * table row produces thumbnails too small to judge anything by, which is the
 * one thing this screen exists to make possible.
 */
class TreeComparison extends Page
{
    use WithPagination;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Growth Comparison';

    protected static string|\UnitEnum|null $navigationGroup = 'Community';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Before & After';

    protected string $view = 'filament.pages.tree-comparison';

    /** 'compared' | 'awaiting' — which half of the programme to look at. */
    public string $filter = 'compared';

    public string $search = '';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_any_tree') ?? false;
    }

    /** Reset to page one when the filter changes, or page 3 of the old set shows empty. */
    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function trees(): LengthAwarePaginator
    {
        return Tree::query()
            ->with('user:id,name,lastname')
            ->when($this->filter === 'compared', fn (Builder $q) => $q->withComparison())
            ->when($this->filter === 'awaiting', fn (Builder $q) => $q->awaitingAfterImage())
            ->when(trim($this->search) !== '', function (Builder $q) {
                $term = str_replace(['%', '_'], ['\%', '\_'], trim($this->search));

                $q->where(fn (Builder $inner) => $inner
                    ->where('species', 'like', "%{$term}%")
                    ->orWhere('location_name', 'like', "%{$term}%")
                    ->orWhereHas('user', fn (Builder $u) => $u->where('name', 'like', "%{$term}%")));
            })
            // Newest follow-up first when comparing; oldest planting first when
            // chasing — the second list is a to-do, and the longest-waiting
            // trees are the ones worth prompting about.
            ->when(
                $this->filter === 'compared',
                fn (Builder $q) => $q->orderByDesc('after_image_taken_at'),
                fn (Builder $q) => $q->orderBy('planted_on'),
            )
            // Six per page: two rows of three on a desktop panel, and each row
            // is two full photographs, so more would be a slow page.
            ->paginate(6);
    }

    /** Headline counts for the tabs. */
    public function counts(): array
    {
        return [
            'compared' => Tree::query()->withComparison()->count(),
            'awaiting' => Tree::query()->awaitingAfterImage()->count(),
        ];
    }

    /**
     * How far the follow-up was shot from the tree, in words.
     *
     * Null when either reading is missing — most trees recorded before phase 8
     * have no follow-up coordinates at all, and rendering "0m away" for them
     * would be a confident lie.
     */
    public function distanceLabel(Tree $tree): ?string
    {
        if ($tree->after_image_distance === null) {
            return null;
        }

        return $tree->after_image_distance >= 1000
            ? sprintf('%.1f km from the tree', $tree->after_image_distance / 1000)
            : sprintf('%dm from the tree', $tree->after_image_distance);
    }

    /**
     * Whether the follow-up looks like it was taken somewhere else.
     *
     * 100m is generous — a photographer steps back to frame a grown tree, and
     * GPS drifts under a canopy. Beyond that it is worth a second look.
     */
    public function looksDisplaced(Tree $tree): bool
    {
        return $tree->after_image_distance !== null && $tree->after_image_distance > 100;
    }
}
