<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#101312">
    <title>DigiPowerX</title>
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
                <button class="top-nav-item" data-page="admin" data-admin-only hidden><span data-icon="settings"></span><span>Admin</span></button>
            </nav>
            <div class="topbar-actions">
                <button class="icon-button" id="theme-toggle" data-action="toggle-theme" aria-label="Switch theme" title="Switch theme"><span data-icon="theme"></span></button>
                <button class="icon-button" id="refresh-button" aria-label="Refresh dashboard" title="Refresh"><span data-icon="refresh"></span></button>
                <button class="identity-button" data-action="auth"><span class="avatar avatar-small" id="top-avatar">G</span><span id="top-identity">Sign in</span></button>
            </div>
        </header>

        <main id="page-content" class="page-content" aria-live="polite"></main>

        <nav class="mobile-nav bottom-nav" aria-label="Mobile navigation">
            <button class="mobile-nav-item bottom-nav-item is-active" data-page="home"><span data-icon="home"></span><small>Home</small></button>
            <button class="mobile-nav-item bottom-nav-item" data-page="products"><span data-icon="vip"></span><small>Products</small></button>
            <button class="mobile-nav-item bottom-nav-item" data-page="mining"><span data-icon="mining"></span><small>Mining</small></button>
            <button class="mobile-nav-item bottom-nav-item" data-page="chat"><span data-icon="chat"></span><small>Chat</small></button>
            <button class="mobile-nav-item bottom-nav-item" data-page="team"><span data-icon="team"></span><small>Team</small></button>
            <button class="mobile-nav-item bottom-nav-item" data-page="me"><span data-icon="user"></span><small>Me</small></button>
            <button class="mobile-nav-item bottom-nav-item" data-page="admin" data-admin-only hidden><span data-icon="settings"></span><small>Admin</small></button>
        </nav>
    </div>
    <div class="modal-backdrop" id="modal-backdrop" hidden><section class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title"><button class="modal-close icon-button" data-action="close-modal" aria-label="Close dialog">×</button><div id="modal-content"></div></section></div>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
</body>
</html>
