<?php

namespace App\Http\Services;

use App\Models\ShortenedUrl;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class URLShortenerService
{
    public function shorten(string $url): string 
    {
        // Re-use the slug if this URL has already been shortened
        $existing = ShortenedUrl::where('original_url', '=', $url, 'and')->first();
        if ($existing) return $existing->slug;

        // Verify the URL actually exists before saving it
        $this->verifyUrlExists($url);

        // Generate a unique 6-char slug
        do {
            $slug = substr(md5($url . uniqid()), 0, 6);
        } while (ShortenedUrl::where('slug', '=', $slug, 'and')->exists());

        // Store in DB
        ShortenedUrl::create([
            'slug'         => $slug,
            'original_url' => $url,
        ]);

        return $slug;
    }

    private function verifyUrlExists(string $url): void 
    {
        try {
            $response = Http::timeout(5)
                ->withUserAgent('url.snev.dev/1.0')
                ->head($url);

            // Some servers don't support HEAD. Fall back to GET
            if ($response->status() === 405) {
                $response = Http::timeout(5)
                    ->withUserAgent('url.snev.dev/1.0')
                    ->get($url);
            }

            if ($response->clientError()) {
                throw new \RuntimeException(
                    "The URL returned a {$response->status()} error and could not be found."
                );
            }
        } catch (ConnectionException $e) {
            throw new \RuntimeException('The URL could not be reached. Check that it is correct.');
        }
    }
}