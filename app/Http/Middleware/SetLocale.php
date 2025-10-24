<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from Accept-Language header
        $locale = $request->header('Accept-Language', 'en');

        // Validate locale
        $availableLocales = config('app.available_locales', ['en', 'ar']);

        if (!in_array($locale, $availableLocales)) {
            $locale = config('app.fallback_locale', 'en');
        }

        // Set the application locale
        App::setLocale($locale);

        return $next($request);
    }
}
