<?php

namespace Nonamechrs\GotenbergScreenshots\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('screenshots.api_token');
        $given = (string) $request->bearerToken();

        // An unset token must never let everyone in.
        abort_if($expected === '' || ! hash_equals($expected, $given), 401, 'Unauthorized.');

        return $next($request);
    }
}
