<?php

namespace Nonamechrs\GotenbergScreenshots\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Nonamechrs\GotenbergScreenshots\Exceptions\GotenbergException;
use Nonamechrs\GotenbergScreenshots\GotenbergClient;
use Nonamechrs\GotenbergScreenshots\Http\Requests\StoreScreenshotRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScreenshotController extends Controller
{
    public function store(StoreScreenshotRequest $request, GotenbergClient $gotenberg): JsonResponse
    {
        $input = $request->validated();
        $format = $input['format'] ?? 'png';

        $html = view('screenshots::templates.'.$input['template'], ['data' => $input['data'] ?? []])->render();

        try {
            $image = $gotenberg->screenshotHtml($html, $format, $input['width'] ?? 1200, $input['height'] ?? 630);
        } catch (GotenbergException $e) {
            report($e);

            return response()->json(['message' => 'Screenshot service unavailable.'], 502);
        }

        $id = (string) Str::ulid();
        $this->disk()->put($this->path($id, $format), $image);

        return response()->json([
            'id' => $id,
            'format' => $format,
            'url' => route('screenshots.show', $id),
        ], 201);
    }

    public function show(string $id): StreamedResponse
    {
        foreach (StoreScreenshotRequest::FORMATS as $format) {
            if ($this->disk()->exists($path = $this->path($id, $format))) {
                return $this->disk()->response($path, headers: ['Content-Type' => "image/{$format}"]);
            }
        }

        abort(404);
    }

    private function disk()
    {
        return Storage::disk(config('screenshots.disk'));
    }

    private function path(string $id, string $format): string
    {
        return config('screenshots.directory')."/{$id}.{$format}";
    }
}
