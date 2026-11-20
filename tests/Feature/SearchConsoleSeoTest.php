<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchConsoleSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_canonical_seo_metadata_and_structured_data(): void
    {
        config(['app.url' => 'https://digpowerx.com']);
        config(['services.google_search_console.verification_token' => null]);

        $response = $this->get('/')->assertOk();

        $response->assertSee('<link rel="canonical" href="https://digpowerx.com/">', false)
            ->assertSee('<meta name="robots" content="index, follow">', false)
            ->assertSee('<meta name="description"', false)
            ->assertSee('<meta property="og:url" content="https://digpowerx.com/">', false)
            ->assertSee('application/ld+json', false)
            ->assertDontSee('name="google-site-verification"', false);
    }

    public function test_homepage_renders_google_site_verification_meta_tag_when_configured(): void
    {
        config(['services.google_search_console.verification_token' => 'abc123verifyTOKEN']);

        $this->get('/')
            ->assertOk()
            ->assertSee('<meta name="google-site-verification" content="abc123verifyTOKEN">', false);
    }

    public function test_sitemap_xml_returns_valid_xml_with_canonical_url(): void
    {
        config(['app.url' => 'https://digpowerx.com']);

        $response = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $this->assertSame('https://digpowerx.com/', (string) $xml->url[0]->loc);
    }

    public function test_google_html_verification_route_matches_configured_file_only(): void
    {
        config(['services.google_search_console.verification_file' => null]);
        $this->get('/google123456abcdef.html')->assertNotFound();

        config(['services.google_search_console.verification_file' => 'google123456abcdef.html']);

        $this->get('/google123456abcdef.html')
            ->assertOk()
            ->assertSeeText('google-site-verification: google123456abcdef.html');

        $this->get('/googlewrongtoken.html')->assertNotFound();
    }

    public function test_robots_txt_declares_sitemap_and_disallows_api(): void
    {
        $contents = file_get_contents(public_path('robots.txt'));

        $this->assertIsString($contents);
        $this->assertStringContainsString('Disallow: /api/', $contents);
        $this->assertStringContainsString('Sitemap: https://digpowerx.com/sitemap.xml', $contents);
    }
}
