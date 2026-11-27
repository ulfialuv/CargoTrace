# Deployment guide

CargoTrace ships as a single Docker image (`Dockerfile` in the repository root): PHP 8.4 + Apache, built from the GitHub repository by the hosting platform. The application needs **one external MySQL 8-compatible database** (the app uses MySQL JSON functions, so SQLite is only suitable for a quick smoke test).

## What the container does on every start (`docker-entrypoint.sh`)

1. Makes Apache listen on the platform's `PORT`.
2. Stops with a clear message if `APP_KEY` or the database settings are missing.
3. Caches Laravel configuration, routes and views.
4. Runs `php artisan migrate --force` (retried 5 times if the database is not ready yet).
5. Optionally seeds demo data when `RUN_SEED=true`.
6. Starts Apache.

Every line it prints starts with `[entrypoint]`, so it is easy to find in the platform logs.

## Environment variables

Set these in the hosting dashboard. **Never commit them** (`.env` is git-ignored).

| Variable | Example | Notes |
|---|---|---|
| `APP_KEY` | `base64:…` | Required. Generate locally: `php artisan key:generate --show` |
| `APP_ENV` | `production` | |
| `APP_DEBUG` | `false` | Never `true` on a public site |
| `APP_URL` | `https://your-app.example.app` | The public HTTPS address |
| `TRUSTED_PROXIES` | `*` | The platform terminates HTTPS in front of the container; without this, links and redirects use `http://` |
| `DB_CONNECTION` | `mysql` | |
| `DB_HOST` / `DB_PORT` | `db.example.com` / `3306` | |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | | From your database provider |
| `MYSQL_ATTR_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` | Only if the provider requires TLS |
| `LOG_CHANNEL` | `stderr` | Sends logs to the platform log viewer |
| `SESSION_DRIVER` / `CACHE_STORE` | `file` | Keeps the database free for real data |
| `QUEUE_CONNECTION` | `sync` | No queue worker is run |
| `SESSION_SECURE_COOKIE` | `true` | |
| `CARGOTRACE_DELAY_THRESHOLD_HOURS` | `12` | Your group's delay threshold |
| `GEOCODING_USER_AGENT` | `CargoTrace/1.0 (you@example.com)` | Nominatim requires an identifying User-Agent |
| `RUN_SEED` | `true` | Demo data on the first deploy; set to `false` afterwards |
| `DEMO_ALLOW_PRODUCTION` | `true` | Allows the demo seeder when `APP_ENV=production` (demo server only) |
| `DEMO_PASSWORD` | *(choose one)* | Password of the demo accounts |

## First deployment checklist

1. `Dockerfile`, `docker-entrypoint.sh` and `.dockerignore` are committed and pushed to GitHub.
2. A MySQL database exists and its host, name, user and password are at hand.
3. All variables above are set on the platform.
4. Deploy. A healthy start ends with `[entrypoint] Siap. Menjalankan Apache di port …`.
5. Open `/up` (health check) and `/login`, then sign in with a demo account (see the README).
6. Set `RUN_SEED=false` once the demo data exists.

## Troubleshooting

| Symptom in the log / browser | Cause | Fix |
|---|---|---|
| Build fails: no Dockerfile found | Files were not pushed | `git add Dockerfile docker-entrypoint.sh .dockerignore`, commit, push |
| `APP_KEY belum diisi` | Variable missing | Set `APP_KEY` |
| `[2002] Connection refused` / `getaddrinfo failed` | Wrong `DB_HOST`/`DB_PORT`, or the database is not reachable from the platform | Check host and port; allow external connections |
| `Access denied for user` | Wrong credentials | Check `DB_USERNAME` / `DB_PASSWORD` |
| TLS / SSL required error | Provider enforces TLS | Set `MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt` |
| Only `/` works, other pages return 404 | Apache ignores `.htaccess` | Rebuild with the current `Dockerfile` (it enables `AllowOverride All`) |
| Redirects to `http://`, mixed-content warnings, login loops | HTTPS terminated by the proxy | Set `TRUSTED_PROXIES=*` and an `https://` `APP_URL` |
| Blank page / error 500 | Application error | Set `LOG_CHANNEL=stderr` and read the platform logs (temporarily `APP_DEBUG=true` only if the site is private) |
| Data disappears after a restart | Running on SQLite inside the container | Use an external MySQL database |

## SQLite smoke test (not for the final demo)

`DB_CONNECTION=sqlite` makes the entrypoint create `database/database.sqlite` inside the container. The data is lost whenever the container is recreated or wakes from sleep, and the operator notification page uses MySQL-only JSON functions, so use this only to check that the image builds and starts.
