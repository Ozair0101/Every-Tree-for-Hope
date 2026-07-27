# Task Management — Eloquent Layer

Phase 2 reference. Companion to [TASK_MANAGEMENT.md](TASK_MANAGEMENT.md) (why the
schema is shaped this way) and [TASK_MANAGEMENT_ERD.md](TASK_MANAGEMENT_ERD.md)
(the map).

**19 models · 8 enums · 4 concerns · 1 exception.**

---

## 1. Models at a glance

| Model | Key helpers | Scopes |
|---|---|---|
| `Task` | `assign()` `assignMany()` `reassign()` `transitionTo()` `recalculateStatus()` `recalculateProgress()` `distanceTo()` `isWithinGeofence()` `canStart()` `logActivity()` | `open` `awaitingReview` `overdue` `forUser` `withinBounds` `mostUrgent` `forList` `forDetail` `withActivityCounts` `byUuid` |
| `TaskAssignment` | `transitionTo()` `accept()` `decline()` `start()` `reportProgress()` `durationMinutes()` `responseMinutes()` `nextAttemptNumber()` `hasCompletedRequiredChecklist()` `hasRequiredPhoto()` | `active` `assignees` `forUser` |
| `TaskSubmission` | `evaluateGeofence()` `reviewer()` `createOnce()` | `pending` `suspicious` `byClientUuid` `fromDevice` |
| `TaskProgress` | `hasLocation()` `createOnce()` `alreadyRecorded()` | `latestFirst` `withLocation` `byClientUuid` `fromDevice` |
| `TaskReview` | `record()` | `approved` `needingRevision` `byReviewer` `latestFirst` |
| `TaskAttachment` | `storeFor()` `url` `readable_size` `readable_duration` | `ofType` `images` `forAssignment` |
| `TaskComment` | — | `public` |
| `TaskChecklistItem` | `isCompletedBy()` `completeFor()` `uncompleteFor()` | `required` `ordered` |
| `TaskChecklistCompletion` | — | `forAssignment` `latestFirst` |
| `TaskDependency` | `isSatisfied()` `wouldCycle()` | `blocking` |
| `TaskCategory` | — | `active` |
| `TaskTemplate` | `toTaskAttributes()` | `active` |
| `TaskTemplateItem` | `toChecklistAttributes()` | `ordered` |
| `TaskRecurrence` | `nextOccurrenceAfter()` `hasRemainingOccurrences()` | `due` |
| `TaskNotification` | `notify()` `markAsRead()` `markAsUnread()` | `unread` `forUser` `latestFirst` |
| `TaskActivityLog` | `record()` `prunable()` | `timeline` `ofAction` `statusChanges` `byUser` |
| `PushToken` | `register()` `recordFailure()` `recordSuccess()` `looksValid()` | `active` |
| `PushNotification` | `markSent()` `markFailed()` `recentlySent()` `prunable()` | `queued` |
| `NotificationPreference` | `allows()` `allowsMany()` `optOut()` `optIn()` | `forChannel` `disabled` |

---

## 2. Enums

All backed by `string`, all with `label()` and `options()`. Stored as `VARCHAR`
so adding a case is a code-only deploy, not an `ALTER TABLE` with a write lock.

| Enum | Cases | Beyond labels |
|---|---|---|
| `TaskStatus` | Draft, Assigned, In Progress, Submitted, Under Review, Approved, Rejected, Cancelled | `allowedTransitions()` `canTransitionTo()` `isTerminal()` `isOpen()` `awaitsReview()` `countsTowardsOverdue()` |
| `TaskPriority` | Low, Medium, High, Critical | `weight()` `reminderLeadHours()` `color()` |
| `TaskAssignmentStatus` | Pending, Accepted, Declined, In Progress, Submitted, Approved, Rejected, Reassigned, Cancelled | `allowedTransitions()` `isActive()` `isDisengaged()` |
| `TaskAssignmentRole` | Assignee, Reviewer, Watcher | `canSubmit()` `canReview()` |
| `TaskSubmissionStatus` | Pending, Approved, Rejected, Needs Revision | `color()` |
| `TaskReviewStatus` | Approved, Rejected, Needs Revision | `isFinal()` `requiresRework()` `submissionStatus()` `assignmentStatus()` `notificationKey()` |
| `TaskAttachmentType` | Photo, Video, Document, Audio | `allowedMimeTypes()` `maxSizeKb()` `validationRules()` `fromMimeType()` |
| `TaskActivityAction` | 19 actions | `forStatus()` `forAssignmentStatus()` `isNotifiable()` `icon()` |

**The two state machines live in the enums, not the controllers.** Every write
path funnels through `canTransitionTo()`, so an illegal move is impossible
regardless of which client attempts it.

---

## 3. UUID strategy

The short answer to "use UUID if needed": **not as primary keys, and yes in two
specific places.**

### Why not primary keys

On InnoDB the primary key *is* the clustered index, and it is copied into every
secondary index. A random 36-character UUID there means:

- **Page splits on every insert** — new rows land in random positions rather than
  appending, so the B-tree is constantly reorganised.
- **Fatter secondary indexes** — every one carries the PK. On
  `task_activity_logs`, the fastest-growing table here, that is multiplied across
  millions of rows and several indexes.
- **Slower joins** — 36-byte comparisons instead of 8-byte.

Auto-increment `bigint` keys stay.

### Where UUIDs earn their place

**1. `tasks.uuid` — the public route key** (`HasPublicUuid`)

A sequential id in an API URL tells anyone who looks how many tasks the
organisation has ever created, and invites walking the range. The UUID is what
the mobile API exposes; the bigint stays the join key.

```php
Route::get('/tasks/{task}', ...);   // resolves on uuid — getRouteKeyName()
Task::byUuid($uuid)->first();
```

Generated with `Str::orderedUuid()`, not `Str::uuid()`. A time-ordered COMB UUID
means values created close together sort close together, so the unique index
appends rather than scattering writes across the B-tree. At volume that is the
difference between a healthy index and a fragmented one.

**2. `client_uuid` on submissions and progress — offline idempotency**
(`HasClientUuid`)

This is the one that matters most for a field app. On a connection that drops
mid-request, the client cannot tell a lost response from a lost request — so it
retries. Without a key of its own, that retry is a second submission in the
review queue or a duplicate progress report skewing the roll-up.

The **client** generates the key before its first attempt and sends the same
value on every retry, so it survives the app being killed between attempts.

```php
$assignment->reportProgress(40, $user, clientUuid: $requestKey);
// Same key again → returns the original row. No new row, no re-run
// transition, no duplicate push.
```

The unique index is the real guarantee; `createOnce()` catches the
duplicate-key error from a lost race and resolves to the winner's row.

---

## 4. Built for scale

### Column projection

`Task::forList()` selects `LIST_COLUMNS` — everything a row renders, and nothing
else. `description` (TEXT) and `instructions` (LONGTEXT) are excluded: a 50-row
page would otherwise drag fifty long bodies out of MySQL, over the wire and into
memory to render a title and a due date.

Every foreign key stays in the projection. Omitting one silently breaks eager
loading, which costs far more than the bytes saved.

### Eager-load bundles, not `$with`

`forList()` and `forDetail()` name their relations at the call site. A global
`$with` would pay for them on every query — counts, exports, the recurrence
generator — none of which touch relations. Naming the bundle keeps the cost where
the benefit is and makes an N+1 a visible omission rather than an invisible
default.

**Measured: 3 queries for a 15-row list, and accessing every relation on every
row adds zero more.**

### Retention — `MassPrunable`

Two tables grow without bound. Both are mass-pruned: plain chunked DELETEs, no
model hydration, because neither has a `deleted` hook to honour or files to
unlink.

| Model | Kept | Never pruned |
|---|---|---|
| `TaskActivityLog` | Status trail 730 days; routine chatter 180 | Anything on a task that is still open |
| `PushNotification` | Sent/skipped 30 days; failed 180 | Queued rows — one still queued after a month is a stuck worker, and deleting the evidence hides the bug |

`TaskAttachment` is deliberately **not** prunable: its rows own files on disk that
only the `deleted` hook unlinks.

Wire it up:

```php
// routes/console.php
Schedule::command('model:prune', [
    '--model' => [TaskActivityLog::class, PushNotification::class],
])->daily();
```

### Other choices

- **Cached roll-ups** — `tasks.progress`, `tasks.actual_hours`. Every list screen
  reads them; only the report and review actions write them.
- **Write-time computation** — `distance_meters` is calculated once at submit, so
  the review queue never runs trigonometry over thousands of rows.
- **`$perPage` per model** — 20 for tasks and submissions, 30 for progress, 50 for
  logs, matching what each screen actually shows.
- **Grouped OR in reusable queries** — `prunable()` wraps its branches, because a
  top-level `A OR B` silently mis-scopes the moment a caller composes onto it.
- **`chunkById` in the backfill** — a large table is not rewritten under one long
  transaction.

---

## 5. Concerns

| Trait | Gives | Used by |
|---|---|---|
| `HasPublicUuid` | UUID on create, `getRouteKeyName()`, `byUuid` scope | `Task` |
| `HasClientUuid` | `createOnce()` `alreadyRecorded()` `byClientUuid` `fromDevice` | `TaskSubmission`, `TaskProgress` |
| `HasTaskAttachments` | `attachments()` morph + deletion cleanup | `Task`, `TaskSubmission`, `TaskComment` |
| `HasSponsorCode`, `HasTreeSpecies` | *(pre-existing, untouched)* | — |

---

## 6. One gotcha worth knowing

A `Task` instance held across an assignment transition goes **stale**. The
roll-up moves the row through its own instance, so yours still has the old status
and `transitionTo()` will refuse the move — returning `false`, not throwing.

```php
$assignment->transitionTo(TaskAssignmentStatus::SUBMITTED, $user);
$task->refresh()->transitionTo(TaskStatus::UNDER_REVIEW, $admin);  // refresh() matters
```

This is ordinary Eloquent behaviour rather than anything specific to this module,
but it is the one place the roll-up design makes it easy to trip over.

---

## 7. Verified

**345 assertions across five suites, all passing** — tasks (58), assignments
(65), attachments (71), tracking (89), models (62).

The models suite covers: UUID generation and ordering, route-key binding,
integer PKs retained, offline retry returning the original row, the unique index
catching a raced duplicate, `forList` omitting long bodies while keeping FKs,
fixed query count under load, prune windows for open vs closed tasks and for
queued vs sent pushes, dependency satisfaction and cycle detection, preference
opt-out/opt-in, and a reflection audit asserting all 19 models have fillable and
relationships and all 8 enums have `label()` and `options()`.

Migrations apply, roll back and re-apply cleanly, and the repair path was tested
against a **reconstructed** copy of the drifted schema rather than a guess at it.
