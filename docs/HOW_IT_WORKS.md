# How It Works — A Walkthrough

The task management system end to end, in the order you would actually meet it.
This is the operator's guide; for the schema reasoning see
[TASK_MANAGEMENT.md](TASK_MANAGEMENT.md), for the ERD see
[TASK_MANAGEMENT_ERD.md](TASK_MANAGEMENT_ERD.md).

---

## 0. Before you start

```bash
cd Every-Tree-for-Hope
php artisan migrate            # already applied on your dev DB
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan serve
php artisan queue:work         # in a second terminal — push notifications need it
php artisan schedule:work      # in a third — due-date reminders need it
```

Two of those are easy to forget and both fail *silently*:

- **Without `queue:work`**, every push notification is written to the database
  and never sent. The app looks fine; volunteers' phones stay quiet.
- **Without `schedule:work`** (or a real cron entry in production), the
  due-tomorrow reminder never fires.

In production, replace both with a supervisor process and a single crontab line:

```
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

---

## 1. Who is who

Five roles, seeded by `RolesAndPermissionsSeeder`:

| Role | Panel access | On tasks |
|---|---|---|
| **Super Admin** | yes | everything, bypasses all checks |
| **Admin** | yes | everything except role management |
| **Manager** | yes | runs the programme: create, assign, review, delete, cancel |
| **Staff** | yes | create, assign, review — but **cannot** delete or cancel |
| **Volunteer** | **no** | only what they are assigned |

**A volunteer holds no permissions at all.** That is deliberate. Their access
comes from *being attached to a task* — `TaskPolicy` checks the relationship,
not a permission. It exists as a named role so the assign dialog can list
"normal users", and so an account whose staff role was revoked is not
indistinguishable from a member of the public.

Anyone registering through the mobile app's registration form gets the
**Volunteer** role automatically, and `User::canAccessPanel()` excludes that
role — so a self-registered user can never reach `/admin`, whatever else
changes.

---

## 2. A task's life, step by step

```
  Draft ──▶ Assigned ──▶ In Progress ──▶ Submitted ──▶ Under Review ──▶ Approved
    │                                                        │
    │                                                        └──▶ Rejected ──┐
    └──▶ Cancelled                                                           │
                              (volunteer fixes it and resubmits) ◀───────────┘
```

The arrows are enforced in code, not by convention: `TaskStatus::allowedTransitions()`
owns the machine, and anything else is refused with an exception rather than
written to the database. The same is true of assignments, which have their own
clock.

### Step 1 — Create the task

**In the admin panel:** *Field Operations → Tasks → New task*.

The fields that change behaviour later:

| Field | What it does |
|---|---|
| `due_date` | drives the reminder, the overdue count, and the punctuality score |
| `latitude` / `longitude` / `radius` | defines the geofence |
| `requires_geo_check` | **turns geofence enforcement on** |
| `requires_photo` | refuses a submission with no photo on the current attempt |
| `estimated_hours` | reported against actual, nothing is blocked |

> **The one that catches people out:** setting coordinates and a radius does
> *nothing* on its own. `requires_geo_check` is the switch. Coordinates without
> it are a map pin; coordinates with it are a rule.

A task starts as **Draft** and is invisible to volunteers until published.

**From the mobile app:** the same thing over `POST /api/v1/tasks`, for a
coordinator standing in a field with no laptop. Same policy, same validation.

### Step 2 — Assign it

*Assign* on the task row, or `POST /api/v1/tasks/{task}/assignments`.

One volunteer or many — the same table either way. Each assignee gets their own
row with their own clock, so a five-person planting job tracks five independent
sets of timestamps rather than one blurred average.

The moment this happens: the task moves to **Assigned**, and each assignee gets
a push notification.

### Step 3 — The volunteer works

In the mobile app, under *My Tasks*:

1. **Accept** — stamps `accepted_at`.
2. **Start** — stamps `started_at`, task moves to **In Progress**, *the admin is
   notified*. This is also where the completion-time clock begins; the gap
   before starting is a scheduling matter and is deliberately excluded.
3. **Report progress** — as many interim updates as they like, each with a
   percentage, a note and a location. The task's own progress is recalculated
   from these.
4. **Attach proof** — photos, video, documents, audio.
5. **Submit** — stamps `submitted_at`, task moves to **Submitted**, *the admin
   is notified*.

Every one of these works offline. The app holds a persisted queue and each
action carries a client-generated `client_uuid`; when the phone reconnects, the
queue drains and the UUID makes a replayed request harmless. A volunteer in a
valley with no signal loses nothing.

### Step 4 — GPS verification (at submission)

If `requires_geo_check` is on, the submission is checked **at write time**, not
at read time — the verdict is stored on the row, so it can never be recomputed
differently later.

What is captured: latitude, longitude, accuracy, resolved address, capture time,
and the device's own mock-location flag.

What is computed: haversine distance to the task centre, and whether that falls
inside `radius`.

**Refused outright:** being outside the radius, or sending no location at all
when the task demands one. Both come back with a message saying which, and the
distance one tells the volunteer how far off they are.

**Flagged but accepted:** a mock-provider report, an implausible accuracy
figure, missing EXIF. These reach the reviewer marked rather than blocked —
`TaskSubmission::needsScrutiny()` and `verificationSeverity()` drive the badge —
because a wrongly-refused honest volunteer is a worse outcome than a flagged
dishonest one that a human then looks at.

> **Expect a tolerance.** The radius is widened by the handset's own reported
> accuracy, capped at 100m. A phone claiming ±40m standing 20m outside a 100m
> fence is let through, because refusing it punishes the phone rather than the
> person. The stored `within` flag is still computed against the *true* radius,
> so the reviewer sees that submission as borderline.

Anti-spoofing is honest about its limits: the device flag, accuracy plausibility
and distance are all checkable server-side; a rooted phone running a good
spoofer is not fully defeatable by any client-reported coordinate. The photo
EXIF cross-check is the second signal.

### Step 5 — Photo verification (per image)

Every uploaded image, on the way in:

- **GPS, capture time and device** are read from EXIF — and recorded as such
  *only when actually found*. A JPEG always carries some EXIF sections, so
  "has EXIF" is not evidence of anything; the source is marked `exif` only when
  real GPS, a real capture time, or a real camera identity was present.
- **Compressed** — triggered by dimensions *or* bytes. A 4000×3000 photo that
  happens to compress small still gets resized; serving it at full resolution to
  a phone on rural data was the bug that prompted this.
- **Thumbnail generated** for list views.
- **Original stored**, untouched, alongside. The compressed copy is what gets
  served; the original is what gets audited.

### Step 6 — The admin reviews

*Field Operations → Tasks → the submitted task → Review*.

On one screen: the task, the volunteer, the GPS verdict and map link, the full
timeline, every photo/video/document/audio file, the progress history, and the
comment thread.

The verdict:

- **Score** 0–100, **rating** 1–5, and a comment.
- **Approve** → assignment approved; when every assignee is signed off, the task
  rolls up to Approved (via Under Review — the machine has no Submitted→Approved
  jump).
- **Reject** → back to the volunteer, who resubmits as **attempt 2**. The
  rejected attempt is *kept*. That history is the point: it is how you answer
  "was this site actually visited in June?" a year later.
- **Need Revision** → same, phrased as a request rather than a refusal.

The volunteer is notified automatically on every one of these.

A reviewer may also overrule an earlier rejection — Rejected → Approved is
permitted at both assignment and task level, treated as an appeal.

---

## 3. Notifications

Six events send a push:

| Event | Who is notified |
|---|---|
| Admin assigns a task | the assignee(s) |
| Volunteer starts | the admin |
| Volunteer submits | the admin |
| Admin approves | the volunteer |
| Admin rejects | the volunteer |
| Task due tomorrow | the assignee — automatic, 08:00 daily |

How it travels: `TaskNotificationService` writes the database row, queues
`SendPushNotificationJob`, and `PushDispatcher` routes by token format —
`ExponentPushToken[...]` goes to Expo, everything else to FCM HTTP v1 (OAuth2
service account; the legacy server key has been dead since 2024).

Users can opt out per event type, and the opt-out is checked before the job is
queued, not after.

**Setup:** put the Firebase service-account JSON path in
`config/services.php` under `fcm`. Without it, notifications are recorded and
marked failed rather than crashing.

---

## 4. Trees — before and after

This is the volunteer-facing feature that is not a task.

A volunteer records a planted tree with a photo, species, coordinates and a
date. It arrives **pending** and is invisible publicly until approved.

Later — weeks or months on — the volunteer opens **the same record** and adds an
*after* image. Both photos then sit on one row, and the admin can compare them
side by side, with the growth interval calculated.

That gap is the point of the whole feature. Trees in the ground is an input;
trees photographed alive six months later is the outcome. The analytics
**follow-up rate** measures exactly that, and it is the honest number to put in
a donor report.

---

## 5. Analytics

*Field Operations → Analytics* (needs `view_any_task_activity_log` — a Viewer
who may see one task does not thereby see a league table of everyone's
performance).

What it shows, and what each number actually means:

| Metric | Definition — and why |
|---|---|
| **Monthly completed** | keyed on `completed_at`, never `created_at`. A task raised in March and finished in May is May's throughput. |
| **Average completion time** | measured from `started_at`, not `assigned_at` — otherwise it mostly measures how fast volunteers open the app. Reported with a **median** beside it, because one abandoned task finished three months late wrecks a mean. |
| **Top / lowest performers** | *not* "most tasks completed". A weighted score: 50% completion rate, 30% review quality, 20% punctuality. Anyone below 3 tasks is excluded — one perfect task is not evidence, and letting it top the table would make the screen untrustworthy. A missing component (no deadline, no review) is dropped and the rest renormalised, never scored as zero. |
| **Average review score** | shown with the distribution histogram. A mean of 70 from scores clustered at 70 is a very different programme from a mean of 70 made of 40s and 100s. |
| **Pending reviews** | count *plus* the age of the oldest. Five waiting an hour is healthy; five waiting three weeks is a programme losing its volunteers. |
| **Tree plantation count** | totals plus the follow-up rate. |
| **Heat map** | 7×24 grid of when submissions actually happen. A programme that discovers all its work happens 06:00–10:00 should stop sending reminders at 14:00. |
| **Location hotspots** | grouped by place *name*, not coordinate — a place a coordinator can name is actionable, a cluster centroid is not. |

**Exports** (both need `export_task`):

- **Excel** — four sheets: Summary, Monthly, Volunteers, Locations.
- **PDF** — opens a print-ready page; "Save as PDF" in the browser's print
  dialogue produces the file. The project carries no PDF library, and adding one
  should be a deliberate decision rather than a side effect of an export button.

Both read from `AnalyticsService`, the same source as the screen — so a figure
quoted in a grant application and the same figure on the dashboard cannot drift
apart.

---

## 6. Trying it yourself

The shortest path that exercises everything:

1. Log into `/admin` as a Manager.
2. **Create** a task. Set a due date, coordinates, a radius of 100m, and tick
   **both** `requires_geo_check` and `requires_photo`.
3. **Publish** it, then **assign** it to a volunteer account.
4. On the phone (or via the API), log in as that volunteer — the assignment push
   should already be waiting.
5. **Start** it. Check `/admin` — the task is now In Progress and you have a
   notification.
6. **Report progress** at 50% with a note.
7. Try to **submit from well outside the radius** — a few hundred metres, not
   ten. It is refused, and the message says by how far. (Standing just outside
   may be let through by the accuracy tolerance; that is the intended
   behaviour, not a bug.)
8. Submit from inside, with a photo. Check the attachment record — it should
   carry GPS, capture time, device, a thumbnail, and a stored original.
9. In `/admin`, open **Review**. Confirm you can see the map, the timeline, the
   photo and the progress history.
10. **Reject** it with a score and a comment. The volunteer is notified; the
    task returns to them.
11. Resubmit — note it is recorded as **attempt 2**, with attempt 1 intact.
12. **Approve**. The task rolls up to Approved.
13. Open **Analytics**. The task appears in this month's completed count, the
    volunteer appears once they cross three tasks, and the submission appears in
    the heat map at the hour you filed it.
14. **Export** both reports.

---

## 7. If something looks wrong

| Symptom | Almost always |
|---|---|
| Notifications never arrive | `queue:work` is not running |
| Due-date reminders never fire | `schedule:work` / cron is not running |
| Push marked failed | FCM service-account path missing from `config/services.php` |
| Geofence not enforced | `requires_geo_check` is off — coordinates alone do nothing |
| Volunteer sees no tasks | task is still **Draft**; publish it |
| A volunteer is missing from analytics | fewer than 3 assignments — the minimum sample |
| Volunteer can reach `/admin` | they hold a staff role as well; Volunteer alone cannot |

---

## 8. What has been verified

- **209 tests, 709 assertions, all passing.**
- Migrations apply, roll back and re-apply cleanly on MySQL 8, against a
  throwaway database — the dev database was never used for this.
- The analytics aggregates were additionally executed against **real MySQL under
  `ONLY_FULL_GROUP_BY`**. The suite runs on SQLite, and every dialect bug this
  project has hit (`FIELD()`, `DATE_FORMAT()`, `ALTER COLUMN … SET DEFAULT`) was
  invisible there and fatal in production. `App\Support\SqlDialect` now
  centralises those expressions so the next aggregate gets it right without
  knowing the history.
- Pint clean across all 448 files.
- The React Native app typechecks with no errors.
