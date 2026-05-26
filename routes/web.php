<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\URLController;
use App\Models\ShortenedUrl;

Route::post('/api/v1/shorten', [URLController::class, 'shorten']);

Route::get('/s/{slug}', function ($slug) {
    $url = ShortenedUrl::where('slug', '=', $slug, 'and')->firstOrFail();
    return redirect($url->original_url);
});

include 'general.php';