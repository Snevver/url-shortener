# url-shortener

A self-hosted URL shortener built with Laravel and Vue 3. Paste a long URL, get a short one. That's it.

Live at [url.snev.dev](https://url.snev.dev)

---

## What it does

- Shortens any URL to a 6-character slug
- Verifies the target URL actually exists before saving it
- Redirects visitors via `/s/{slug}`
- Re-uses slugs for duplicate URLs

## Stack

- **Backend:** Laravel 11, PHP
- **Frontend:** Vue 3 (Composition API), Tailwind CSS v4, Vite
- **Database:** MySQL / SQLite (anything Laravel supports)

## Setup

```bash
git clone https://github.com/Snevver/url-shortener
cd url-shortener

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`, then:

```bash
php artisan migrate
npm run build
php artisan serve
```

## Environment

The only variable that affects shortened URL output is `APP_URL`. Set it to your domain:

```
APP_URL=https://url.snev.dev
```

Shortened URLs will be generated as `APP_URL/s/{slug}`.

## API

**POST** `/api/v1/shorten`

| Field | Type   | Required |
|-------|--------|----------|
| url   | string | yes      |

Returns:
```json
{ "shortenedUrl": "https://url.snev.dev/s/a3f9c2" }
```

Errors return HTTP 422 with an `error` or `errors` field.

**GET** `/s/{slug}`

Redirects to the original URL. Returns 404 if the slug does not exist.

## Frontend

The UI was designed and built by [Claude Code](https://claude.ai/code). Dark brutalist aesthetic using Bebas Neue and IBM Plex Mono. No purple gradients.

## License

MIT
