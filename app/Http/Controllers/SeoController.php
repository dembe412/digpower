<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Faq;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Throwable;

class SeoController extends Controller
{
    /**
     * Serve the XML sitemap for Google Search Console and other crawlers.
     */
    public function sitemap(): Response
    {
        $baseUrl = rtrim((string) config('app.url', 'https://digpowerx.com'), '/');
        $lastModified = $this->resolveLastModified()->toAtomString();

        $urls = [
            [
                'loc' => $baseUrl.'/',
                'lastmod' => $lastModified,
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
        ];

        $entries = collect($urls)->map(function (array $entry): string {
            $loc = htmlspecialchars($entry['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $lastmod = htmlspecialchars($entry['lastmod'], ENT_XML1 | ENT_QUOTES, 'UTF-8');

            return implode("\n", [
                '  <url>',
                "    <loc>{$loc}</loc>",
                "    <lastmod>{$lastmod}</lastmod>",
                "    <changefreq>{$entry['changefreq']}</changefreq>",
                "    <priority>{$entry['priority']}</priority>",
                '  </url>',
            ]);
        })->implode("\n");

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            ."<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
            ."{$entries}\n"
            ."</urlset>\n";

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Serve the Google Search Console HTML file verification response when configured.
     */
    public function googleVerification(string $token): Response
    {
        $configured = trim((string) config('services.google_search_console.verification_file', ''));

        if ($configured === '') {
            abort(404);
        }

        $expectedFile = str_starts_with($configured, 'google') ? $configured : 'google'.$configured;
        if (! str_ends_with($expectedFile, '.html')) {
            $expectedFile .= '.html';
        }

        $requestedFile = 'google'.$token.'.html';

        if (! hash_equals($expectedFile, $requestedFile)) {
            abort(404);
        }

        return response("google-site-verification: {$expectedFile}", 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    private function resolveLastModified(): Carbon
    {
        $timestamps = [];

        try {
            foreach ([Product::class, Announcement::class, Faq::class] as $model) {
                $latest = $model::query()->max('updated_at');
                if ($latest) {
                    $timestamps[] = Carbon::parse($latest);
                }
            }
        } catch (Throwable) {
            // Fall back to current timestamp if tables are unavailable.
        }

        if ($timestamps === []) {
            return now();
        }

        usort($timestamps, fn (Carbon $a, Carbon $b): int => $b->getTimestamp() <=> $a->getTimestamp());

        return $timestamps[0];
    }
}
