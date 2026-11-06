# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

**iClix** is a Netflix-style film and series streaming site (a university project). `PRD.md`, written in Indonesian, is the source of truth for the product. Its useful sections are §5 requirements (F-01 to F-30), §9 database, §10 routes and §11 UI, including the hero spec. User-facing copy is Indonesian (`lang/id`, `lang/id.json`).

## Commands

```sh
composer run setup                              # install deps, create .env and key, migrate and seed, build assets
composer run dev                                # run the local dev processes (php artisan dev)
npm run build                                   # production asset build

php artisan test --compact                      # all tests
php artisan test --compact tests/Feature/PlayerTest.php
php artisan test --compact --filter=testName    # single test

vendor/bin/pint --dirty --format agent          # format changed PHP files
php artisan migrate:fresh --seed                # reset to the demo data
```

Seeded logins, both with the password `password`: `admin@iclix.test` and `member@iclix.test`.

## Where the code differs from the PRD

Follow the code; the PRD was written against older assumptions.

- **Stack:** Laravel 13 and PHP 8.3, while the PRD says Laravel 11/12 and PHP 8.2+.
- **Tailwind v4 and Breeze:** Breeze's `breeze:install` would downgrade to Tailwind v3 and overwrite `vite.config.js`, so never run it. Its auth stubs were copied in by hand. Tailwind is CSS-first, with tokens in `@theme` in `resources/css/app.css` and no `tailwind.config.js`.
- **Database:** SQLite in `.env` and in tests, while the PRD targets MySQL. Keep migrations and queries portable.
- **Additions:** `contents.cast_members` (F-16 needs cast but §9 omits it), `POST /ratings/{content}`, `/trending` and `/genres`.
- **Route names:** the PRD's `admin.movies.index` example does not exist; content is managed under `admin.contents.*`.

## Architecture

**Rendering:** pages are server-rendered Blade. Alpine.js handles interaction and calls JSON endpoints for My List, ratings, watch progress and live search. JS lives in `resources/js`: `components.js` (Alpine components), `stores.js` (toast, myList, ratings, detail modal), `http.js` (CSRF fetch helper) and `parallax.js`.

**Access control** is layered through middleware registered in `bootstrap/app.php`:

| Middleware | Checks |
| --- | --- |
| `auth` | User is logged in |
| `admin` | User has the admin role |
| `subscribed` | Active subscription; otherwise redirect to `/plans` (403 for JSON) |
| `profile.selected` | A profile of this user is in the session; otherwise redirect to `/profiles` (403 for JSON) |
| `EnsureAccountIsActive` | Appended to the `web` group; ends the session of a deactivated account |

**Current profile:** `EnsureProfileIsSelected` binds the session's profile into the container, so controllers behind it type-hint `Profile $profile`. When a route also has an optional parameter, declare it last with a default: `(Content $content, Profile $profile, ?Episode $episode = null)`.

**Kids profiles:** every member-facing read must respect them, using `Content::visibleTo($profile)` for queries and `$content->isVisibleTo($profile)` for route-bound models. Hidden titles answer 404.

**Data model:**

- Films and series share one `contents` table, split by `type`. The video file lives on `contents.video_path` for films and `episodes.video_path` for series.
- `subscriptions` belong to a **user**, while `watch_histories`, `watchlists` and `ratings` belong to a **profile**. Each user has at most 4 profiles.
- `role` and `is_active` are deliberately not mass assignable; set them with `forceFill`.

**Files:** `App\Support\ContentMedia` stores images on the `public` disk and videos on the `local` disk (`storage/app/private`). Videos never get a public URL: `StreamController` serves them behind `subscribed` and relies on `BinaryFileResponse` for `Range` / 206 support. Paths under `demo/` are shared by seeded records, and `ContentMedia` never deletes them.

**Demo data:** `database/seeders/demo/` holds `catalog.php`, generated artwork and `sample.mp4`. `ContentSeeder` publishes these to the disks and runs `storage:link`.

**Out of scope for v1:** real payment gateways (checkout is simulated), HLS/DASH, DRM and native apps.

## Parallax

Use the `x-parallax` directive from `resources/js/parallax.js` rather than adding scroll listeners:

```blade
<section data-parallax-root class="relative overflow-hidden">
    <div x-parallax="{ scroll: 0.28, pointer: 16, origin: 'top' }" class="absolute -inset-x-8 -top-12 -bottom-40">...</div>
</section>
```

- `scroll` is how far the layer lags behind the page, `pointer` is the pixels it follows the cursor, `origin: 'top'` is for heroes at the top of a page, and `fade` fades it out while scrolling away.
- Make layers larger than the root so moving them never exposes an edge.
- Don't put `x-parallax` and an `x-transition` on the same element; both write `transform`.
- Layers are skipped entirely under `prefers-reduced-motion`.

## Repo gotchas

- Alpine ignores directives outside an `x-data` scope, so layouts set `x-data` on `<body>`. A new standalone page needs the same, or its `x-parallax` and `x-reveal` silently do nothing.
- Blade compiles `@error` as a directive, so write `x-on:error` in templates instead of Alpine's `@error` shorthand.
- Pass dynamic strings to components as `:label="'Hapus '.$title"`. Writing `label="Hapus {{ $title }}"` escapes the value twice.
- `AGENTS.md` is an outdated Boost bootstrap stub. Boost is already installed, so don't follow its setup steps.
- `boost:update` runs after `composer update` and rewrites everything from the first Boost opening tag to its closing tag. Keep project notes above that block and never write the literal tag in them, or the notes get swallowed.

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.3. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
