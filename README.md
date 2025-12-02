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

## Hostinger (manual upload) notes
- Keep the repository structure exactly as-is when uploading through the File Manager (folders like `assets/`, `includes/`, `database/` stay at the same level as `index.php`).
- Upload the files into `public_html` or a subfolder such as `public_html/family-law-blog/` so links like `BASE_URL/post/slug` work.
- Create `config.php` from `config.sample.php` before uploading (or edit it in place) and set your `CRON_SECRET_TOKEN` and `BASE_URL`.
- Ensure the `database/` directory remains writable (755 is usually sufficient); the `blog.db` file will be created automatically on first run.
- If you cannot use SSH, you can zip the repo locally, upload the archive via File Manager, and extract it directly in the target directory to preserve the simple structure.

## File overview
- `index.php` — homepage listing recent posts.
- `post.php` — single post view.
- `cron.php` — generates a post from the next unused topic.
- `config.sample.php` — configuration template (copy to `config.php`).
- `topics.json` — seed topics used for automatic posts.
- `includes/db.php` — SQLite connection and schema setup.
- `includes/functions.php` — topic loading, content generation, and helpers.
- `assets/css/style.css` — minimal mobile-first styling.

## Notes
- Content generation uses deterministic scaffolding in `includes/functions.php`. Swap the `generate_content` logic to call an external model like Claude when credentials are available.
- If topics run out, add more to `topics.json` or reset the `topics` table.
- Clean URLs are enabled via `.htaccess` (e.g., `/post/your-slug`), while `post.php?slug=` continues to work if rewriting is disabled.
