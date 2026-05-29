<?php

use Illuminate\Support\Facades\Route;

// SPA catch-all: serve React SPA with injected CSRF token
Route::get('{path?}', function () {
    $indexPath = public_path('app/index.html');

    if (!file_exists($indexPath)) {
        return response('Frontend not built. Run: cd frontend && npm run build', 500);
    }

    $html = file_get_contents($indexPath);

    // Inject CSRF token and API URL into <head>
    $inject = sprintf(
        '<meta name="csrf-token" content="%s"><meta name="api-url" content="%s">',
        csrf_token(),
        url('/api')
    );

    $html = str_replace('<head>', '<head>' . $inject, $html);

    return response($html)->header('Content-Type', 'text/html');
})->where('path', '.*');
