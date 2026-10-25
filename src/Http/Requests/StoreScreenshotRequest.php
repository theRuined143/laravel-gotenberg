<?php

namespace Nonamechrs\GotenbergScreenshots\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;

class StoreScreenshotRequest extends FormRequest
{
    public const FORMATS = ['png', 'jpeg', 'webp'];

    /**
     * Template names are the Blade files in the package's (or the app's published) templates folder;
     * callers can never send raw Blade.
     */
    public static function templates(): array
    {
        return collect(View::getFinder()->getHints()['screenshots'] ?? [])
            ->map(fn ($path) => $path.'/templates')
            ->filter(fn ($dir) => is_dir($dir))
            ->flatMap(fn ($dir) => File::files($dir))
            ->map(fn ($f) => str($f->getFilename())->before('.blade.php')->toString())
            ->unique()
            ->values()
            ->all();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template' => ['required', 'string', Rule::in(self::templates())],
            'data' => ['sometimes', 'array'],
            'width' => ['sometimes', 'integer', 'between:100,4000'],
            'height' => ['sometimes', 'integer', 'between:100,4000'],
            'format' => ['sometimes', Rule::in(self::FORMATS)],
        ];
    }
}
