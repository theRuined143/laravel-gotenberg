<?php

namespace Nonamechrs\GotenbergScreenshots\Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Nonamechrs\GotenbergScreenshots\Tests\TestCase;

class ScreenshotApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['screenshots.api_token' => 'secret', 'screenshots.gotenberg.url' => 'http://gotenberg:3000']);
        Storage::fake('local');
    }

    private function create(array $payload)
    {
        return $this->withToken('secret')->postJson('/gotenberg/screenshots', $payload);
    }

    public function test_requires_a_valid_token(): void
    {
        $this->postJson('/gotenberg/screenshots', [])->assertUnauthorized();
        $this->withToken('wrong')->postJson('/gotenberg/screenshots', [])->assertUnauthorized();
    }

    public function test_rejects_unknown_templates(): void
    {
        $this->create(['template' => '../../secrets'])->assertUnprocessable()->assertJsonValidationErrors('template');
    }

    public function test_creates_and_fetches_a_screenshot(): void
    {
        Http::fake(['gotenberg:3000/*' => Http::response('PNGBYTES', 200)]);

        $id = $this->create(['template' => 'custom', 'data' => ['name' => '<b>Hi</b>']])
            ->assertCreated()
            ->json('id');

        Http::assertSent(fn (Request $r) => $r->url() === 'http://gotenberg:3000/forms/chromium/screenshot/html'
            && str_contains($r->body(), '&lt;b&gt;Hi&lt;/b&gt;'));
        Storage::disk('local')->assertExists("screenshots/{$id}.png");

        $this->withToken('secret')->get("/gotenberg/screenshots/{$id}")
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    }

    public function test_app_templates_override_bundled_ones(): void
    {
        Http::fake(['gotenberg:3000/*' => Http::response('PNGBYTES', 200)]);

        $this->create(['template' => 'card'])->assertCreated();

        Http::assertSent(fn (Request $r) => str_contains($r->body(), 'overridden card'));
    }

    public function test_bundled_template_is_used_when_the_app_has_none(): void
    {
        $this->templatePaths = [];
        $this->refreshApplication();
        config(['screenshots.api_token' => 'secret']);
        Storage::fake('local');
        Http::fake(['gotenberg:3000/*' => Http::response('PNGBYTES', 200)]);

        $this->create(['template' => 'card', 'data' => ['title' => 'Hello']])->assertCreated();

        Http::assertSent(fn (Request $r) => str_contains($r->body(), '<h1>Hello</h1>'));
    }

    public function test_returns_502_when_gotenberg_fails(): void
    {
        Http::fake(['gotenberg:3000/*' => Http::response('boom', 500)]);

        $this->create(['template' => 'card'])->assertStatus(502);
    }

    public function test_unknown_screenshot_is_404(): void
    {
        $this->withToken('secret')->getJson('/gotenberg/screenshots/01JZZZZZZZZZZZZZZZZZZZZZZZ')->assertNotFound();
    }
}
