<?php

namespace Nonamechrs\GotenbergScreenshots;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Nonamechrs\GotenbergScreenshots\Exceptions\GotenbergException;

class GotenbergClient
{
    /**
     * Screenshot a full HTML document via Gotenberg's Chromium module and return the image bytes.
     *
     * @throws GotenbergException
     */
    public function screenshotHtml(string $html, string $format = 'png', int $width = 1200, int $height = 630): string
    {
        try {
            $response = Http::baseUrl(config('screenshots.gotenberg.url'))
                ->timeout(config('screenshots.gotenberg.timeout'))
                ->retry(2, 200, throw: false)
                ->attach('files', $html, 'index.html')
                ->post('/forms/chromium/screenshot/html', [
                    'format' => $format,
                    'width' => $width,
                    'height' => $height,
                    'optimizeForSpeed' => 'true',
                ]);
        } catch (ConnectionException $e) {
            throw new GotenbergException('Could not reach Gotenberg.', previous: $e);
        }

        if ($response->failed()) {
            throw new GotenbergException("Gotenberg responded with HTTP {$response->status()}.");
        }

        return $response->body();
    }
}
