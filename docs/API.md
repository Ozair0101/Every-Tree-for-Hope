# Every Tree for Hope — Mobile API

JSON API for the React Native app. It sits **alongside** the website: nothing
in `routes/web.php` or `app/Http/Controllers/*` was changed. The API is a
parallel copy under `Api\`.

| Layer       | Web                              | API                                     |
| ----------- | -------------------------------- | --------------------------------------- |
| Routes      | `routes/web.php`                 | `routes/api.php`                        |
| Controllers | `app/Http/Controllers/`          | `app/Http/Controllers/Api/`             |
| Output      | Blade views                      | `app/Http/Resources/` (JSON)            |
| Route names | `voices.index`                   | `api.voices.index`                      |

Base URL: `http://<host>/api`

---

## Conventions

### Response envelope

Every endpoint returns the same shape:

```json
{ "success": true, "message": null, "data": { } }
```

* `message` — a human-readable string on writes (already translated), else `null`.
* `data` — the payload: an object for single resources, an array for lists.

Paginated lists add a flat `meta` block:

```json
{
  "success": true,
  "data": [ ],
  "meta": { "current_page": 1, "last_page": 4, "per_page": 9, "total": 32, "has_more": true }
}
```

Pass `?per_page=N` (1–50) to change the page size, `?page=N` to move between pages.

### Errors

| Status | Meaning                                                     |
| ------ | ----------------------------------------------------------- |
| 404    | Not found, or the record is not public (inactive/unapproved) |
| 422    | Validation failed — see `errors` keyed by field name         |
| 429    | Rate limit hit on a write endpoint                           |

422 bodies are Laravel's standard shape:

```json
{ "message": "The email field is required.", "errors": { "email": ["The email field is required."] } }
```

### Headers

| Header         | Value              | Why                                                                      |
| -------------- | ------------------ | ------------------------------------------------------------------------ |
| `Accept`       | `application/json` | Optional — `ForceJsonResponse` sets it anyway                            |
| `X-Locale`     | `en` \| `fa` \| `ps` | Language for translated content and messages. `?lang=` also works        |
| `X-Device-Id`  | a UUID             | Identifies the device for voice likes. Generate once, store, always send |

Translatable fields (job postings, FAQs, upcoming events, job categories) come
back as a plain string already resolved to `X-Locale`, falling back to English.

### Uploads

Send `multipart/form-data` for any endpoint taking a file — job applications
(`resume`), volunteer sign-ups (`cv`), voices (`image`), tree requests (`media[]`).

---

## Endpoints

### Home & impact

| Method | Path      | Notes                                                    |
| ------ | --------- | -------------------------------------------------------- |
| GET    | `/home`   | One call for the home screen: stats + latest/upcoming events, voices, media, partners |
| GET    | `/stats`  | Impact counters only                                      |
| GET    | `/report` | Expenses, paginated, with `summary.total_spent` and `summary.by_type` |

### Events (past plantings)

| Method | Path              | Notes                                                        |
| ------ | ----------------- | ------------------------------------------------------------ |
| GET    | `/events`         | `?q=` matches a **sponsor code** first, then falls back to an event title search. When a code matches, the response includes a `sponsor` block |
| GET    | `/events/{id}`    | One event + `related_events`                                  |

### Upcoming events

| Method | Path                     | Notes                                        |
| ------ | ------------------------ | -------------------------------------------- |
| GET    | `/upcoming-events`       | Future events. `?include_past=1` for archive |
| GET    | `/upcoming-events/{id}`  | One event + `registrations_count`            |

### Careers

| Method | Path                        | Notes                                                |
| ------ | --------------------------- | ---------------------------------------------------- |
| GET    | `/careers`                  | `?q=`, `?type=`, `?category=` (slug)                 |
| GET    | `/careers/filters`          | Categories with open jobs + type/level dictionaries  |
| GET    | `/careers/{slug}`           | One job + `related_jobs`                             |
| POST   | `/careers/{slug}/apply`     | multipart; `resume` required (pdf/doc/docx, ≤10 MB)  |

Long-form job text is also returned pre-split as `requirement_lines`,
`responsibility_lines`, `benefit_lines` so the app can render bullet lists directly.

### Voices of Nature

| Method | Path                        | Notes                                                |
| ------ | --------------------------- | ---------------------------------------------------- |
| GET    | `/voices`                   | `?category=`, `?q=`. Includes `stats` and per-item `has_liked` |
| GET    | `/voices/categories`        | Category options for the composer                    |
| GET    | `/voices/{slug}`            | One voice + `comments` + `related`. Bumps view count |
| POST   | `/voices`                   | multipart (optional `image`). Held for moderation — response `status` is `pending` |
| POST   | `/voices/{slug}/like`       | Toggles. Returns `{ liked, count }`. Needs `X-Device-Id` |
| POST   | `/voices/{slug}/comment`    | Published immediately                                |

### Supporters

| Method | Path                          | Notes                                          |
| ------ | ----------------------------- | ---------------------------------------------- |
| GET    | `/donators`                   | The supporters wall                            |
| GET    | `/donators/{code}`            | By sponsor code (`ETH-NAME-01`) + their events |
| GET    | `/partners`                   | Sponsors, collaborators, supporters, other     |
| GET    | `/partners/advisors`          | Advisors only                                  |
| GET    | `/partners/{code}`            | By sponsor code + their events                 |
| GET    | `/sponsor-packages`           | Sponsorship tiers                              |
| GET    | `/sponsor-packages/{id}`      |                                                |

### Team, media, FAQ

| Method | Path                       | Notes                                      |
| ------ | -------------------------- | ------------------------------------------ |
| GET    | `/team`                    | Active members                             |
| GET    | `/team/{id}`               |                                            |
| GET    | `/media`                   | Videos, with YouTube id + thumbnail        |
| GET    | `/media/{id}`              |                                            |
| POST   | `/media/manage`            | Admin CRUD — see the security note below   |
| PUT    | `/media/manage/{id}`       |                                            |
| DELETE | `/media/manage/{id}`       |                                            |
| GET    | `/faqs`                    | Answered FAQs, flat (`faqs`) and grouped (`groups`) |
| POST   | `/faqs`                    | Ask a question                             |

### Form submissions

All rate limited to 10/minute per IP.

| Method | Path              | Notes                                                     |
| ------ | ----------------- | --------------------------------------------------------- |
| POST   | `/contact`        | `name`, `email`, `subject`, `message`                     |
| POST   | `/tree-requests`  | multipart; `media[]` up to 10 images/videos               |
| POST   | `/involvement`    | `type` = `volunteer` \| `sponsor` \| `collaborate`. Volunteers must send the extra fields and a `cv` PDF. Pass `upcoming_event_id` to register for an event instead of a general enquiry |

---

## Security note

`/media/manage/*` is **unauthenticated**, matching the web `MediaController`
routes it mirrors. Before shipping, guard that one route group:

```php
Route::prefix('manage')->name('manage.')->middleware('auth:sanctum')->group(function () {
```

The project already has `spatie/laravel-permission` wired up (`role`,
`permission`, `role_or_permission` middleware aliases in `bootstrap/app.php`),
so `->middleware('role:Admin')` works too once tokens are issued. Adding token
auth means installing Sanctum: `php artisan install:api`.

---

## Where things live

```
app/Http/Controllers/Api/     ApiController (base: envelope + pagination helpers)
                              CareerController, ContactController, DonatorController,
                              EventController, FaqController, HomeController,
                              InvolvementController, MediaController, PartnerController,
                              ReportController, SponsorPackageController, TeamController,
                              TreeRequestController, UpcomingEventController, VoiceController
app/Http/Resources/           One resource per model — the JSON shape
app/Http/Middleware/          ForceJsonResponse, SetApiLocale
routes/api.php                All routes, grouped by resource prefix
bootstrap/app.php             Registers the api route file + the two middleware
```

`HomeController` and `ReportController` have no web counterpart — the site's
home, about, works and report pages are static Blade views with no controller,
so these assemble the data those pages render inline.
