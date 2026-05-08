# CLAUDE.md — DOST-SDN PMIS Project Memory

This file orients Claude Code across the entire build of the **DOST Surigao del Norte Project Monitoring and Information System (PMIS)**. Read this before executing any task in this repository.

---

## Project Identity

| Field            | Value                                                                 |
|------------------|-----------------------------------------------------------------------|
| App Name         | DOST-SDN PMIS                                                         |
| Full Name        | Project Monitoring and Information System — DOST Surigao del Norte    |
| Agency           | Department of Science and Technology Caraga – PSTO Surigao del Norte |
| Programs Covered | SETUP, GIA, CEST                                                      |
| Purpose          | Monitor financial targets and physical accomplishments of DOST-funded projects with document-based verification before data is displayed |

---

## Tech Stack (Locked — Do Not Change)

| Layer            | Technology                                      |
|------------------|-------------------------------------------------|
| Framework        | Laravel 13, PHP 8.3+                            |
| Frontend         | Blade templates                                 |
| CSS              | Tailwind CSS v4 (CSS-first, no tailwind.config.js) |
| JS Interactivity | Alpine.js v3 (via CDN)                          |
| Charts           | ApexCharts (via CDN, initialized in Alpine x-init) |
| Reactive Tables  | Livewire v3                                     |
| Database         | MySQL 8.0+                                      |
| Auth             | Laravel Breeze (Blade stack)                    |
| Queue Driver     | database                                        |
| File Storage     | Laravel Storage, local disk                     |
| Excel            | maatwebsite/excel                               |
| PDF Export       | barryvdh/laravel-dompdf                         |
| PDF Parsing      | smalot/pdfparser                                |

---

## Laravel 13 Conventions — Strictly Follow These

- **No `Http/Kernel.php`** — middleware is registered in `bootstrap/app.php` via `->withMiddleware()`
- **No `api.php` by default** — run `php artisan install:api` only if API routes are needed
- **Middleware aliases** registered in `bootstrap/app.php`:
  ```php
  ->withMiddleware(function (Middleware $middleware) {
      $middleware->alias([
          'role' => \App\Http\Middleware\RoleMiddleware::class,
      ]);
  })
  ```
- **Custom service providers** listed in `bootstrap/providers.php`
- **Scheduled tasks** go in `routes/console.php` using `Schedule::` facade
- **Eloquent casts** use method syntax:
  ```php
  protected function casts(): array {
      return ['verified_at' => 'datetime'];
  }
  ```
- **Tailwind v4** uses CSS-first config — import in `resources/css/app.css`:
  ```css
  @import "tailwindcss";
  ```
  No `tailwind.config.js` needed unless customizing design tokens.
- Use `php artisan make:model ModelName --all` for full scaffold (migration + factory + seeder + controller + resource)

---

## Roles & Permissions

| Capability                      | Admin | Encoder | Verifier | Viewer |
|---------------------------------|:-----:|:-------:|:--------:|:------:|
| Manage projects                 | ✅    | ✅      | ❌       | ❌     |
| Upload project documents        | ✅    | ✅      | ❌       | ❌     |
| Encode financial targets        | ✅    | ✅      | ❌       | ❌     |
| Encode physical accomplishments | ✅    | ✅      | ❌       | ❌     |
| Verify / flag / reject entries  | ✅    | ❌      | ✅       | ❌     |
| View verified dashboards        | ✅    | ✅      | ✅       | ✅     |
| View unverified/pending entries | ✅    | ✅      | ✅       | ❌     |
| Manage users                    | ✅    | ❌      | ❌       | ❌     |
| Export reports                  | ✅    | ✅      | ✅       | ✅     |

**Critical rule:** Viewer role must NEVER see entries with `verified_status` of `pending`, `flagged`, or `rejected`. Enforce this at the Eloquent query level — not just the UI.

---

## Database: Key Tables & Rules

### Unique Composite Indexes (enforce at migration level)
```php
// financial_targets
$table->unique(['project_id', 'year', 'quarter', 'month', 'line_item']);

// physical_accomplishments
$table->unique(['project_id', 'year', 'quarter', 'month', 'indicator_name']);
```

### Money Fields
- Always `DECIMAL(15,2)` — never `FLOAT` or `DOUBLE` for financial data.

### Generated Column (DO NOT accept as user input)
```sql
-- physical_accomplishments
accomplishment_rate DECIMAL(5,2) GENERATED ALWAYS AS
  (ROUND((accomplished_value / NULLIF(target_value, 0)) * 100, 2)) STORED
```
- Exclude `accomplishment_rate` from `$fillable` on the model.
- Never include it in form requests or import column mappings.

### verified_status Enum Values
`pending` | `verified` | `flagged` | `rejected`
- All new entries default to `pending`.
- Only `verified` entries are visible to Viewer role.

### project_documents extras
- Add `extracted_text LONGTEXT NULL` column to store PDF text parsed by `smalot/pdfparser`.
- Populate `extracted_text` asynchronously via a queued job when a document is uploaded.

---

## File Storage & Security Rules

- **Never expose raw storage paths in Blade views.**
- All document downloads must go through:
  ```
  GET /documents/{id}/download  →  DocumentController@download
  ```
  Use `Storage::disk('local')->download()` with auth middleware protecting the route.
- Store uploads under `storage/app/projects/{project_id}/{document_type}/`.

---

## Verification Pipeline — Core Logic

### Flow
```
Encoder submits entry
  → entry saved with verified_status = 'pending'
  → RunVerificationChecks job dispatched to queue
    → VerificationService runs automated rule checks
    → Flags stored as notes on the entry
  → Verifier reviews queue (VerificationQueue Livewire component)
  → Verifier marks: Verified ✅ / Flagged ⚠️ / Rejected ❌
  → verification_logs row inserted (polymorphic)
  → EntryStatusChanged notification sent to Encoder (mail)
```

### VerificationService — Financial Target Rules
1. Entry period (year/quarter/month) is within `project.start_date` → `project.end_date`
2. Cumulative `target_amount` for line item does not exceed `total_approved_budget`
3. `obligated_amount <= target_amount`
4. `disbursed_amount <= obligated_amount`
5. Active GAA or financial_plan document exists for the project
6. No duplicate (enforced by DB unique index — surface friendly error in form)

### VerificationService — Physical Accomplishment Rules
1. Entry period within project timeline
2. `accomplished_value >= 0`
3. `accomplishment_rate > 150%` → auto-flag for review (do not reject)
4. `indicator_name` (case-insensitive) found in `project_documents.extracted_text` of work_plan type
5. No duplicate (enforced by DB unique index)

### Important: Auto-checks flag — they do NOT auto-reject. Human verifier has final say.

---

## Livewire Components

| Component                  | Purpose                                              |
|----------------------------|------------------------------------------------------|
| `ProjectTable`             | Filterable/sortable project list                     |
| `FinancialTargetTable`     | Filterable by year, quarter, month, program, status  |
| `PhysicalAccomplishmentTable` | Filterable by year, quarter, month, program, status |
| `VerificationQueue`        | Verifier's pending entry queue with action buttons   |

All Livewire tables must support:
- Column sorting (toggle asc/desc)
- Filters: year, quarter, month, program, project, verified_status
- Pagination (15 per page default)
- `wire:loading` spinner on filter/sort interactions

---

## Dashboard Charts (ApexCharts + Alpine.js)

Initialize charts inside Alpine `x-init` using data passed from the controller via `@json()`:

```html
<div x-data="{
    init() {
        new ApexCharts(this.$refs.chart, @json($chartOptions)).render();
    }
}" x-init="init()">
    <div x-ref="chart"></div>
</div>
```

### Required Charts
| Chart | Type | Scope |
|-------|------|-------|
| Financial target vs obligated vs disbursed | Stacked Bar | Per quarter, current year |
| Physical accomplishment rate trend | Line | Per month, current year |
| Project status distribution | Donut | All projects |
| Top 10 projects by accomplishment rate | Horizontal Bar | Current year |

Dashboard filters (year, quarter, program) use Livewire `wire:model` to re-fetch data and re-render charts.

---

## Report Exports

### Excel (maatwebsite/excel)
- `FinancialSummaryExport` — filters: program, project, year, quarter/month range
- `PhysicalAccomplishmentExport` — same filters
- `VerificationAuditExport` — filters: date range, verifier, action

### PDF (barryvdh/laravel-dompdf)
- Blade views at `resources/views/reports/financial-pdf.blade.php`, `physical-pdf.blade.php`, `audit-pdf.blade.php`
- Use inline CSS only in PDF Blade views (DomPDF does not support external CSS)
- Paper size: A4, landscape for wide tables

---

## UI Guidelines

| Token       | Value              |
|-------------|-------------------|
| Primary     | `#003087` (DOST Blue) |
| Accent      | `#FDB913` (Gold)  |
| Background  | `#F5F7FA`         |
| Surface      | `#FFFFFF`         |
| Text        | `#1A1A2E`         |

### Status Badges
```html
pending  → bg-gray-100  text-gray-600
verified → bg-green-100 text-green-700
flagged  → bg-amber-100 text-amber-700
rejected → bg-red-100   text-red-700
```

### Layout
- Sidebar: fixed left, collapsible on mobile
- Sidebar sections: Dashboard | SETUP | GIA | CEST | Verification | Reports | Users (admin only)
- Topbar: breadcrumb left, user name + role badge + logout right
- Flash messages: use `<x-alert>` Blade component (success / warning / error)

---

## Build Order — Follow This Sequence

Do not skip steps or build out of order. Each step depends on the previous.

```
Step 1  → Migrations (in FK dependency order)
Step 2  → Seeders (ProgramSeeder, UserSeeder, ProjectSeeder)
Step 3  → Auth (Breeze install, add role/office to users, RoleMiddleware, bootstrap/app.php)
Step 4  → Layout (app.blade.php sidebar, Tailwind v4, Alpine.js CDN, x-alert component)
Step 5  → Project Registry (CRUD, document upload, DocumentController download route)
Step 6  → PdfExtractorService + document text extraction job
Step 7  → VerificationService (all rule checks for both entry types)
Step 8  → RunVerificationChecks job (queued, dispatched on entry save)
Step 9  → Financial Target module (CRUD, FinancialTargetTable Livewire, Excel import)
Step 10 → Physical Accomplishment module (CRUD, PhysicalAccomplishmentTable Livewire, Excel import)
Step 11 → VerificationQueue Livewire component + EntryStatusChanged notification
Step 12 → Dashboard (ApexCharts, summary cards, program filter)
Step 13 → Reports (Excel exports, DomPDF PDF views, ReportController)
Step 14 → User Management (Admin CRUD)
Step 15 → Feature tests (VerificationService rules, role guards, duplicate detection)
```

---

## Commands Reference

```bash
# Install dependencies
composer require livewire/livewire maatwebsite/excel barryvdh/laravel-dompdf smalot/pdfparser

# Run migrations and seed
php artisan migrate --seed

# Run queue worker (required for verification jobs)
php artisan queue:work --queue=default

# Clear all caches during development
php artisan optimize:clear

# Run tests
php artisan test --filter VerificationServiceTest
```

---

## What NOT To Do

- ❌ Do not use `tailwind.config.js` — Tailwind v4 is CSS-first
- ❌ Do not register middleware in `Http/Kernel.php` — it does not exist in Laravel 13
- ❌ Do not put scheduled tasks in a service provider — use `routes/console.php`
- ❌ Do not use `FLOAT` or `DOUBLE` for any money column
- ❌ Do not accept `accomplishment_rate` as form input — it is a MySQL generated column
- ❌ Do not expose raw `storage/app` paths in Blade — always use `DocumentController@download`
- ❌ Do not show `pending`, `flagged`, or `rejected` entries to the Viewer role anywhere in the app
- ❌ Do not auto-reject entries in VerificationService — flags are warnings, not verdicts
- ❌ Do not use Vue.js or React — frontend is Blade + Alpine.js only
- ❌ Do not use Inertia.js — not in this stack

---

## Glossary

| Term | Meaning |
|------|---------|
| SETUP | Small Enterprise Technology Upgrading Program |
| GIA | Grant-in-Aid |
| CEST | Community Empowerment through Science and Technology |
| SSCP | Smart and Sustainable Communities Program |
| PPA | Program, Project, Activity |
| GAA | General Appropriations Act (budget document) |
| MOA | Memorandum of Agreement |
| PSTO | Provincial Science and Technology Office |
| SDN | Surigao del Norte |
| Encoder | Staff who inputs financial/physical data |
| Verifier | Staff who reviews and approves encoded data |
| verified_status | Entry lifecycle: pending → verified / flagged / rejected |
