# Ivora Dental Studio Zynera Dental

A complete dental clinic platform built in Laravel 12 — public marketing site, patient portal, and a staff console with a hand-rolled role & permission system.

Everything is server-rendered Blade. There is no npm build step: Tailwind runs from the Play CDN with an inline theme, and the interaction layer is one vanilla JS file. Clone, `composer install`, migrate, and it runs.

---

## What's inside

**Public website** — homepage, about, treatments index + detail, practitioner directory + profiles, transparent pricing, gallery, journal (blog), FAQs, reviews, contact, and a live booking flow that reads real availability from the diary.

**Patient portal** (`/portal`) — dashboard, appointments (book, reschedule, cancel), clinical records, prescriptions with PDF download, invoices with PDF download, medical history, notifications.

**Staff console** (`/console`) — dashboard with charts, appointment list + weekly diary + printable day sheet, patient register, clinical notes and prescriptions, invoicing and payments, treatment/blog/gallery/testimonial/FAQ management, review moderation, enquiry inbox, mailing list broadcasts, team accounts, role editor, per-person permission overrides, clinic settings, audit trail, SMS outbox, and date-ranged reports.

---

## Running it locally

You need **PHP 8.2+**, **Composer**, and **MySQL 8** (or MariaDB 10.6+). SQLite works too if you change `DB_CONNECTION`.

```bash
# 1. Install dependencies
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Create the database, then point .env at it
#    mysql -u root -e "CREATE DATABASE ivora_dental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
#    DB_DATABASE=ivora_dental
#    DB_USERNAME=root
#    DB_PASSWORD=

# 4. Tables + demo data (about 700 appointments, 25 patients, 10 staff)
php artisan migrate --seed

# 5. Storage symlink so uploaded images resolve
php artisan storage:link

# 6. Serve
php artisan serve
```

Open <http://127.0.0.1:8000>.

### Two more processes you'll want

Queued mail and SMS sit in the `jobs` table until a worker picks them up. Without this, booking still works but no email ever leaves:

```bash
php artisan queue:work
```

Scheduled work (reminders, no-show sweeps, the morning digest) runs off one cron entry:

```cron
* * * * * cd /path/to/ivora-dental && php artisan schedule:run >> /dev/null 2>&1
```

To fire the scheduled jobs by hand while developing:

```bash
php artisan ivora:send-reminders     # email + SMS for visits inside the reminder window
php artisan ivora:daily-schedule     # each dentist's chair list for today
php artisan ivora:mark-no-shows      # yesterday's unattended visits
php artisan ivora:purge-tokens       # expired reset / verification tokens
php artisan ivora:backfill-invoices  # invoices for completed visits that never got one
```

### Reset to a clean demo state

```bash
composer fresh          # alias for: php artisan migrate:fresh --seed
```

---

## Sign-in details

All seeded accounts use the password `password`.

| Role | Email | What they can reach |
|---|---|---|
| Clinic owner | `owner@ivoradental.com` | Everything — bypasses the permission matrix entirely |
| Dentist | `dentist@ivoradental.com` | Own diary, patients, clinical notes, prescriptions |
| Receptionist | `reception@ivoradental.com` | Bookings, patient registration, enquiries, mailing list |
| Accountant | `accounts@ivoradental.com` | Invoices, payments, financial exports — no clinical access |
| Content editor | `editor@ivoradental.com` | Treatments, journal, gallery, FAQs only |
| Dental assistant | `assistant@ivoradental.com` | Read-only diary and clinical notes |
| Patient | `patient@example.com` | Their own portal |

Signing in as the editor or the accountant is the fastest way to see the permission system working — the sidebar rebuilds itself around what each role actually holds.

---

## Packages installed, and why

| Package | Why it's here |
|---|---|
| `laravel/framework` ^12.0 | The framework. |
| `maatwebsite/excel` ^3.1 | Excel/CSV exports for appointments, patients, invoices and payments. Each export is a class in `app/Exports/`. |
| `barryvdh/laravel-dompdf` ^3.0 | Server-side PDFs: invoices, prescriptions, the printable day sheet, appointment exports and practice reports. Templates live in `resources/views/pdf/`. |
| `twilio/sdk` ^8.3 | The production SMS driver. Optional — the app ships on the `log` driver and only touches Twilio if you set `SMS_DRIVER=twilio`. |
| `laravel/tinker` | REPL for poking at the data. |

Dev only: `phpunit/phpunit`, `fakerphp/faker`, `nunomaduro/collision`, `laravel/pail`, `laravel/sail`, `mockery/mockery`.

**Deliberately not installed:** no `spatie/laravel-permission` (the roles system is custom, as specified), no Breeze/Jetstream/Fortify (authentication is hand-rolled), no npm build chain.

### Front-end libraries (all via CDN, nothing to install)

Tailwind CSS 3.4 (Play CDN with an inline theme config), Chart.js 4.4 for the dashboard charts, and Google Fonts for Bricolage Grotesque / Inter Tight / JetBrains Mono. Scroll reveals, counters, accordions, tabs, dropdowns, modals, the repeatable-row builder and the live slot picker are all in `public/assets/js/ivora.js` — about 250 lines of vanilla JS, no dependencies, and everything respects `prefers-reduced-motion`.

If you'd rather compile Tailwind properly for production, swap the CDN `<script>` in `resources/views/layouts/partials/head.blade.php` for a Vite build and move the inline `tailwind.config` into a real config file. The class names don't change.

---

## Configuration you'll probably touch

### Mail

Out of the box `MAIL_MAILER=log`, so every email is written to `storage/logs/laravel.log` and nothing is sent. For real delivery point it at Mailtrap, Mailpit or Gmail SMTP:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=...
MAIL_PASSWORD=...
```

### SMS

```dotenv
SMS_DRIVER=log        # writes to storage/logs/sms.log and the sms_logs table
# SMS_DRIVER=twilio
# TWILIO_SID=
# TWILIO_TOKEN=
# TWILIO_FROM=+1234567890
```

Either way every message is recorded in `sms_logs` and visible in the console under **SMS outbox** — useful when a patient insists they never got a reminder.

### Clinic rules

`config/clinic.php` holds the slow-moving rules: slot length, how far ahead people can book, minimum notice, and the window after which patients can no longer cancel online. Day-to-day settings a receptionist should own (phone number, opening hours, address, social links, SEO copy) live in the `settings` table and are edited in the console.

---

## How the permission system works

Three layers, in order of precedence:

1. **Owner bypass.** `Gate::before()` returns true for the owner role. There is no way to lock the clinic owner out of their own system.
2. **Role matrix.** Each user holds exactly one role; the role holds many permissions (`permission_role`). Roles are editable in the console — you can invent new ones.
3. **Per-person overrides.** The `permission_user` pivot carries a `granted` boolean that beats the role in either direction. This is how you give one receptionist billing access without giving it to all of them, or take blog access off one editor.

The resolved map is cached per user for 30 minutes and busted by `forgetPermissionCache()` whenever anything changes.

Use it three ways:

```php
$user->hasPermission('billing.manage');           // anywhere in PHP
Route::get(...)->middleware('can.do:billing.manage');
```

```blade
@permission('billing.manage') ... @endpermission
@role('owner', 'dentist') ... @endrole
@can('billing.manage') ... @endcan   {{-- every slug is registered as a Gate --}}
```

The full catalogue lives in `app/Support/Permissions.php` — about 30 slugs across eight groups. Add one there, re-run `php artisan db:seed --class=RolePermissionSeeder`, and it appears in the role editor.

---

## How availability works

`app/Services/SlotService.php` is the piece worth reading. It takes a practitioner and a date and returns bookable slots by subtracting, in order: days with no weekly schedule, full-day closures, lunch breaks, partial time-off windows, slots already taken, and anything starting sooner than the minimum-notice cutoff.

Nothing on the website is decorative — the "next free slot" chip on the homepage, the day strip on the booking form, and the ten-day panel on each practitioner profile all call the same service. Set a dentist's Wednesday to inactive and Wednesday disappears from the public site immediately.

Double-booking is prevented in two places: the service re-checks the slot inside `AppointmentService::book()`, and there's a unique index on `(doctor_id, scheduled_on, starts_at)` as the backstop.

---

## Project layout

```
app/
├── Console/Commands/     five scheduled commands (reminders, no-shows, digest, cleanup)
├── Exports/              Excel exports (appointments, patients, invoices, payments)
├── Http/
│   ├── Controllers/      Site/ Auth/ Patient/ Admin/
│   └── Middleware/       role, can.do, active, verified.by, guest.only, ShareSiteSettings
├── Jobs/                 queued SMS + newsletter sends
├── Mail/                 nine mailables
├── Models/               27 models
├── Services/             SlotService, AppointmentService, SmsService,
│                         PasswordResetService, VerificationService, DashboardMetrics
└── Support/              Permissions catalogue, HasRolesAndPermissions trait, Redirects

database/
├── migrations/           19 migrations
└── seeders/              nine seeders with realistic clinic data

resources/views/
├── layouts/              site, portal, console, auth
├── components/           ui/ and site/ Blade components
├── site/ auth/ patient/ admin/
├── emails/               nine responsive email templates
└── pdf/                  invoice, prescription, day sheet, exports, report

public/assets/            ivora.css (design layer), ivora.js (interaction), SVG logo
```

---

## Design notes

The brand is built around a single idea: **the dental arch as a layout device**. It frames the hero image, forms the section dividers between light and dark bands, shapes the practitioner portraits and gallery tiles, and curves behind the four booking steps. It is one shape, used consistently, rather than a pile of unrelated flourishes.

- **Palette** — ink `#0A1614`, pine `#10403B`, jade `#1C7A6E`, mint `#A8E6D8`, porcelain `#F4F7F5`, with iris `#4F5BD5` held back for data and accents. Porcelain and mint are enamel colours; the deep pine keeps it from reading as another sterile blue-and-white dental site.
- **Type** — Bricolage Grotesque for display (tight, slightly odd, not a default), Inter Tight for body copy, JetBrains Mono for references, times, prices and codes. Anything a patient might read aloud to reception is set in mono so it's unambiguous.
- **Motion** — reveals are staggered and short, the ticker drifts, counters count once. Everything collapses to nothing under `prefers-reduced-motion`.

---

## Tests

```bash
php artisan test
```

Covers public page loads, the custom login flow and its redirect targets, console access control, and the four-way behaviour of the permission system (owner bypass, role matrix, per-user grant, per-user revoke). They run against an in-memory SQLite database and seed themselves.

---

## Things worth knowing before you deploy

- Point the web root at `public/`, not the project root.
- `php artisan storage:link` must run on the server too, or uploaded images 404.
- Run the queue worker under a process supervisor (Supervisor, systemd, or `php artisan queue:work --daemon` behind one) — not in a terminal you'll close.
- The seeded demo images are hot-linked from Unsplash and randomuser.me. Replace them with real photographs before launch; every one is swappable from the console.
- `APP_DEBUG=false` in production, and set a real `APP_URL` since the mailables build absolute links from it.
