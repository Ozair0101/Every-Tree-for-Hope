# Task Management — Database Architecture

The field-operations module: how planting, watering, pruning and survey work is
defined, handed to volunteers, proven, reviewed and audited.

Eighteen tables across six migrations. Sixteen are new; two existing tables were
extended additively (`push_tokens`, `users`). Nothing existing was reshaped.

See **[TASK_MANAGEMENT_ERD.md](TASK_MANAGEMENT_ERD.md)** for the full entity
relationship diagram, the cascade matrix, the index strategy and the scalability
plan.

---

## 1. Table map

```
task_categories ──┐
task_templates ───┼──▶ tasks ◀──── task_dependencies (self-referencing)
  └ template_items│      │  ▲
task_recurrences ─┘      │  └── task_activity_logs   (the audit trail)
                         │
                         ├──▶ task_checklist_items ──▶ task_checklist_completions
                         ├──▶ task_comments ──┐                    ▲
                         └──▶ task_assignments ───────────────────┘
                                  │        │
                                  │        ├──▶ task_progress   (interim reports)
                                  │        ├──▶ task_reviews    (verdicts)
                                  │        └──▶ task_attachments
                                  │             (morph: task | submission | comment,
                                  └──▶ task_submissions ──┘  + task_id, the real FK)

task_notifications ──▶ push_notifications ◀── push_tokens
   (what to SEE)          (did it ARRIVE)      (which devices)
                                ▲
                    notification_preferences (opt-outs)
```

| Table | Purpose |
|---|---|
| `task_categories` | Grouping + colour: Planting, Watering, Pruning, Survey |
| `tasks` | The work item — what, where, by when, to what standard |
| `task_assignments` | Who is attached, in which role, and their own progress |
| `task_progress` | Interim "I am 40% done" reports from the field |
| `task_reviews` | An admin's verdict: score, rating, comments |
| `task_notifications` | The task module's in-app inbox |
| `task_activity_logs` | The single audit trail — every action, ever |
| `task_checklist_items` | The steps a task is broken into |
| `task_checklist_completions` | Which assignee ticked which step |
| `task_submissions` | One attempt at completion, with proof-of-presence |
| `task_attachments` | Photos, videos, documents, audio — on a task, submission or comment |
| `task_comments` | Threaded discussion, with staff-only internal notes |
| `task_dependencies` | "This cannot start until that finishes" |
| `task_templates` | Reusable blueprints |
| `task_template_items` | The checklist a template stamps onto each task |
| `task_recurrences` | Schedules that generate tasks from a template |
| `push_tokens` *(existing, extended)* | Device registry, shared with the Expo channel |
| `notification_preferences` | Per-user, per-event opt-outs |
| `push_notifications` | FCM outbox and delivery log |

---

## 2. The eleven decisions that shape this schema

### 2.1 Assignment is a table, not a column

A plantation task is rarely one person's job — "water the 40 saplings at Qargha"
goes to a team. An `assigned_to` column would force one row per volunteer,
duplicating the title, instructions, geofence and due date, and making "how many
tasks are open?" unanswerable.

`task_assignments` gives each volunteer an independent lifecycle against one
shared definition of the work. Reviewers and watchers fall out of the same
structure for free via the `role` column.

**Two status columns, on purpose.** `tasks.status` is the task-level state;
`task_assignments.status` is one person's progress. Volunteer A can be
`submitted` while volunteer B has not accepted. The task status is always the
roll-up — `Task::recalculateStatus()` derives it, nothing else writes it.

#### One user or many — the same table

`tasks.max_assignees` is the only thing that separates the two cases:

| Value | Meaning |
|---|---|
| `1` | Single-assignee task. A second `assign()` throws `TaskAssignmentException` |
| `n` | Capped team task |
| `null` | Open to any number of volunteers |

Enforced in `Task::assign()` / `assignMany()`, not in a controller, so the rule
holds for the admin panel, the mobile API and any future importer alike.
`assignMany()` is all-or-nothing: if the fifth user would breach the cap, the
first four roll back too — a half-assigned task is worse than a refused one,
because the coordinator would otherwise discover days later that two of five
never got the push.

Reviewers and watchers are **not** counted against the cap; the limit is about
who does the work, not who oversees it. Declined and reassigned people are not
counted either, so a decline frees the slot automatically.

#### The lifecycle clock

`assigned_at`, `accepted_at`, `started_at`, `submitted_at`, `completed_at` (plus
`declined_at`) each record one milestone, so "how long did volunteers sit on this
before starting?" is a subtraction rather than a scan of the history table —
`responseMinutes()` and `durationMinutes()` read straight off these columns.

**`assigned_at` is deliberately not `created_at`.** `created_at` is when the row
was inserted; `assigned_at` is when the business event happened. They diverge
whenever a coordinator records an assignment agreed verbally in the field the day
before, or when historical data is imported. Reports must key off the business
event, so it gets its own column and defaults to `now()` only when the caller
does not supply one.

The timestamps are written **by** the transition, never independently:
`TaskAssignment::transitionTo()` guards the move against
`TaskAssignmentStatus::allowedTransitions()` and stamps the matching column in
the same save. Without that guard a client could submit work that was never
started, leaving `started_at` null while `submitted_at` is set — quietly
corrupting every duration report built on those columns.

```
Pending ─ accept ─▶ Accepted ─ start ─▶ In Progress ─ submit ─▶ Submitted
   │                                        ▲                       │
decline                                   rework                  review
   ▼                                        │                       ▼
Declined                             Rejected ◀──────────────── Approved
```

Two deliberate details: starting straight from Pending is legal (a volunteer who
taps "Start" has plainly accepted, and should not have to say so twice), and
rework uses `??=` on `started_at` so the original start time survives — total
elapsed time stays honest across a reject-and-retry.

#### `remarks`

One free-text column rather than one per reason: it carries the decline reason,
the reassignment note, the reviewer's rejection comment. The text is only ever
read by a human, never filtered on, so splitting it would buy nothing.

#### Reassignment preserves history

`Task::reassign()` closes the old row as `reassigned` — never deletes it — and
carries the primary flag across. The record that a task was once Ahmad's, and
why it moved, is exactly what a coordinator needs three months later.

#### Roll-up bridges the review step

`Task::recalculateStatus()` derives the task status from its assignees. The task
machine refuses Submitted → Approved (approval must pass through review), so when
every assignee has been signed off individually the roll-up walks through
`Under Review` rather than failing silently and stranding the task at Submitted.
Both moves land in `task_activity_logs`.

When *every* engaged assignee is rejected, the task itself moves to Rejected —
otherwise it would sit in Submitted, where a coordinator would read it as still
queued for review rather than needing rework.

### 2.2 Submissions are rows, not columns

Rejection is a first-class outcome: a reviewer sends work back and the volunteer
tries again. Storing the submission on the assignment would overwrite the
rejected attempt and destroy the evidence of what was wrong the first time.

Each attempt is its own immutable row, numbered by `attempt`, unique per
assignment. That unique index also makes a double-tapped submit button a
duplicate-key error rather than two rows in the review queue.

### 2.3 Proof-of-presence is computed at write time

Every submission captures the device's own GPS reading. The server computes the
haversine distance to the task centre once, at submit, and stores it in
`distance_meters` — so the review queue never runs trigonometry over thousands
of rows.

Being outside the geofence sets `is_within_geofence = false`. It does **not**
reject the submission: rural GPS drifts by tens of metres and a hard block would
strand honest volunteers. The reviewer sees the flag and decides.
`TaskSubmission::suspicious()` scopes to exactly these rows.

`device_captured_at` stores the phone's own clock at capture. Compared against
`created_at` it reveals work logged long after the fact, or a tampered clock.

### 2.4 One attachment table, three owners

Photos, videos, documents and audio all live in `task_attachments`, hanging off
whichever model they belong to:

| Owner | What it holds |
|---|---|
| `Task` | Reference material from the office — site map, species sheet |
| `TaskSubmission` | The volunteer's proof for **one attempt** |
| `TaskComment` | A picture attached to a question from the field |

One table means one upload endpoint, one validation path, one URL accessor and
one cleanup job. Splitting it three ways would triple all of that to save
nothing.

**Why the morph *and* `task_assignment_id`.** The morph records precisely what a
file belongs to, which matters most for submissions: attempt 1 was rejected for
dark photos and attempt 2 has new ones, and a file keyed only to the assignment
could not tell them apart. `task_assignment_id` is the denormalised index for
"every file this volunteer uploaded for this job" — the query the app gallery and
the reviewer's sidebar actually run.

**`task_id` is the only real foreign key on the table, and it is load-bearing.**
A polymorphic relation cannot be cascaded by the database. Without `task_id`, a
deleted task would cascade its submissions away at the SQL level — firing no
Eloquent events — and leave their attachment rows pointing at nothing. Every
attachment carries it, whatever it hangs off, so no orphan can survive.

**File cleanup is application-level, because it has to be.** The
`HasTaskAttachments` trait deletes attachments one model at a time on `deleting`,
since each row's own `deleted` hook is what unlinks the physical file — a bulk
`delete()` query would clear the table and leak the storage. `Task` overrides the
sweep to catch its whole tree by `task_id` before the database cascade runs. Soft
deletes never touch files: a restorable record must come back intact.

**The type is a constraint, not a label.** `App\Enums\TaskAttachmentType` carries
the accepted MIME allow-list and the size ceiling per category, so the upload
endpoint, the Filament form and any future importer validate identically:

| Type | Ceiling | Notes |
|---|---|---|
| Photo | 10 MB | includes `image/heic` — the iPhone camera default |
| Video | 64 MB | includes `video/3gpp` — common on low-end Android |
| Document | 25 MB | PDF, Word, Excel, text, CSV |
| Audio | 25 MB | includes `audio/3gpp` voice notes |

Ceilings are deliberately conservative. This app runs in the field in
Afghanistan, often on 3G and prepaid data — a 200MB video that fails at 90% costs
a volunteer real money and gets the app uninstalled. Anything not on the
allow-list is refused outright, including SVG (a scriptable vector format).

`requires_photo` is enforced against the **current attempt only**
(`TaskAssignment::hasRequiredPhoto()`). A photo uploaded for a rejected earlier
attempt does not satisfy the retry — that photo is usually the very thing that
was wrong.

### 2.5 Status transitions are owned by the enum

`App\Enums\TaskStatus` — not the controllers — owns the state machine. Every
write path funnels through `Task::transitionTo()`, so an illegal jump such as
Draft → Approved is impossible regardless of which client attempts it.

```
Draft ─ publish ─▶ Assigned ─ accept ─▶ In Progress ─ submit ─▶ Submitted
                                                                   │
                                                           take for review
                                                                   ▼
       Approved ◀── approve ── Under Review ── reject ──▶ Rejected
                                                             │
                                                    rework ──┘ ▶ In Progress
```

Cancelled is reachable from any non-terminal state. Approved and Cancelled are
terminal. The status write and its `task_status_histories` row share a
transaction — an audit trail with gaps is worse than no audit trail.

### 2.6 Progress and submission are different things

Both are needed, and conflating them loses information:

| | `task_progress` | `task_submissions` |
|---|---|---|
| Says | "I am partway, here is where I am" | "I am finished, review this" |
| Reviewed | Never | Always (unless `requires_review = false`) |
| Frequency | Any number of times a day | Once per attempt |
| Purpose | Keeps a long task visible while it runs | The reviewable deliverable |

Watering 200 saplings across three days would otherwise be silent until it was
over. Filing a progress report also *starts* the assignment — a volunteer who
skips "Accept" and just reports 20% has plainly begun, and the coordinator should
see that.

`tasks.progress` prefers reported figures over checklist counting: a person on
the ground saying "60%" is better information than counting ticked boxes.
Assignees who have not reported count as **0**, not skipped — a five-person task
where one person says 100% is 20% done, not 100%.

### 2.7 Review is a table, and has three outcomes

A reviewer can look at the same attempt twice: a second opinion, an appeal after
the volunteer sends better photos, a supervisor overruling a junior. Columns on
`task_submissions` could only ever remember the last opinion, and the earlier one
is precisely what an appeal needs. `task_submissions.status` stays as the cache
the review queue filters on; `task_reviews` is the record.

**"Needs Revision" is not a soft rejection.** It is the difference between a
system volunteers tolerate and one they abandon — a rejection reads as "your work
was bad", a revision request reads as "nearly, fix this one thing". Different
push, different wording, different submission status.

Two measures, both optional: `score` is objective marks out of 100 ("38 of 40
saplings alive"); `rating` is 1–5 stars for the quality of the work. Both are
clamped in the model, because a stray 9-star rating would poison every average
built on the column.

Approving previously rejected work is legal at both the assignment and task
level — that is what an appeal *is*. The earlier verdict survives in
`task_reviews`, so the reversal is on the record.

### 2.8 Three notification tables, each doing one job

| Table | Answers |
|---|---|
| `notifications` *(existing)* | Generic Laravel inbox — trees, voices |
| `task_notifications` | What the user should **see** about tasks |
| `push_notifications` | Whether it **reached** each device |

The existing `notifications` table is live and read by the shipped mobile
endpoints via `$user->notifications()`. Reshaping it to fit this module would
break the tree-approval inbox, so the task inbox is its own table with the flat
`title/body/type/task_id/is_read` shape the app screen wants. The `User` relation
is deliberately named `taskNotifications()` — overriding `notifications()` would
break the Notifiable trait.

One inbox row fans out to one delivery row per device. Keeping them apart means a
dead push token cannot make a notification vanish from someone's list.

**`is_read` is redundant with `read_at != null`, on purpose.** The unread badge is
the most-run query in the app, and a boolean leads a composite index far better
than a nullable timestamp. Redundancy is only safe if it cannot drift, so the
model derives one from the other on every save rather than trusting call sites.

### 2.9 One device registry, not two

`push_tokens` already existed, backing the Expo push channel. Rather than adding
a parallel `device_tokens` table, it was **extended** with the columns delivery
logging needs (`device_id`, `device_name`, `app_version`, `os_version`, `locale`,
`is_active`, `failure_count`).

Two registries would have meant two registration endpoints, two cleanup jobs, and
an app left guessing which one a given device is in.

One behavioural difference is preserved rather than forced: the Expo channel
*deletes* tokens Expo reports dead, while the task module needs them to survive
because `push_notifications` rows point at them. `PushToken::recordFailure()`
deactivates instead, so the cleanup job can switch to the flag whenever you want
without any other change.

### 2.10 One audit log, not two

`task_activity_logs` absorbed what began as a separate `task_status_histories`.
Two tables recording overlapping events meant writing two rows per transition and
gave drift somewhere to hide.

Status changes keep typed `from_status` / `to_status` columns, so "when was this
approved?" stays an indexed query while everything else lands in the same
timeline. `Task::statusHistories()` still exists as a scoped view over the merged
table, so nothing that read it had to change.

**The person's action and the task's status change are separate rows saying
different things.** `TaskAssignment::transitionTo()` records "Ahmad submitted his
work"; the roll-up records "the task moved to Submitted" as a generic status
change. One event is never told twice in two voices.

Descriptions are composed at write time and stored, not rendered on read — the
wording must stay true even after the task is renamed or the user is deleted. A
timeline that rewrites itself is worthless as evidence, which is also why
`user_id` is `SET NULL` rather than cascading.

### 2.11 Enum-ish columns are VARCHAR, not MySQL ENUM

A native `ENUM` requires an `ALTER TABLE` — a full table rebuild and a write
lock — every time a case is added. `VARCHAR(20)` cast to a PHP backed enum keeps
values validated in code and makes adding a status a code-only deploy.

---

## 3. Integration with the existing app

| Existing thing | How tasks connect |
|---|---|
| `events` / `upcoming_events` | `tasks.event_id`, `tasks.upcoming_event_id` — a watering task belongs to a past planting event; a prep task to an upcoming one. Both optional. |
| `trees` GPS convention | Same `decimal(10,7)` precision, so tasks and trees plot on one map with no conversion. |
| `users` | Volunteers are ordinary users with no role, exactly as for trees and voices. |
| spatie/laravel-permission | New `operations` scope in `PermissionCatalog`, wired into the existing five roles. |
| Sanctum `/api/v1` | Mobile endpoints slot in under the same prefix and policy pipeline. |
| `notifications` table | Untouched — still the in-app bell. The FCM tables cover *delivery*, a separate problem. |

### Role grants (from `RolesAndPermissionsSeeder`)

| Role | Task permissions |
|---|---|
| Super Admin | Everything, via the `Gate::before()` bypass |
| Admin | All 28 |
| Manager | All 28 — runs the programme end to end |
| Staff | 18: view/create/update, `assign_task`, `review_task`. No delete, no cancel |
| Viewer | 8: read-only |

`assign_task` and `review_task` are separate from `update_task` deliberately — a
coordinator may hand out and sign off work without being able to rewrite the
brief, and a reviewer is not automatically an editor.

---

## 4. Firebase Cloud Messaging

**Why `push_notifications` is separate from `notifications`.** The existing
table is the record of *what the user should see*. These tables cover *delivery
to a device*, which fails in ways the inbox should never inherit: tokens expire,
apps get uninstalled, FCM rate-limits and answers per-token. One logical
notification to a user with three phones is three delivery rows and one inbox
row.

**Token hygiene.** `device_tokens` is keyed by the token itself, so re-register
is an upsert. `DeviceToken::register()` also re-keys a token to the current user
— one phone shared by two volunteers must not keep pushing A's tasks to B. On
`UNREGISTERED` / `INVALID_ARGUMENT`, or three consecutive failures, the row is
deactivated rather than deleted, so delivery history survives.

**Preferences store only opt-outs.** Absence of a row means enabled, so the
table stays proportional to how many people actually changed something rather
than to `users × events × channels`.

**Event keys:** `task.assigned`, `task.updated`, `task.due_soon`,
`task.overdue`, `task.submitted`, `task.approved`, `task.rejected`,
`task.commented`, `task.cancelled`.

`PushNotification::recentlySent()` guards against a reminder job that runs twice,
or a status flapping, turning into a burst of identical pushes.

---

## 5. Indexing

Every index exists for a named query.

| Index | Serves |
|---|---|
| `tasks (status, due_date)` | Admin queue: open work, soonest deadline first |
| `tasks (priority, status)` | Triage board: critical work not yet finished |
| `tasks (due_date, status)` | Overdue sweep run by the reminder scheduler |
| `tasks (latitude, longitude)` | Map bounding box — "tasks near me" |
| `task_assignments (user_id, status)` | **"My Tasks"** — the hottest query in the app |
| `task_assignments (status, last_notified_at)` | Reminder sweep |
| `task_assignments (user_id, assigned_at)` | Volunteer workload: "what did Ahmad take on this month?" |
| `task_attachments (task_id, file_type)` | Reviewer's gallery: every file on a task, grouped by kind |
| `task_attachments (task_assignment_id, file_type)` | One volunteer's uploads for one job |
| `task_submissions (status, created_at)` | Reviewer queue, oldest first |
| `task_status_histories (task_id, created_at)` | Task detail timeline |
| `device_tokens (user_id, is_active)` | Push fan-out |
| `push_notifications (status, created_at)` | Delivery worker's queue |
| `task_recurrences (is_active, next_run_at)` | Generator's only query |

**Bounding box, not radius, for map queries.** A box hits the composite
`(latitude, longitude)` B-tree; the app refines to a true circle client-side once
the candidate set is small. Migrate to a `SPATIAL` POINT index only if this
becomes a measured bottleneck.

### Uniqueness that enforces correctness

| Constraint | Prevents |
|---|---|
| `task_assignments (task_id, user_id, role)` | Assigning the same person twice |
| `task_submissions (task_assignment_id, attempt)` | Double-tapped submit creating two review rows |
| `task_checklist_completions (item, assignment)` | Duplicate ticks — makes the endpoint an idempotent upsert, which matters on a flaky rural connection |
| `tasks (task_recurrence_id, occurrence_date)` | A scheduler that runs twice, or a replayed queue job, duplicating an occurrence |
| `device_tokens (token)` | Duplicate device registrations |

---

## 6. Deletion behaviour

| Foreign key | On delete | Why |
|---|---|---|
| `tasks.created_by` | SET NULL | A task records work that actually happened. Deleting the staff member who wrote it must not delete the volunteers' history. |
| `tasks.task_category_id` | SET NULL | Removing a category must not destroy its work. |
| `tasks.event_id` | SET NULL | Same. |
| `task_assignments.user_id` | CASCADE | A departed volunteer's assignment is meaningless; the task and its submissions survive. |
| `task_submissions.task_id` | CASCADE | A submission has no meaning without its task. |
| `task_checklist_completions.*` | CASCADE | Pure join data. |
| `tasks` | Soft delete | Cancelling is a status; hard removal must not silently orphan submissions and audit rows. |

---

## 7. Scale characteristics

- **Cached roll-ups.** `tasks.progress` and `tasks.actual_hours` are
  denormalised. Every list screen and workload report reads them; only the tick
  and review actions write them. Without this, each task row in a list would
  aggregate its children.
- **Recurrence is bounded.** `max_occurrences` is a safety net as much as a
  feature: a misconfigured rule cannot flood the table.
- **Generated tasks are ordinary rows.** Editing one never touches its template;
  deactivating a schedule leaves existing work standing.
- **Push is an outbox.** Rows are written `queued` and drained by a worker, so
  an FCM outage delays pushes instead of losing them.
- **Audit tables only grow.** `task_status_histories` and `push_notifications`
  are the archival candidates — partition or roll off by `created_at` first.

---

## 8. Verification performed

Validated on MySQL 8 against a throwaway database (the dev database was not
touched):

- All five migrations apply cleanly, roll back cleanly, and re-apply.
- **283 assertions across four smoke suites, all passing.**
  - *Tasks (58)*: reference generation, enum casts, illegal-transition refusal,
    audit-trail completeness, checklist gating, progress roll-up, haversine
    accuracy (14m vs 5105m), geofence flagging, reject-and-retry attempt
    numbering, all five unique-index guards, every query scope, FCM token upsert
    and re-keying, opt-out precedence, duplicate-push guard, cascade integrity.
  - *Assignments (65)*: single-assignee refusal, unlimited and capped team
    assignment, all-or-nothing `assignMany` rollback, idempotent re-assign,
    reviewers exempt from the cap, `assigned_at` backdating and divergence from
    `created_at`, every illegal lifecycle move refused with no stray timestamps,
    each milestone stamped, `started_at` preserved through rework, remarks on
    decline and rejection, reassignment audit trail and primary-flag carry-over,
    duration/response metrics, closed tasks refusing new assignees.
  - *Attachments (71)*: all four categories classified from real MIME types,
    executables and SVG refused, size ceilings, storage against all three owner
    types, attempt-level isolation across a reject-and-retry, `requires_photo`
    enforced against the current attempt only, every scope, human-readable size
    and duration, and the full deletion matrix — soft delete keeps files, force
    delete removes them, and a hard-deleted task leaks nothing from its
    submissions or comments.
- `RolesAndPermissionsSeeder` seeds 28 task permissions and distributes them
  correctly across the five roles.
- Existing test suite: 32 passing, unchanged.

Four bugs were caught and fixed by this exercise: `tasks.reference` was NOT NULL
but generated post-insert; the planning migration's `down()` dropped a unique
index MySQL still needed for a foreign key; the status roll-up tried to jump
Submitted → Approved, which the task machine forbids, silently stranding a task
whose assignees were all signed off; and deleting an attachment's owner orphaned
its rows and leaked its files, because a polymorphic relation has no foreign key
for the database to cascade.

---

## 9. What was built on top

The six items this document once listed as outstanding have all landed. The
data layer described above is now the foundation for:

1. **REST API** — 34 routes under `/api/v1/tasks`, plus device registration on
   `/api/v1/notifications/devices`.
2. **Filament resources and pages** — the task board, the review screen, the
   calendar, and the analytics dashboard.
3. **Policies** — `TaskPolicy` and siblings on the `BasePolicy` convention, so
   the panel and the API share one ruleset.
4. **FCM transport** — `FcmService` (HTTP v1, OAuth2 service account),
   `PushDispatcher`, and the queued `SendPushNotificationJob`.
5. **Scheduled jobs** — due-tomorrow reminders (08:00), stuck-push flushing
   (hourly), and log pruning (03:30).
6. **GPS and photo verification** — geofence evaluation at write time, EXIF
   extraction, compression, thumbnails, and before/after comparison.
7. **Analytics** — `AnalyticsService` and the performance dashboard, with Excel
   and printable exports.

For a step-by-step account of how these fit together in daily use, see
**[HOW_IT_WORKS.md](HOW_IT_WORKS.md)**.

Current state: **209 tests, 709 assertions, all passing**; migrations apply,
roll back and re-apply cleanly on MySQL 8; the analytics aggregates were
additionally executed against real MySQL under `ONLY_FULL_GROUP_BY`, since the
suite itself runs on SQLite and every dialect bug this project has hit was
invisible there.
