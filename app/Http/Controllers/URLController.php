<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\URLShortenerService;

class URLController extends Controller
{
    protected URLShortenerService $urlShortenerService;

    public function __construct() {
        $this->urlShortenerService = new URLShortenerService();
    }

    public function shorten(Request $request) {
        $request->validate([
            'url' => 'required|url'
        ]);

        try {
            $slug = $this->urlShortenerService->shorten($request->input('url'));
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'shortenedUrl' => url('/s/' . $slug),
        ]);
    }

    public function test() {
        return true;
    }
}
