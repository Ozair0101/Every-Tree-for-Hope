{{--
    The review workspace.

    Two columns on desktop: evidence on the left, context on the right. The
    reviewer reads left and decides from the header actions without scrolling
    away from the photographs — putting the verdict below a long page is how
    reviews get rubber stamped.
--}}
@php
    $submission = $this->record;
    $task = $submission->task;
    $assignment = $submission->assignment;
    $volunteer = $submission->user;

    $images = $this->attachmentsOfKind('image');
    $videos = $this->attachmentsOfKind('video');
    $documents = $this->attachmentsOfKind('document');
    $audio = $this->attachmentsOfKind('audio');
@endphp

<x-filament-panels::page>

    {{-- ── Verdict already given ────────────────────────────────────── --}}
    @if ($submission->status->value !== 'pending')
        @php($latest = $submission->reviews->first())
        <div @class([
            'rounded-xl border p-4',
            'border-success-300 bg-success-50 dark:border-success-500/30 dark:bg-success-500/10' => $submission->status->value === 'approved',
            'border-danger-300 bg-danger-50 dark:border-danger-500/30 dark:bg-danger-500/10' => $submission->status->value === 'rejected',
            'border-warning-300 bg-warning-50 dark:border-warning-500/30 dark:bg-warning-500/10' => $submission->status->value === 'needs_revision',
        ])>
            <p class="font-semibold text-gray-950 dark:text-white">
                Already reviewed — {{ $submission->status->label() }}
            </p>
            @if ($latest?->comments)
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $latest->comments }}</p>
            @endif
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                {{ $latest?->reviewer?->name ?? 'A coordinator' }}
                @if ($latest?->reviewed_at) · {{ $latest->reviewed_at->format('d M Y, H:i') }} @endif
            </p>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- ══════════════ LEFT: the evidence ══════════════ --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- What they said --}}
            <x-filament::section heading="The submission" icon="heroicon-o-paper-airplane">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-full bg-gray-100 px-2 py-1 dark:bg-white/10">
                            Attempt {{ $submission->attempt }}
                        </span>
                        @if ($submission->hours_spent)
                            <span class="rounded-full bg-gray-100 px-2 py-1 dark:bg-white/10">
                                {{ rtrim(rtrim((string) $submission->hours_spent, '0'), '.') }} hours logged
                            </span>
                        @endif
                        <span class="text-gray-500 dark:text-gray-400">
                            Submitted {{ $submission->created_at?->diffForHumans() }}
                        </span>
                    </div>

                    <p class="whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">
                        {{ $submission->note ?: 'No note was left with this submission.' }}
                    </p>

                    {{-- The device's own clock. A large gap from created_at means
                         the work was logged long after it happened — usually an
                         offline queue, occasionally something worth asking about. --}}
                    @if ($submission->device_captured_at && $submission->created_at
                        && $submission->device_captured_at->diffInHours($submission->created_at) >= 12)
                        <p class="text-xs text-warning-600 dark:text-warning-400">
                            Captured {{ $submission->device_captured_at->format('d M, H:i') }} but received
                            {{ $submission->created_at->format('d M, H:i') }} — likely sent from an offline queue.
                        </p>
                    @endif
                </div>
            </x-filament::section>

            {{-- Photos --}}
            @if ($images->isNotEmpty())
                <x-filament::section heading="Photos ({{ $images->count() }})" icon="heroicon-o-photo">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($images as $image)
                            {{-- Opens full size in a new tab: judging a planting
                                 from a 200px thumbnail is not a review. --}}
                            <a href="{{ $image->url }}" target="_blank" rel="noopener" class="group block">
                                <img
                                    src="{{ $image->url }}"
                                    alt="{{ $image->file_name }}"
                                    loading="lazy"
                                    class="aspect-square w-full rounded-lg object-cover ring-1 ring-gray-200 transition group-hover:opacity-90 dark:ring-white/10"
                                >
                                <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
                                    {{ $image->readable_size }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif

            {{-- Videos --}}
            @if ($videos->isNotEmpty())
                <x-filament::section heading="Videos ({{ $videos->count() }})" icon="heroicon-o-video-camera">
                    <div class="space-y-3">
                        @foreach ($videos as $video)
                            {{-- preload="metadata": the duration and first frame
                                 render, but a 60MB file is not pulled down until
                                 the reviewer actually presses play. --}}
                            <video
                                controls
                                preload="metadata"
                                class="w-full rounded-lg ring-1 ring-gray-200 dark:ring-white/10"
                            >
                                <source src="{{ $video->url }}" type="{{ $video->mime_type }}">
                            </video>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $video->file_name }} · {{ $video->readable_size }}
                                @if ($video->readable_duration) · {{ $video->readable_duration }} @endif
                            </p>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif

            {{-- Audio --}}
            @if ($audio->isNotEmpty())
                <x-filament::section heading="Audio ({{ $audio->count() }})" icon="heroicon-o-musical-note">
                    <div class="space-y-3">
                        @foreach ($audio as $clip)
                            <div>
                                <audio controls preload="metadata" class="w-full">
                                    <source src="{{ $clip->url }}" type="{{ $clip->mime_type }}">
                                </audio>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $clip->file_name }} · {{ $clip->readable_size }}
                                    @if ($clip->readable_duration) · {{ $clip->readable_duration }} @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif

            {{-- Documents --}}
            @if ($documents->isNotEmpty())
                <x-filament::section heading="Documents ({{ $documents->count() }})" icon="heroicon-o-document-text">
                    <ul class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach ($documents as $document)
                            <li class="flex items-center justify-between py-2">
                                <span class="truncate text-sm text-gray-700 dark:text-gray-300">
                                    {{ $document->file_name }}
                                </span>
                                <a
                                    href="{{ $document->url }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="shrink-0 text-sm font-medium text-primary-600 hover:underline dark:text-primary-400"
                                >
                                    Open ({{ $document->readable_size }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-filament::section>
            @endif

            @if ($submission->attachments->isEmpty())
                <x-filament::section heading="Attachments" icon="heroicon-o-paper-clip">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Nothing was attached to this submission.
                        @if ($task?->requires_photo)
                            <span class="text-danger-600 dark:text-danger-400">
                                This task requires a photo.
                            </span>
                        @endif
                    </p>
                </x-filament::section>
            @endif

            {{-- Progress history --}}
            @php($progress = $this->progressHistory())
            @if ($progress->isNotEmpty())
                <x-filament::section heading="Progress history" icon="heroicon-o-chart-bar" collapsible>
                    <ol class="space-y-3">
                        @foreach ($progress as $entry)
                            <li class="flex gap-3">
                                <span class="mt-0.5 w-12 shrink-0 text-sm font-semibold text-primary-600 dark:text-primary-400">
                                    {{ $entry->progress_percentage }}%
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $entry->note ?: '—' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $entry->creator?->name ?? 'Unknown' }} ·
                                        {{ $entry->created_at?->diffForHumans() }}
                                        @if ($entry->hasLocation())
                                            · {{ round((float) $entry->latitude, 5) }}, {{ round((float) $entry->longitude, 5) }}
                                        @endif
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </x-filament::section>
            @endif

            {{-- Earlier attempts --}}
            @php($earlier = $this->earlierAttempts())
            @if ($earlier->isNotEmpty())
                <x-filament::section heading="Earlier attempts" icon="heroicon-o-clock" collapsible collapsed>
                    <div class="space-y-4">
                        @foreach ($earlier as $attempt)
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-white/10">
                                <p class="text-sm font-medium text-gray-950 dark:text-white">
                                    Attempt {{ $attempt->attempt }} — {{ $attempt->status->label() }}
                                </p>
                                @if ($attempt->note)
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $attempt->note }}</p>
                                @endif
                                {{-- `review_status`, not `status`: a review's own
                                     verdict column. The submission has `status`,
                                     and mixing them up renders a fatal null. --}}
                                @foreach ($attempt->reviews as $verdict)
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                                        {{ $verdict->review_status->label() }} by
                                        {{ $verdict->reviewer?->name ?? 'a coordinator' }}
                                        @if ($verdict->comments) — “{{ $verdict->comments }}” @endif
                                    </p>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif

            {{-- Comments --}}
            @php($comments = $this->comments())
            <x-filament::section heading="Discussion ({{ $comments->count() }})" icon="heroicon-o-chat-bubble-left-right">
                @if ($comments->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Nothing has been said about this task yet.
                    </p>
                @else
                    <ul class="space-y-3">
                        @foreach ($comments as $comment)
                            <li @class([
                                'rounded-lg p-3',
                                'bg-gray-50 dark:bg-white/5' => ! $comment->is_internal,
                                // Internal notes are tinted so a reviewer can
                                // never mistake one for something the volunteer
                                // has read.
                                'bg-warning-50 ring-1 ring-warning-200 dark:bg-warning-500/10 dark:ring-warning-500/30' => $comment->is_internal,
                            ])>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $comment->body }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $comment->user?->name ?? 'Unknown' }} ·
                                    {{ $comment->created_at?->diffForHumans() }}
                                    @if ($comment->is_internal)
                                        <span class="font-medium text-warning-700 dark:text-warning-400">· staff only</span>
                                    @endif
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-filament::section>
        </div>

        {{-- ══════════════ RIGHT: the context ══════════════ --}}
        <div class="space-y-6">

            {{-- Task --}}
            <x-filament::section heading="Task" icon="heroicon-o-clipboard-document-list">
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Reference</dt>
                        <dd class="text-gray-950 dark:text-white">{{ $task?->reference }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Title</dt>
                        <dd class="text-gray-950 dark:text-white">{{ $task?->title }}</dd>
                    </div>
                    @if ($task?->category)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Category</dt>
                            <dd class="text-gray-950 dark:text-white">{{ $task->category->name }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Due</dt>
                        <dd @class([
                            'text-gray-950 dark:text-white',
                            'text-danger-600 dark:text-danger-400 font-medium' => $task?->is_overdue,
                        ])>
                            {{ $task?->due_date?->format('d M Y, H:i') ?? 'No deadline' }}
                            @if ($task?->is_overdue) (overdue) @endif
                        </dd>
                    </div>
                    @if ($task?->instructions)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Instructions</dt>
                            <dd class="whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $task->instructions }}</dd>
                        </div>
                    @endif
                </dl>

                @if ($task)
                    <x-slot name="footerActions">
                        <x-filament::link :href="route('filament.admin.resources.tasks.edit', ['record' => $task->id])">
                            Open task
                        </x-filament::link>
                    </x-slot>
                @endif
            </x-filament::section>

            {{-- Volunteer --}}
            <x-filament::section heading="Volunteer" icon="heroicon-o-user">
                <p class="text-sm font-medium text-gray-950 dark:text-white">
                    {{ trim(($volunteer?->name ?? '') . ' ' . ($volunteer?->lastname ?? '')) ?: 'Unknown' }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $volunteer?->email }}</p>
                @if ($assignment?->assigner)
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Assigned by {{ $assignment->assigner->name }}
                    </p>
                @endif
            </x-filament::section>

            {{-- GPS --}}
            <x-filament::section heading="Location" icon="heroicon-o-map-pin">
                @if ($submission->latitude === null)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        No GPS reading was sent with this submission.
                    </p>
                @else
                    @php($lat = (float) $submission->latitude)
                    @php($lng = (float) $submission->longitude)

                    <div @class([
                        'rounded-lg p-3 text-sm',
                        'bg-success-50 text-success-800 dark:bg-success-500/10 dark:text-success-300' => $submission->is_within_geofence === true,
                        'bg-danger-50 text-danger-800 dark:bg-danger-500/10 dark:text-danger-300' => $submission->is_within_geofence === false,
                        'bg-gray-50 text-gray-700 dark:bg-white/5 dark:text-gray-300' => $submission->is_within_geofence === null,
                    ])>
                        @if ($submission->is_within_geofence === true)
                            <strong>On site.</strong>
                            Recorded {{ $submission->distance_meters }}m from the task point.
                        @elseif ($submission->is_within_geofence === false)
                            <strong>Off site.</strong>
                            Recorded {{ number_format((int) $submission->distance_meters) }}m away —
                            beyond the {{ $task?->radius }}m radius.
                            {{-- Deliberately not framed as cheating: rural GPS
                                 drifts by tens of metres and the reviewer, not
                                 the system, decides what it means. --}}
                            <span class="block mt-1 text-xs opacity-80">
                                GPS can drift in valleys and under tree cover. Judge alongside the photos.
                            </span>
                        @else
                            This task has no geofence, so there was nothing to check against.
                        @endif
                    </div>

                    {{-- Fake-GPS findings. Advisory: every signal here has an
                         innocent explanation, so the page reports what was
                         checked and lets the reviewer judge. --}}
                    @if (filled($submission->verification))
                        <div class="mt-3 space-y-2">
                            @foreach ($submission->verification as $finding)
                                <div @class([
                                    'rounded-lg px-3 py-2 text-xs',
                                    'bg-danger-50 text-danger-800 dark:bg-danger-500/10 dark:text-danger-300' => ($finding['severity'] ?? '') === 'high',
                                    'bg-warning-50 text-warning-800 dark:bg-warning-500/10 dark:text-warning-300' => ($finding['severity'] ?? '') === 'medium',
                                    'bg-gray-50 text-gray-700 dark:bg-white/5 dark:text-gray-300' => ($finding['severity'] ?? '') === 'low',
                                ])>
                                    <span class="font-semibold">
                                        {{ ucfirst(str_replace('_', ' ', $finding['code'] ?? 'note')) }}
                                    </span>
                                    <span class="block">{{ $finding['detail'] ?? '' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <dl class="mt-3 space-y-1 text-xs text-gray-600 dark:text-gray-400">
                        @if ($submission->address)
                            <div class="flex justify-between gap-3">
                                <dt class="shrink-0">Address</dt>
                                <dd class="text-right">{{ $submission->address }}</dd>
                            </div>
                        @endif
                        @if ($submission->device_captured_at)
                            <div class="flex justify-between">
                                <dt>Captured</dt>
                                <dd>{{ $submission->device_captured_at->format('d M Y, H:i') }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt>Reading</dt>
                            <dd class="font-mono">{{ number_format($lat, 5) }}, {{ number_format($lng, 5) }}</dd>
                        </div>
                        @if ($submission->gps_accuracy)
                            <div class="flex justify-between">
                                <dt>Device accuracy</dt>
                                <dd>±{{ $submission->gps_accuracy }}m</dd>
                            </div>
                        @endif
                        @if ($task?->latitude)
                            <div class="flex justify-between">
                                <dt>Task point</dt>
                                <dd class="font-mono">
                                    {{ number_format((float) $task->latitude, 5) }},
                                    {{ number_format((float) $task->longitude, 5) }}
                                </dd>
                            </div>
                        @endif
                    </dl>

                    {{-- Google Maps.

                         Embedded when a key is configured, because seeing the
                         pin next to the task's own point is what actually
                         settles "was this the right place?".

                         Without a key it falls back to a link rather than an
                         iframe: Google's embed API returns a grey "for
                         development purposes only" tile when unauthenticated,
                         and a broken map is worse than an honest link.

                         Set GOOGLE_MAPS_KEY in .env to switch it on. --}}
                    @php($mapsKey = config('services.google_maps.key'))

                    @if ($mapsKey)
                        <div class="mt-3 overflow-hidden rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
                            <iframe
                                width="100%"
                                height="220"
                                style="border:0"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                allowfullscreen
                                src="https://www.google.com/maps/embed/v1/place?key={{ $mapsKey }}&q={{ $lat }},{{ $lng }}&zoom=17&maptype=satellite"
                            ></iframe>
                        </div>
                    @endif

                    <div class="mt-3 flex flex-wrap gap-3 text-sm font-medium">
                        <a
                            href="https://www.google.com/maps/search/?api=1&query={{ $lat }},{{ $lng }}"
                            target="_blank"
                            rel="noopener"
                            class="text-primary-600 hover:underline dark:text-primary-400"
                        >
                            Open in Google Maps →
                        </a>

                        {{-- The most useful single link on the page when a
                             submission is off site: it shows the gap between
                             where the work was meant to be and where it was
                             logged, at a glance. --}}
                        @if ($task?->latitude)
                            <a
                                href="https://www.google.com/maps/dir/?api=1&origin={{ $task->latitude }},{{ $task->longitude }}&destination={{ $lat }},{{ $lng }}"
                                target="_blank"
                                rel="noopener"
                                class="text-primary-600 hover:underline dark:text-primary-400"
                            >
                                Compare with task point →
                            </a>
                        @endif
                    </div>
                @endif
            </x-filament::section>

            {{-- Timeline --}}
            <x-filament::section heading="Timeline" icon="heroicon-o-clock">
                <ol class="space-y-2 text-sm">
                    @foreach ([
                        'Assigned' => $assignment?->assigned_at,
                        'Accepted' => $assignment?->accepted_at,
                        'Started' => $assignment?->started_at,
                        'Submitted' => $assignment?->submitted_at,
                        'Completed' => $assignment?->completed_at,
                    ] as $label => $moment)
                        <li class="flex items-center justify-between gap-3">
                            <span @class([
                                'text-gray-500 dark:text-gray-400' => ! $moment,
                                'text-gray-950 dark:text-white' => (bool) $moment,
                            ])>{{ $label }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $moment?->format('d M, H:i') ?? '—' }}
                            </span>
                        </li>
                    @endforeach
                </ol>

                @if ($assignment?->responseMinutes() !== null || $assignment?->durationMinutes() !== null)
                    <div class="mt-3 border-t border-gray-100 pt-3 text-xs text-gray-500 dark:border-white/5 dark:text-gray-400">
                        @if ($assignment->responseMinutes() !== null)
                            <p>Took {{ $assignment->responseMinutes() }} min to start after assignment.</p>
                        @endif
                        @if ($assignment->durationMinutes() !== null)
                            <p>Worked for {{ $assignment->durationMinutes() }} min.</p>
                        @endif
                    </div>
                @endif
            </x-filament::section>
        </div>
    </div>

</x-filament-panels::page>
