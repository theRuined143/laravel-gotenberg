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
POST /api/screenshots        {"template":"card","data":{"title":"Hi"},"width":1200,"height":630,"format":"png"}
                             -> 201 {"id","format","url"}
GET  /api/screenshots/{id}   -> the image
```

Both need `Authorization: Bearer <API_TOKEN>`. An empty `API_TOKEN` rejects every request.
`format` is png, jpeg or webp; `width`/`height` are 100–4000. Gotenberg failures return `502`.

Change the prefix or middleware (e.g. use Sanctum instead of the token), or turn the routes off, in `config/screenshots.php`
under `routes`.

## Templates

Templates are Blade files in the `screenshots::templates` namespace and receive `$data`. The package ships `card`.
To add or override one, publish the views and edit `resources/views/vendor/screenshots/templates/<name>.blade.php`.
Only files in that folder can be requested, so callers can never send raw Blade.
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
