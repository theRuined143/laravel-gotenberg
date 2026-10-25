# Laravel Gotenberg Screenshots

Render a Blade template to HTML, have a [Gotenberg](https://gotenberg.dev) server screenshot it, and store the image.
You get a token-protected HTTP API out of the box, or you can use the client directly in your own code.

Requires PHP 8.3+, Laravel 11–13, and a reachable Gotenberg 8 instance.

## Install

```bash
composer require nonamechrs/laravel-gotenberg-screenshots
```

```dotenv
API_TOKEN=a-long-random-string
GOTENBERG_URL=http://gotenberg:3000
```

The service provider is auto-discovered. Optional publishing:

```bash
php artisan vendor:publish --tag=screenshots-config   # config/screenshots.php
php artisan vendor:publish --tag=screenshots-views    # resources/views/vendor/screenshots
```

## HTTP API

```
POST /gotenberg/screenshots        {"template":"card","data":{"title":"Hi"},"width":1200,"height":630,"format":"png"}
                                   -> 201 {"id","format","url"}
GET  /gotenberg/screenshots/{id}   -> the image
```

By default both need `Authorization: Bearer <API_TOKEN>`. An empty `API_TOKEN` rejects every request.
`format` is png, jpeg or webp; `width`/`height` are 100–4000. Gotenberg failures return `502`.

Change the prefix, or turn the routes off, in `config/screenshots.php` under `routes`. To use your app's own auth
(Sanctum, your own token middleware, ...) set `routes.middleware` to an array such as `['api', 'auth:sanctum']`; it
replaces the package's token middleware entirely. Leave it `null` to keep the default.

## Templates

Put your own Blade files in `resources/views/screenshots/`. Each receives `$data`, and `invoice.blade.php` is requested as `"template": "invoice"`.
They take precedence over the bundled templates (the package ships `card`), so a file named `card.blade.php` overrides it.
Use more or different folders via `templates.paths` in `config/screenshots.php`.
You can also publish the bundled ones with `--tag=screenshots-views` and edit `resources/views/vendor/screenshots`.
Only files in those folders can be requested, so callers can never send raw Blade.
The page is sent to Gotenberg as a standalone `index.html`: use inline CSS and data URIs or absolute URLs for assets.

## Without the API

```php
use Nonamechrs\GotenbergScreenshots\GotenbergClient;

$png = app(GotenbergClient::class)->screenshotHtml($html, 'png', 1200, 630); // throws GotenbergException
```

## Other settings

`SCREENSHOT_DISK` (default `local`), `GOTENBERG_TIMEOUT` (seconds, default 30).

## Tests

```bash
composer install && vendor/bin/phpunit
```
