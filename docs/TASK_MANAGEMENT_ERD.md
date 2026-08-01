# Task Management — Entity Relationship Diagram

Companion to [TASK_MANAGEMENT.md](TASK_MANAGEMENT.md), which explains *why* the
schema is shaped this way. This file is the map.

**18 tables**: 16 new, plus two existing ones extended
(`push_tokens`, `users`).

---

## 1. Full ERD

```mermaid
erDiagram
    users ||--o{ tasks : creates
    users ||--o{ task_assignments : "is assigned"
    users ||--o{ task_submissions : submits
    users ||--o{ task_progress : reports
    users ||--o{ task_reviews : reviews
    users ||--o{ task_notifications : receives
    users ||--o{ task_activity_logs : acts
    users ||--o{ push_tokens : registers
    users ||--o{ notification_preferences : configures

    task_categories ||--o{ tasks : groups
    task_templates  ||--o{ tasks : "stamps out"
    task_templates  ||--o{ task_template_items : "blueprint for"
    task_templates  ||--o{ task_recurrences : "scheduled by"
    task_recurrences ||--o{ tasks : generates

    events          ||--o{ tasks : "context for"
    upcoming_events ||--o{ tasks : "context for"

    tasks ||--o{ tasks : "parent of"
    tasks ||--o{ task_dependencies : blocks
    tasks ||--o{ task_assignments : "handed to"
    tasks ||--o{ task_checklist_items : "broken into"
    tasks ||--o{ task_comments : discusses
    tasks ||--o{ task_attachments : "owns all files"
    tasks ||--o{ task_activity_logs : "audited by"
    tasks ||--o{ task_notifications : "notified about"

    task_assignments ||--o{ task_submissions : "attempts"
    task_assignments ||--o{ task_progress : "reports on"
    task_assignments ||--o{ task_reviews : "judged by"
    task_assignments ||--o{ task_checklist_completions : ticks
    task_checklist_items ||--o{ task_checklist_completions : "ticked as"

    task_submissions ||--o{ task_reviews : "verdict on"
    task_submissions ||--o{ task_attachments : "proof for"
    task_comments    ||--o{ task_attachments : "illustrated by"

    task_notifications ||--o{ push_notifications : "delivered as"
    push_tokens        ||--o{ push_notifications : "delivered to"

    tasks {
        bigint id PK
        varchar reference UK "TSK-2026-000042"
        bigint task_category_id FK "SET NULL"
        bigint event_id FK "SET NULL"
        bigint upcoming_event_id FK "SET NULL"
        bigint parent_task_id FK "CASCADE - subtasks"
        bigint task_template_id FK "SET NULL"
        bigint task_recurrence_id FK "SET NULL"
        date occurrence_date "UK with recurrence"
        varchar title
        text description
        longtext instructions
        varchar priority "low|medium|high|critical"
        varchar status "8-state machine"
        datetime start_date
        datetime due_date
        decimal estimated_hours
        decimal actual_hours "rolled up"
        decimal latitude "10,7"
        decimal longitude "10,7"
        int radius "metres"
        bool requires_photo
        bool requires_geo_check
        bool requires_review
        smallint max_assignees "1=single, null=unlimited"
        tinyint progress "0-100, cached"
        bigint created_by FK "SET NULL"
        bigint approved_by FK "SET NULL"
        timestamp published_at
        timestamp completed_at
        timestamp cancelled_at
        timestamp deleted_at "soft delete"
    }

    task_assignments {
        bigint id PK
        bigint task_id FK "CASCADE"
        bigint user_id FK "CASCADE"
        bigint assigned_by FK "SET NULL"
        varchar role "assignee|reviewer|watcher"
        varchar status "9-state machine"
        bool is_primary
        timestamp assigned_at "business event"
        timestamp accepted_at
        timestamp declined_at
        timestamp started_at
        timestamp submitted_at
        timestamp completed_at
        text remarks
        timestamp last_notified_at
        tinyint reminders_sent
    }

    task_submissions {
        bigint id PK
        bigint task_id FK "CASCADE"
        bigint task_assignment_id FK "CASCADE"
        bigint user_id FK "CASCADE"
        tinyint attempt "UK with assignment"
        text note
        decimal hours_spent
        decimal latitude "10,7"
        decimal longitude "10,7"
        int gps_accuracy
        int distance_meters "computed at write"
        bool is_within_geofence
        timestamp device_captured_at
        varchar status "derived from latest review"
    }

    task_progress {
        bigint id PK
        bigint task_assignment_id FK "CASCADE"
        bigint task_id FK "CASCADE"
        tinyint progress_percentage "0-100"
        text note
        decimal latitude "10,7"
        decimal longitude "10,7"
        int gps_accuracy
        bigint created_by FK "SET NULL"
    }

    task_reviews {
        bigint id PK
        bigint task_assignment_id FK "CASCADE"
        bigint task_submission_id FK "CASCADE"
        bigint task_id FK "CASCADE"
        decimal score "0-100"
        tinyint rating "1-5"
        text comments
        varchar review_status "approved|rejected|needs_revision"
        bigint reviewed_by FK "SET NULL"
        timestamp reviewed_at
    }

    task_attachments {
        bigint id PK
        varchar attachable_type "morph"
        bigint attachable_id "morph"
        bigint task_id FK "CASCADE - the only real FK"
        bigint task_assignment_id FK "CASCADE"
        bigint uploaded_by FK "SET NULL"
        varchar disk
        varchar file_path
        varchar file_name
        varchar file_type "image|video|document|audio"
        varchar mime_type
        bigint file_size
        smallint width
        smallint height
        int duration_seconds
    }

    task_notifications {
        bigint id PK
        bigint user_id FK "CASCADE"
        varchar title
        text body
        varchar type "event key"
        bigint task_id FK "SET NULL - survives purge"
        bigint task_assignment_id FK "SET NULL"
        bool is_read "indexed"
        timestamp read_at
        json data "deep link"
    }

    task_activity_logs {
        bigint id PK
        bigint task_id FK "CASCADE"
        bigint task_assignment_id FK "SET NULL"
        bigint user_id FK "SET NULL - trail survives"
        varchar action "19 actions"
        text description "composed at write"
        varchar from_status
        varchar to_status
        varchar ip "45 - IPv6"
        varchar device "user agent"
        json meta
    }

    push_tokens {
        bigint id PK
        bigint user_id FK "CASCADE"
        varchar token UK "moves between users"
        varchar platform
        varchar device_id
        varchar device_name
        varchar app_version
        varchar os_version
        varchar locale "en|fa|ps"
        bool is_active
        timestamp last_used_at
        tinyint failure_count
    }

    push_notifications {
        bigint id PK
        bigint user_id FK "CASCADE"
        bigint push_token_id FK "SET NULL"
        bigint task_notification_id FK "CASCADE"
        varchar related_type "morph"
        bigint related_id "morph"
        varchar event_key
        varchar title
        text body
        json data
        varchar status "queued|sent|failed|skipped"
        varchar fcm_message_id
        varchar error_code
        tinyint attempts
        timestamp sent_at
    }
```

---

## 2. Table inventory

| # | Table | Cols | Index parts | Purpose |
|---|---|---|---|---|
| 1 | `task_categories` | 10 | 4 | Planting, Watering, Pruning, Survey |
| 2 | `tasks` | 36 | 21 | The work item |
| 3 | `task_assignments` | 18 | 14 | Who is on it, and their own clock |
| 4 | `task_checklist_items` | 9 | 3 | The steps |
| 5 | `task_checklist_completions` | 8 | 5 | Who ticked what |
| 6 | `task_submissions` | 16 | 9 | One attempt, with proof-of-presence |
| 7 | `task_progress` | 11 | 6 | Interim "40% done" reports |
| 8 | `task_reviews` | 12 | 10 | An admin's verdict |
| 9 | `task_attachments` | 17 | 8 | Photos, videos, documents, audio |
| 10 | `task_comments` | 10 | 6 | Discussion + staff-only notes |
| 11 | `task_notifications` | 12 | 8 | In-app inbox |
| 12 | `task_activity_logs` | 13 | 9 | The audit trail |
| 13 | `task_dependencies` | 6 | 4 | "Cannot start until…" |
| 14 | `task_templates` | 18 | 5 | Reusable blueprints |
| 15 | `task_template_items` | 9 | 3 | Blueprint checklist |
| 16 | `task_recurrences` | 22 | 4 | Schedules that generate tasks |
| 17 | `push_tokens` *(extended)* | 14 | 7 | Device registry |
| 18 | `push_notifications` | 18 | 12 | FCM delivery log |
| — | `notification_preferences` | 7 | 4 | Per-event opt-outs |

---

## 3. Cascade rules at a glance

The governing rule: **operational data cascades, evidence does not.**

| Parent removed | Cascades away | Set to NULL (survives) |
|---|---|---|
| `tasks` (force delete) | assignments, submissions, progress, reviews, checklist, comments, attachments, activity logs | `task_notifications.task_id` |
| `task_assignments` | submissions, progress, reviews, checklist completions, attachments | `task_activity_logs.task_assignment_id`, `task_notifications.task_assignment_id` |
| `task_submissions` | reviews, attachments (via app-level hook) | — |
| `users` | their assignments, submissions, push tokens, notifications, preferences | `tasks.created_by`, `tasks.approved_by`, `task_reviews.reviewed_by`, `task_progress.created_by`, `task_activity_logs.user_id`, `task_attachments.uploaded_by` |
| `task_categories` | — | `tasks.task_category_id` |
| `events` / `upcoming_events` | — | `tasks.event_id`, `tasks.upcoming_event_id` |
| `task_templates` | template items, recurrences | `tasks.task_template_id` |

Three deliberate exceptions:

1. **`task_activity_logs.user_id` is SET NULL.** An audit trail that can be
   rewritten by deleting an account is not an audit trail.
2. **`task_notifications.task_id` is SET NULL.** A purged task must not silently
   empty entries out of someone's inbox.
3. **`task_attachments` needs application-level cleanup.** A polymorphic relation
   has no foreign key for the database to cascade, and files must be unlinked
   from disk anyway. `task_id` is the table's only real FK and exists precisely
   so no row can orphan; `HasTaskAttachments` handles the files.

Soft deletes on `tasks` and `task_comments` never touch files or cascade —
a restorable record must come back intact.

---

## 4. Uniqueness constraints that enforce correctness

| Constraint | Prevents |
|---|---|
| `tasks (reference)` | Ambiguous references when quoted over the phone |
| `tasks (task_recurrence_id, occurrence_date)` | A scheduler that runs twice duplicating an occurrence |
| `task_assignments (task_id, user_id, role)` | Assigning the same person twice |
| `task_submissions (task_assignment_id, attempt)` | A double-tapped submit button creating two review rows |
| `task_checklist_completions (item, assignment)` | Duplicate ticks — makes the endpoint an idempotent upsert |
| `task_dependencies (task_id, depends_on_task_id)` | Duplicate edges |
| `task_categories (slug)` | Ambiguous category lookups |
| `push_tokens (token)` | One phone registered twice; forces the row to **move** between users |

---

## 5. Index strategy

Every index exists for a named query. No speculative indexes — each one costs
write throughput and disk.

| Index | Serves |
|---|---|
| `tasks (status, due_date)` | Admin queue: open work, soonest deadline |
| `tasks (priority, status)` | Triage board |
| `tasks (due_date, status)` | Overdue sweep |
| `tasks (latitude, longitude)` | Map bounding box |
| `task_assignments (user_id, status)` | **"My Tasks"** — the hottest query in the app |
| `task_assignments (user_id, assigned_at)` | Volunteer workload by month |
| `task_assignments (status, last_notified_at)` | Reminder sweep |
| `task_submissions (status, created_at)` | Reviewer queue, oldest first |
| `task_progress (task_assignment_id, created_at)` | Progress trail |
| `task_reviews (review_status, reviewed_at)` | Quality dashboard |
| `task_reviews (reviewed_by, reviewed_at)` | A reviewer's own output |
| `task_attachments (task_id, file_type)` | Reviewer's gallery |
| `task_notifications (user_id, is_read)` | Unread badge count |
| `task_activity_logs (task_id, created_at)` | Task timeline |
| `task_activity_logs (action, created_at)` | "Everything approved last month" |
| `push_tokens (user_id, is_active)` | Push fan-out |
| `push_notifications (status, created_at)` | Delivery worker's queue |
| `task_recurrences (is_active, next_run_at)` | Generator's only query |

**Composite column order follows selectivity:** the equality-filtered column
leads, the range or sort column follows — `(user_id, status)` not
`(status, user_id)`, because every "My Tasks" query pins `user_id` and then
filters status.

---

## 6. Future scalability

**Where the growth is.** Three tables grow without bound while the rest scale
with the number of tasks:

| Table | Growth driver | Plan |
|---|---|---|
| `task_activity_logs` | ~10–15 rows per task lifecycle | First archival candidate. Partition by `created_at` or roll off to cold storage annually. Nothing reads beyond ~90 days except audits. |
| `push_notifications` | 1 row per device per event | Prune `sent` rows older than 30 days; keep `failed` for diagnosis. |
| `task_progress` | Unbounded — volunteers can report freely | Only the latest row per assignment feeds the roll-up; older rows are history. Safe to thin. |

**Already designed for scale:**

- **Cached roll-ups.** `tasks.progress` and `tasks.actual_hours` are
  denormalised. Every list screen reads them; only the report and review actions
  write them. Without this, each row in a task list would aggregate its children.
- **Write-time computation.** `task_submissions.distance_meters` is calculated
  once at submit, so the review queue never runs trigonometry over thousands of
  rows.
- **Denormalised `task_id`** on progress, reviews and attachments: the task
  timeline and gallery need no joins, and it doubles as the orphan guard.
- **Bounded recurrence.** `max_occurrences` means a misconfigured rule cannot
  flood the table.
- **Push is an outbox.** Rows are written `queued` and drained by a worker, so an
  FCM outage delays pushes rather than losing them.
- **VARCHAR + PHP enums, not MySQL ENUM.** Adding a status is a code-only deploy,
  not an `ALTER TABLE` with a write lock on a large table.

**When the geofence query becomes hot**, replace the composite
`(latitude, longitude)` B-tree with a `POINT` column and a `SPATIAL` index. The
application already refines candidates client-side, so the change is contained
to `Task::scopeWithinBounds()`.

---

## 7. Verified

Against MySQL 8, on a throwaway database:

- All six migrations apply, roll back, and re-apply cleanly. Rollback leaves the
  pre-existing `push_tokens` table exactly as it was.
- **283 assertions across four smoke suites, all passing** — tasks (58),
  assignments (65), attachments (71), tracking (89).
- `RolesAndPermissionsSeeder` seeds 30 task permissions across the five roles.
- Existing test suite unchanged: 32 passing.
