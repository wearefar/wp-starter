# Agent instructions

## Repository

- WordPress project managed with Composer; frontend assets are built with Vite.
- `public/` is the web root; WordPress core is in `public/wordpress/`.
- The theme in this repository is `public/themes/wp-starter/`.
- Frontend source is in `resources/`; project-level helpers are in `src/`.
- Do not hand-edit WordPress core, Composer/npm dependencies, or generated build output.

## Setup

- Requirements: PHP 8.4+ and MySQL 5.7+.
- Copy `.env.example` to `.env` and configure database credentials, WordPress authentication keys and salts, and any required plugin license keys.
- Point the web server document root to `public/`.

```sh
composer install
npm install
npm run dev
npm run build
```

For component structure, frontend conventions, and build/demo template routing, see [docs/architecture.md](docs/architecture.md).
