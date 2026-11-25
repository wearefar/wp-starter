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

## Docs

- [docs/architecture.md](docs/architecture.md): component structure, ACF, translatable strings, code, frontend and accessibility conventions, and build/demo template routing.
- [docs/decisions.md](docs/decisions.md): decisions and open proposals. Read it before starting work; comment on a proposal in a *Comments* section under the entry.
- [docs/specifications.md](docs/specifications.md): what each content type and component shows, as the project defines it.
