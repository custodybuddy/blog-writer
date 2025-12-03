# Custody Buddy Blog (PHP + SQLite prototype)

This repository contains a minimal, mobile-first PHP blog that auto-creates posts from a predefined topic list. It uses SQLite for storage so it can run on simple hosting and be tested locally without extra services.

## Features
- Daily (or 5-minute test) post generation via `cron.php` secured by a secret token.
- Topics bootstrapped from `topics.json` on first run.
- Lightweight mobile-friendly layout inspired by calm, readable blogs.
- Posts include validation-first intros, 800–1200 word guidance, action steps, scripts, and Amazon affiliate book links.

## Quick start
1. Copy `config.sample.php` to `config.php` and adjust:
   - `DB_PATH` (default: `database/blog.db` resolved to an absolute path via `dirname(__FILE__)`)
   - `BASE_URL` (e.g., `https://example.com/family-law-blog/`)
   - `CRON_SECRET_TOKEN` (generate a 32-character value)
   - `SITE_NAME` and `TIMEZONE`
2. Ensure the `database/` directory exists (created automatically on first run) and is writable (755 for the folder, 644 or 666 for `blog.db`).
3. Serve the app with PHP (example: `php -S localhost:8000 -t .`).
4. Open the homepage: `BASE_URL`.
5. Trigger a post: visit `BASE_URL/cron.php?token=YOUR_TOKEN`.
6. To initialize locally without cron, run `php init-db.php`.

### Local development workflow
- Install PHP 8.1+ with the SQLite extension enabled (verify with `php -m | grep sqlite`).
- Seed topics and the database locally with `php init-db.php`; re-run the command any time you need a clean database.
- Run the built-in server in one terminal tab (`php -S localhost:8000 -t .`) and browse to `http://localhost:8000/`.
- Use `php cron.php token` locally to generate a post without the HTTP call; the script accepts the token as either the `token` query string or a positional argument.

### Scheduling post generation
- Cron-friendly invocation: `*/5 * * * * /usr/bin/php /path/to/cron.php YOUR_SECRET_TOKEN >/dev/null 2>&1`.
- The cron script is idempotent per topic: it will skip generating a new post if a topic is still marked as unused.
- When testing against a staging server, prefer a short schedule (e.g., every 5 minutes) and then switch to daily for production.

### Troubleshooting tips
- If you see `unable to open database file`, ensure the `database/` directory is writable by the web server user and that `DB_PATH` points to an absolute path.
- If posts fail to generate, confirm the `CRON_SECRET_TOKEN` matches between `config.php` and your request. The script will return HTTP 401 on a mismatch.
- When deploying behind HTTPS, set `BASE_URL` to the full canonical URL so the generated links in RSS or emails remain correct.

## Hostinger (manual upload) notes
- Keep the repository structure exactly as-is when uploading through the File Manager (folders like `assets/`, `views/`, `database/`, and `app/` stay at the same level as `index.php`).
- Upload the files into `public_html` or a subfolder such as `public_html/family-law-blog/` so links like `BASE_URL/post/slug` work.
- Create `config.php` from `config.sample.php` before uploading (or edit it in place) and set your `CRON_SECRET_TOKEN` and `BASE_URL`.
- Ensure the `database/` directory remains writable (755 is usually sufficient); the `blog.db` file will be created automatically on first run.
- If you cannot use SSH, you can zip the repo locally, upload the archive via File Manager, and extract it directly in the target directory to preserve the simple structure.

## File overview
- `index.php` — thin controller for the homepage listing recent posts.
- `post.php` — thin controller for the single post view.
- `cron.php` — controller that generates a post from the next unused topic.
- `bootstrap.php` — autoloader and shared bootstrap wiring.
- `config.sample.php` — configuration template (copy to `config.php`).
- `topics.json` — seed topics used for automatic posts.
- `app/Services/PostService.php` — business logic for content creation, sanitization, and retrieval.
- `app/Repositories/PostRepository.php` — persistence layer for posts.
- `app/Repositories/TopicRepository.php` — persistence layer for topics and seeding.
- `includes/db.php` — SQLite connection and schema setup.
- `views/` — view templates that assemble HTML for pages.
- `assets/css/style.css` — minimal mobile-first styling.

## Notes
- Content generation uses deterministic scaffolding in `App\Services\PostService`. Swap the generation logic to call an external model like Claude when credentials are available.
- If topics run out, add more to `topics.json` or reset the `topics` table.
- Clean URLs are enabled via `.htaccess` (e.g., `/post/your-slug`), while `post.php?slug=` continues to work if rewriting is disabled.
