@php
    $baseUrl = rtrim((string) config('app.url', 'https://digpowerx.com'), '/');
    $canonicalUrl = $baseUrl.'/';
    $siteName = (string) config('platform.name', 'DigiPowerX');
    $metaDescription = 'DigiPowerX — Digital mineral mining products, live exchange rates, VIP memberships, and community referral rewards with transparent administration.';
    $ogImage = $baseUrl.'/images/products/mining/diamond.webp';
    $gscVerificationToken = trim((string) config('services.google_search_console.verification_token', ''));
    $structuredData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebSite',
                'name' => $siteName,
                'url' => $canonicalUrl,
                'description' => $metaDescription,
            ],
            [
                '@type' => 'Organization',
                'name' => $siteName,
                'url' => $canonicalUrl,
                'logo' => $ogImage,
            ],
        ],
    ];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#101312">
    <title>{{ $siteName }} — Digital Mining &amp; Community Rewards</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @if ($gscVerificationToken !== '')
    <meta name="google-site-verification" content="{{ $gscVerificationToken }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $siteName }} — Digital Mining &amp; Community Rewards">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $siteName }} — Digital Mining &amp; Community Rewards">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="48x48" href="/favicon-48x48.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-frame">
        <header class="topbar">
            <a class="brand" href="/" aria-label="DigiPowerX home">
                <span class="brand-mark"><i></i><i></i><i></i></span>
                <span class="brand-name">DigiPower<span>X</span></span>
            </a>
            <nav class="top-nav" aria-label="Main navigation">
                <button class="top-nav-item is-active" data-page="home"><span data-icon="home"></span><span>Home</span></button>
                <button class="top-nav-item" data-page="products"><span data-icon="vip"></span><span>Products</span></button>
                <button class="top-nav-item" data-page="mining"><span data-icon="mining"></span><span>Mining</span></button>
                <button class="top-nav-item" data-page="vip"><span data-icon="vip"></span><span>VIP</span></button>
                <button class="top-nav-item" data-page="chat"><span data-icon="chat"></span><span>Chat</span></button>
                <button class="top-nav-item" data-page="team"><span data-icon="team"></span><span>Team</span></button>
                <button class="top-nav-item" data-page="me"><span data-icon="user"></span><span>Account</span></button>
            </nav>
            <div class="topbar-actions">
                <button class="icon-button" id="theme-toggle" data-action="toggle-theme" aria-label="Switch theme" title="Switch theme"><span data-icon="theme"></span></button>
                <button class="icon-button" id="refresh-button" aria-label="Refresh dashboard" title="Refresh"><span data-icon="refresh"></span></button>
                <button class="identity-button" data-action="auth"><span class="avatar avatar-small" id="top-avatar">G</span><span id="top-identity">Sign in</span></button>
            </div>
        </header>

        <main id="page-content" class="page-content" aria-live="polite">
            <noscript>
                <section class="panel" style="margin:24px auto;max-width:720px;padding:24px;">
                    <h1>{{ $siteName }}</h1>
                    <p>{{ $metaDescription }}</p>
                    <p>Please enable JavaScript to use the full interactive DigiPowerX platform.</p>
                </section>
            </noscript>
        </main>

        <nav class="mobile-nav bottom-nav" aria-label="Mobile navigation">
            <button class="mobile-nav-item bottom-nav-item is-active" data-page="home"><span data-icon="home"></span><small>Home</small></button>
            <button class="mobile-nav-item bottom-nav-item" data-page="products"><span data-icon="vip"></span><small>Products</small></button>
            <button class="mobile-nav-item bottom-nav-item" data-page="mining"><span data-icon="mining"></span><small>Mining</small></button>
            <button class="mobile-nav-item bottom-nav-item" data-page="chat"><span data-icon="chat"></span><small>Chat</small></button>
            <button class="mobile-nav-item bottom-nav-item" data-page="team"><span data-icon="team"></span><small>Team</small></button>
            <button class="mobile-nav-item bottom-nav-item" data-page="me"><span data-icon="user"></span><small>Me</small></button>
        </nav>
    </div>
    <div class="modal-backdrop" id="modal-backdrop" hidden><section class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title"><button class="modal-close icon-button" data-action="close-modal" aria-label="Close dialog">×</button><div id="modal-content"></div></section></div>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
</body>
</html>
