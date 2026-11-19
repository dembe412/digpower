const icons = {
	home: '<svg viewBox="0 0 24 24"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/></svg>',
	vip: '<svg viewBox="0 0 24 24"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9z"/></svg>',
	mining: '<svg viewBox="0 0 24 24"><path d="m14 4 6 6M4 20l8.4-8.4M8 5l11 11M5 8l11 11M3 21l4-4"/><path d="m13 3 2-2 6 6-2 2"/></svg>',
	team: '<svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5.5a3 3 0 0 1 0 5.8M18 14a5 5 0 0 1 3 4.6V20"/></svg>',
	user: '<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>',
	refresh: '<svg viewBox="0 0 24 24"><path d="M20 7v5h-5M4 17v-5h5"/><path d="M5.5 9A7 7 0 0 1 18 6l2 2M4 16l2 2a7 7 0 0 0 12.5-3"/></svg>',
	eye: '<svg viewBox="0 0 24 24"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>',
	deposit: '<svg viewBox="0 0 24 24"><path d="M12 3v12m-5-5 5 5 5-5"/><path d="M4 17v3h16v-3"/></svg>',
	withdraw: '<svg viewBox="0 0 24 24"><path d="M12 16V4m-5 5 5-5 5 5"/><path d="M4 17v3h16v-3"/></svg>',
	transfer: '<svg viewBox="0 0 24 24"><path d="M4 7h15l-3-3m4 13H5l3 3"/></svg>',
	statement: '<svg viewBox="0 0 24 24"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2zM9 8h6M9 12h6M9 16h3"/></svg>',
	arrow: '<svg viewBox="0 0 24 24"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>',
	lock: '<svg viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3m-4 5v2"/></svg>',
	help: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9.6 9a2.5 2.5 0 1 1 4.6 1.4c-.9 1.1-2.2 1.3-2.2 3M12 17.5h.01"/></svg>',
	bell: '<svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>',
	shield: '<svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"/><path d="m9 12 2 2 4-4"/></svg>',
	event: '<svg viewBox="0 0 24 24"><path d="M5 4h14v17H5zM8 2v4m8-4v4M8 10h8M8 14h5"/></svg>',
	about: '<svg viewBox="0 0 24 24"><path d="M4 20V5l8-2 8 2v15H4zM8 8h8M8 12h8M8 16h5"/></svg>',
	app: '<svg viewBox="0 0 24 24"><path d="M7 3h10v18H7zM10 6h4M10 18h4"/></svg>',
	invite: '<svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 20v-1a6 6 0 0 1 12 0M16 11h5m-2.5-2.5v5"/></svg>',
	settings: '<svg viewBox="0 0 24 24"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/><path d="m19.4 15 .1.1-1.6 2.8-.2-.1a2 2 0 0 0-2.8 1.1V19h-3.2v-.2a2 2 0 0 0-2.8-1.1l-.2.1-1.6-2.8.1-.1a2 2 0 0 0 0-3.2l-.1-.1 1.6-2.8.2.1a2 2 0 0 0 2.8-1.1V7h3.2v.2a2 2 0 0 0 2.8 1.1l.2-.1 1.6 2.8-.1.1a2 2 0 0 0 0 3.2z"/></svg>',
	theme: '<svg viewBox="0 0 24 24"><path d="M20.5 15.5A8.5 8.5 0 0 1 8.5 3.5 8.5 8.5 0 1 0 20.5 15.5z"/></svg>',
	chat: '<svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
	paperclip: '<svg viewBox="0 0 24 24"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>',
	send: '<svg viewBox="0 0 24 24"><path d="m22 2-11 11M22 2l-7 20-4-9-9-4 20-7z"/></svg>',
	image: '<svg viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>',
	reply: '<svg viewBox="0 0 24 24"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>',
	trash: '<svg viewBox="0 0 24 24"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>',
	at: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"/></svg>',
	camera: '<svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>',
};

let savedTheme = localStorage.getItem('dpx-theme');
if (savedTheme === 'light' && !sessionStorage.getItem('dpx_explicit_light')) {
	localStorage.removeItem('dpx-theme');
	savedTheme = null;
}
const initialTheme = savedTheme === 'light' ? 'light' : 'dark';
document.documentElement.dataset.theme = initialTheme;
const state = {
	token: sessionStorage.getItem('dp_token'),
	page: 'home',
	profile: null,
	home: null,
	prices: [],
	vip: null,
	products: [],
	ownedProducts: [],
	mining: null,
	miningTab: 'arena',
	miningHistory: null,
	miningRounds: null,
	miningPollTimer: null,
	stakeInputAmount: '10',
	miningLastStatus: null,
	team: null,
	teamLevel: 1,
	teamMembers: null,
	teamMembersPage: 1,
	miningHistoryPage: 1,
	miningRoundsPage: 1,
	adminClientPage: 1,
	adminTxStatus: 'pending',
	adminDepositPage: 1,
	adminWithdrawalPage: 1,
	adminPurchasePage: 1,
	hiddenBalance: false,
	modal: null,
	homeRefreshTimer: null,
	chatType: 'group',
	chatTargetUser: null,
	chatMessages: [],
	chatPollTimer: null,
	chatReplyingTo: null,
	chatAttachmentFile: null,
	chatAttachmentPreview: null,
	chatMembersList: [],
	chatShowTags: false,
};
const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
const safe = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char]);
const icon = (name) => icons[name] || '';
const amount = (value, digits = 2) => { const number = Number(value); return Number.isFinite(number) ? number.toLocaleString('en-US', { minimumFractionDigits: digits, maximumFractionDigits: digits }) : '—'; };
const maskedAmount = (value) => state.hiddenBalance ? '••••••' : amount(value);
const productImages = {
	1: '/images/products/mining/bronze.webp',
	2: '/images/products/mining/graphite.webp',
	3: '/images/products/mining/silver.webp',
	4: '/images/products/mining/gold.webp',
	5: '/images/products/mining/diamond.webp',
	6: '/images/products/mining/monaco-diamond.webp',
	7: '/images/products/mining/colored-diamond.webp',
	8: '/images/products/mining/green-mineral.webp',
};

async function api(path, options = {}) {
	const headers = { Accept: 'application/json', ...(options.body && !(options.body instanceof FormData) ? { 'Content-Type': 'application/json' } : {}), ...options.headers };
	if (state.token) headers.Authorization = `Bearer ${state.token}`;
	const response = await fetch(`/api/${path.replace(/^\//, '')}`, { ...options, headers });
	const data = await response.json().catch(() => ({}));
	if (!response.ok) {
		if (response.status === 401 && state.token) clearSession();
		throw new Error(data.errors ? Object.values(data.errors).flat().join(' ') : data.message || `Request failed (${response.status})`);
	}
	return data;
}

function renderIcons() { $$('[data-icon]').forEach((element) => { element.innerHTML = icon(element.dataset.icon); }); }
function updateThemeToggle() {
	const light = document.documentElement.dataset.theme === 'light';
	const toggle = $('#theme-toggle');
	if (!toggle) return;
	toggle.setAttribute('aria-label', light ? 'Switch to dark theme' : 'Switch to light theme');
	toggle.title = light ? 'Switch to dark theme' : 'Switch to light theme';
	toggle.innerHTML = icon('theme');
}
function toggleTheme() {
	const current = document.documentElement.dataset.theme === 'light' ? 'light' : 'dark';
	const theme = current === 'light' ? 'dark' : 'light';
	document.documentElement.dataset.theme = theme;
	localStorage.setItem('dpx-theme', theme);
	if (theme === 'light') {
		sessionStorage.setItem('dpx_explicit_light', '1');
	} else {
		sessionStorage.removeItem('dpx_explicit_light');
	}
	updateThemeToggle();
}
function setNav() { $$('[data-page]').forEach((button) => button.classList.toggle('is-active', button.dataset.page === state.page)); }
function updateIdentity() {
	const identity = state.profile?.identity_masked || 'Guest account';
	const initial = identity[0]?.toUpperCase() || 'G';
	const avatarUrl = state.profile?.avatar_url;
	const avatarHtml = avatarUrl ? `<img src="${safe(avatarUrl)}" alt="Avatar">` : initial;
	if ($('#sidebar-identity')) $('#sidebar-identity').textContent = identity;
	if ($('#sidebar-status')) $('#sidebar-status').textContent = state.profile ? `VIP ${state.home?.vip?.level ?? 'member'}` : 'Sign in to connect';
	if ($('#sidebar-avatar')) $('#sidebar-avatar').innerHTML = avatarHtml;
	if ($('#top-avatar')) $('#top-avatar').innerHTML = avatarHtml;
	if ($('#top-identity')) $('#top-identity').textContent = state.profile ? identity : 'Sign in';
	const topIdentityBtn = $('.identity-button');
	if (topIdentityBtn) {
		topIdentityBtn.classList.toggle('is-signed-in', !!state.profile);
		topIdentityBtn.title = state.profile ? `Signed in as ${identity} (Click to manage)` : 'Click to sign in';
	}
	$$('[data-admin-only]').forEach((element) => { element.hidden = !state.profile?.is_admin; });
}
function heading(kicker, title, subtitle, trailing = '') { return `<div class="page-heading page-enter"><div><p class="eyebrow">${safe(kicker)}</p><h1>${safe(title)}</h1><p class="page-subtitle">${safe(subtitle)}</p></div>${trailing}</div>`; }
function pageLoading() { $('#page-content').innerHTML = '<div class="loading-line" aria-label="Loading"></div>'; }
function emptyPanel(message) { return `<div class="empty-state">${safe(message)}</div>`; }
function renderPagination(paginator, action, extraDataset = {}) {
	if (!paginator || typeof paginator.total === 'undefined' || paginator.total <= 0) return '';
	const current = Number(paginator.current_page || 1);
	const last = Number(paginator.last_page || 1);
	const from = paginator.from ?? 1;
	const to = paginator.to ?? paginator.total;
	const total = paginator.total;

	if (last <= 1) {
		return `
			<div class="pagination-wrap">
				<div class="pagination-summary">Showing <strong>${from}</strong>–<strong>${to}</strong> of <strong>${total}</strong> entries</div>
			</div>
		`;
	}

	const extraAttrs = Object.entries(extraDataset).map(([k, v]) => `data-${k}="${safe(v)}"`).join(' ');

	const pages = [];
	const delta = 2;
	const left = Math.max(1, current - delta);
	const right = Math.min(last, current + delta);

	for (let i = 1; i <= last; i++) {
		if (i === 1 || i === last || (i >= left && i <= right)) {
			pages.push(i);
		} else if (pages[pages.length - 1] !== '...') {
			pages.push('...');
		}
	}

	const pageButtons = pages.map((p) => {
		if (p === '...') return `<span class="pagination-ellipsis">…</span>`;
		const active = p === current ? 'is-active' : '';
		return `<button type="button" class="pagination-btn ${active}" data-action="${safe(action)}" data-page="${p}" ${extraAttrs}>${p}</button>`;
	}).join('');

	const prevDisabled = current <= 1 ? 'disabled' : '';
	const nextDisabled = current >= last ? 'disabled' : '';

	return `
		<div class="pagination-wrap">
			<div class="pagination-summary">Showing <strong>${from}</strong>–<strong>${to}</strong> of <strong>${total}</strong> entries</div>
			<div class="pagination-nav">
				<button type="button" class="pagination-btn" data-action="${safe(action)}" data-page="${current - 1}" ${prevDisabled} ${extraAttrs}>« Prev</button>
				${pageButtons}
				<button type="button" class="pagination-btn" data-action="${safe(action)}" data-page="${current + 1}" ${nextDisabled} ${extraAttrs}>Next »</button>
			</div>
		</div>
	`;
}
function withdrawalRows(rows = []) {
	return rows.map((row) => {
		const avatar = `<span class="withdraw-avatar withdraw-avatar-${safe(row.avatar_variant || 1)}">${row.avatar_url ? `<img src="${safe(row.avatar_url)}" alt="">` : safe(row.avatar_initial || '?')}</span>`;
		return `<div class="withdraw-row">${avatar}<div class="withdraw-member"><strong>${safe(row.user)}</strong><span>withdrew</span></div><b>${amount(row.amount)} UGX</b></div>`;
	}).join('');
}
function sessionPrompt() { return state.profile ? '' : `<button class="connect-inline" data-action="auth">${icon('lock')} Sign in to load your account data ${icon('arrow')}</button>`; }

function renderHome() {
	const home = state.home;
	const wallet = home?.wallets;
	const vip = home?.vip;
	const balance = (value) => wallet ? maskedAmount(value) : '—';
	const forex = home?.forex || state.forexRates;
	const usdRate = Number(forex?.rates?.USD?.rate || forex?.USD_UGX || 3750);
	const eurRate = Number(forex?.rates?.EUR?.rate || forex?.EUR_UGX || 4050);
	const eurUsdRate = Number(forex?.EUR_USD || (usdRate > 0 ? eurRate / usdRate : 1.08));
	const usdChange = forex?.rates?.USD?.change_pct ?? 0.25;
	const eurChange = forex?.rates?.EUR?.change_pct ?? 0.18;

	const miningForexCard = `
		<article class="market-asset forex-asset-card">
			<div class="market-asset-title">
				<span class="coin-badge forex-badge" style="background:#d3ef7520;color:var(--lime);border-color:#d3ef7550;font-weight:700;">$€</span>
				<strong>USD &amp; EUR Mining Currencies</strong>
				<small>/UGX · Live Exchange Rates</small>
				<button class="text-button" data-action="forex-convert-modal" style="margin-left:auto;font-size:11px;font-weight:700;color:var(--lime);padding:3px 9px;border:1px solid var(--line);border-radius:4px;background:#171c19;">Convert ⇄</button>
			</div>
			<div class="exchange-grid">
				<div class="exchange-card">
					<strong>Mining USD · Central Rate</strong>
					<span>${amount(usdRate, 0)} UGX</span>
					<small class="${usdChange >= 0 ? 'positive' : 'negative'}">${usdChange >= 0 ? '+' : ''}${safe(usdChange)}% · 1 USD</small>
				</div>
				<div class="exchange-card">
					<strong>Mining EUR · Central Rate</strong>
					<span>${amount(eurRate, 0)} UGX</span>
					<small class="${eurChange >= 0 ? 'positive' : 'negative'}">${eurChange >= 0 ? '+' : ''}${safe(eurChange)}% · 1 EUR</small>
				</div>
				<div class="exchange-card">
					<strong>EUR / USD · Market Cross</strong>
					<span>$${amount(eurUsdRate, 4)}</span>
					<small class="positive">+0.05% · 1 EUR</small>
				</div>
				<div class="exchange-card">
					<strong>USD / USDT · Mining Parity</strong>
					<span>1.0000 USDT</span>
					<small class="positive">1:1 Parity</small>
				</div>
			</div>
		</article>
	`;

	const topMinersList = home?.top_miners || [];
	const podiumMiners = topMinersList.slice(0, 3);

	const podiumHtml = podiumMiners.length ? `
		<div class="miners-grid-podium">
			${podiumMiners.map((m) => `
				<div class="podium-card rank-${m.rank}">
					<span class="podium-rank-badge">${m.rank === 1 ? '🥇 #1' : m.rank === 2 ? '🥈 #2' : '🥉 #3'}</span>
					<div class="podium-miner-meta">
						<span class="withdraw-avatar withdraw-avatar-${m.avatar_variant || 1}">
							${m.avatar_url ? `<img src="${safe(m.avatar_url)}" alt="">` : safe(m.avatar_initial || 'M')}
						</span>
						<div style="min-width:0;">
							<div class="podium-miner-name">${safe(m.user)}</div>
							<div class="podium-machine">${safe(m.machine)}</div>
						</div>
					</div>
					<div class="podium-stats">
						<div><span>Daily:</span> <strong>+${safe(m.daily_output)}</strong></div>
						<div><span>Hashrate:</span> <b style="color:var(--lime);">${safe(m.hashrate)}</b></div>
					</div>
				</div>
			`).join('')}
		</div>
	` : '';

	const minerRowsHtml = topMinersList.map((m) => `
		<div class="miner-row">
			<span class="miner-rank rank-${m.rank}">${m.rank === 1 ? '🥇' : m.rank === 2 ? '🥈' : m.rank === 3 ? '🥉' : `#${m.rank}`}</span>
			<div class="miner-info">
				<span class="withdraw-avatar withdraw-avatar-${m.avatar_variant || 1}" style="width:28px;height:28px;font-size:10px;">
					${m.avatar_url ? `<img src="${safe(m.avatar_url)}" alt="">` : safe(m.avatar_initial || 'M')}
				</span>
				<strong>${safe(m.user)}</strong>
			</div>
			<div class="miner-equipment">
				<strong>${safe(m.machine)}</strong>
				<small>${safe(m.hashrate)}</small>
			</div>
			<div class="miner-output">
				<strong>+${safe(m.daily_output)}</strong>
				<small>Daily output</small>
			</div>
			<div class="miner-total">
				<b style="color:var(--ink);">${safe(m.total_mined)}</b>
				<small style="display:block;color:var(--muted);font-size:9px;">Total mined</small>
			</div>
			<div class="miner-status">
				<span class="tag tag-good" style="font-size:9px;padding:3px 6px;">⚡ Mining</span>
			</div>
		</div>
	`).join('');

	const topMinersCard = `
		<article class="top-miners-card">
			<div class="top-miners-header">
				<div class="top-miners-title">
					<span class="coin-badge" style="background:#25D36620;color:#25D366;border-color:#25D36650;">⚡</span>
					<div>
						<h3>Top Miners &amp; Hashrate Leaders</h3>
						<p>Real-time equipment hashrate and mining output</p>
					</div>
				</div>
				<button class="primary-button" data-page="products" style="padding:6px 14px;font-size:11px;min-height:30px;">Deploy Equipment ⚡</button>
			</div>
			${podiumHtml}
			<div class="miners-list-table">
				${minerRowsHtml || emptyPanel('No active miners reporting yet.')}
			</div>
		</article>
	`;
	const ticker = home?.ticker?.length ? `<div class="announcement-ticker"><span>${icon('bell')}</span><span>${safe(home.ticker[0])}</span></div>` : '';
	const withdrawals = withdrawalRows(home?.withdraw_log || []);
	const greeting = state.profile ? `Welcome back, ${safe((state.profile.identity_masked || 'member').split('@')[0])}` : 'Build your digital future';
	const heroActions = state.profile
		? `<button class="hero-button hero-button-primary" data-page="products">Explore products ${icon('arrow')}</button><button class="hero-button hero-button-secondary" data-page="mining">View mining ${icon('arrow')}</button>`
		: `<button class="hero-button hero-button-primary" data-action="auth">Join DigiPowerX ${icon('arrow')}</button><button class="hero-button hero-button-secondary" data-page="products">Explore products ${icon('arrow')}</button>`;
	$('#page-content').innerHTML = `<section class="welcome-hero has-video page-enter"><video class="hero-video" src="/videos/mining-bg.mp4" autoplay muted loop playsinline preload="auto" aria-hidden="true"></video><div class="hero-video-shade"></div><div class="hero-glow"></div><div class="hero-copy"><span class="hero-kicker"><i></i> Digital growth, made clearer</span><h1>${greeting}</h1><p>Discover mineral products, transparent manual administration and a growing digital ecosystem built to help you move with confidence.</p><div class="hero-actions">${heroActions}</div><div class="hero-trust"><span>${icon('shield')} Manual review</span><span>${icon('eye')} Clear balances</span><span>${icon('team')} Community rewards</span></div></div><div class="hero-orbit"><div class="orbit-ring orbit-ring-one"></div><div class="orbit-ring orbit-ring-two"></div><div class="orbit-core"><span>DPX</span><small>POWER</small></div><span class="orbit-tag orbit-tag-one">PRODUCTS</span><span class="orbit-tag orbit-tag-two">MINING</span><span class="orbit-tag orbit-tag-three">TEAM</span></div></section>
		${ticker}
		<section class="welcome-strip page-enter"><div><span class="eyebrow">Start here</span><h2>One platform. More ways to grow.</h2><p>Choose a mineral product, track your activity, and build your network at your own pace.</p></div><div class="welcome-strip-links"><button data-page="products"><span class="welcome-link-icon">${icon('vip')}</span><span><strong>Mineral products</strong><small>Own your next opportunity</small></span>${icon('arrow')}</button><button data-page="team"><span class="welcome-link-icon">${icon('invite')}</span><span><strong>Invite &amp; earn</strong><small>Grow with your community</small></span>${icon('arrow')}</button></div></section>
		<section class="page-enter" style="margin-bottom:14px;background:linear-gradient(135deg,#18251e,#131e17);border:1px solid #25D36633;border-radius:10px;padding:14px 18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
			<div style="display:flex;align-items:center;gap:12px;">
				<div style="width:38px;height:38px;border-radius:50%;background:#25D366;color:#101312;display:flex;align-items:center;justify-content:center;font-size:18px;">💬</div>
				<div>
					<strong style="color:var(--ink);font-size:13px;display:flex;align-items:center;gap:6px;">Community &amp; Member Group Chat</strong>
					<p style="color:var(--muted);font-size:11px;margin:2px 0 0 0;">WhatsApp-style live group chat for everyone. Share screenshots, tag members, and ask questions.</p>
				</div>
			</div>
			<button class="primary-button" data-page="chat" style="padding:7px 16px;font-size:12px;background:#25D366;color:#101312;font-weight:700;">Join Chat 💬</button>
		</section>
		<section class="home-grid page-enter" aria-label="Account overview"><article class="panel wallet-panel"><div class="wallet-top"><span class="label">Total assets</span><button class="icon-button" data-action="toggle-balance" aria-label="Toggle balance visibility">${icon('eye')}</button></div><div class="wallet-total">${balance(wallet?.total_assets)}<small>UGX</small></div><div class="wallet-breakdown"><div><strong>${balance(wallet?.invest)} UGX</strong><span>Invest wallet</span></div><div><strong>${balance(wallet?.commission)} UGX</strong><span>Commission wallet</span></div></div>${sessionPrompt()}</article>
			<article class="panel vip-panel"><div><div class="vip-panel-top"><span class="label">Product</span><span class="vip-symbol">${icon('vip')}</span></div><div class="vip-tier">${safe(vip?.name || 'Choose a product')}</div><div class="vip-meta">${vip ? `Level ${safe(vip.level)} · ${amount(vip.deposit_amount)} UGX active` : 'Explore products and choose a plan that fits you.'}</div></div><button class="panel-link" data-page="products"><span>Explore products</span>${icon('arrow')}</button></article></section>
		<section class="quick-actions page-enter" aria-label="Quick actions">${[['deposit', 'Deposit', 'Add funds', 'deposit'], ['withdraw', 'Withdraw', 'Request payout', 'withdraw'], ['vip', 'VIP', 'Membership levels', 'vip'], ['events', 'Events', 'Platform updates', 'event'], ['faq', 'FAQ', 'Help center', 'help'], ['invite', 'Invite', 'Earn with referrals', 'invite'], ['about', 'About', 'Learn more', 'about'], ['app', 'App', 'Download app', 'app']].map(([action, label, subtitle, iconName]) => `<button class="quick-action" ${action === 'vip' ? 'data-page="vip"' : `data-action="${action}"`}><span class="quick-action-icon">${icon(iconName)}</span><span><strong>${label}</strong><small>${subtitle}</small></span></button>`).join('')}</section>
		${home ? `<section class="section-heading"><div><h2>Withdraw log</h2><p>Recent opt-in member withdrawals</p></div></section><section class="withdraw-list">${withdrawals || emptyPanel('No public withdrawals yet.')}</section>` : ''}
		<section class="section-heading"><div><h2>Mining currencies &amp; live rates</h2><p>Live exchange rates for USD &amp; EUR mining assets</p></div><button class="text-button" data-action="refresh">Refresh rates</button></section>
		<section class="market-assets">${miningForexCard}</section>
		${topMinersCard}
		<section class="share-panel"><h2>Share your link &amp; earn</h2><div class="social-row">${['X', 'f', 'Telegram', 'in', 'WhatsApp', 'Instagram', 'TikTok'].map((name) => `<button data-action="share" data-share="${safe(name)}">${safe(name)}</button>`).join('')}</div></section>
		<div class="disclaimer"><span>${icon('shield')}</span><span><strong>Risk notice:</strong> Earnings are not guaranteed and depend on reported revenue. You can lose money. ${safe(home?.risk_notice || '')}</span></div>`;
	renderIcons();
	setNav();
}

function renderProducts() {
	const levels = state.vip?.levels || [];
	const isVipPage = state.page === 'vip';
	if (!isVipPage) {
		const cards = state.products.map((product, index) => {
			const owned = state.ownedProducts.find((purchase) => purchase.product_id === product.id && ['pending', 'active'].includes(purchase.status));
			const action = owned ? `<span class="product-status">${safe(owned.status === 'active' ? 'Active product' : 'Awaiting admin approval')}</span>` : `<button class="primary-button product-action" data-action="product-purchase" data-product-id="${safe(product.id)}">${state.token ? 'Submit manual payment' : 'Sign in to choose'} ${icon('arrow')}</button>`;
			return `<article class="product-card page-enter"><div class="product-card-visual" style="--product-image: url('${safe(product.image_path)}')"><img class="product-image" src="${safe(product.image_path)}" alt="${safe(product.name)} mineral product"><span class="product-number">PRODUCT ${String(index + 1).padStart(2, '0')}</span></div><div class="product-card-body"><div class="product-card-heading"><div><p class="eyebrow">Mineral product</p><h2>${safe(product.name)}</h2></div><span class="product-badge">${safe(product.code)}</span></div><p class="product-description">${safe(product.tagline || 'Manual activation after payment verification.')}</p><div class="product-details"><div><span>Product price</span><strong>${amount(product.cost_price)} UGX</strong></div><div><span>Indicative daily</span><strong>${amount(product.daily_income)} UGX/day</strong></div><div><span>Duration</span><strong>${product.duration_days ? `${safe(product.duration_days)} days` : 'Open'}</strong></div></div>${action}</div></article>`;
		}).join('');
		$('#page-content').innerHTML = `${heading('Products', 'Mineral products', 'Choose a product, pay manually, then submit your transaction ID for admin verification.')}<section class="product-grid">${cards || emptyPanel('No products are currently available.')}</section><div class="disclaimer"><span>${icon('shield')}</span><span>Product income is indicative only. Purchases remain pending until an administrator verifies the manual payment.</span></div>`;
		renderIcons(); setNav(); return;
	}
	const pageTitle = isVipPage ? 'VIP levels' : 'Products';
	const pageKicker = isVipPage ? 'Membership' : 'Products';
	const pageSubtitle = isVipPage ? 'Explore membership levels and mining windows. Yield targets are indicative only, never guaranteed.' : 'Explore deposit products and mining windows. Yield targets are indicative only, never guaranteed.';
	const productLevels = levels.filter((level) => Number(level.level) > 0);
	const vipLevels = levels.length ? levels : [{ level: 0, name: 'No equipment', min_deposit: 0, mining_window: '—', rebate_rates: {} }];
	const rows = vipLevels.map((level) => `<div class="list-row"><span><b class="level-badge">LV ${safe(level.level)}</b></span><strong>${safe(level.name)}</strong><span>${Number(level.level) === 0 ? '0' : amount(level.min_deposit)}${level.max_deposit ? ` – ${amount(level.max_deposit)}` : Number(level.level) === 0 ? '' : '+'} UGX</span><span>${safe(level.mining_window || '—')}</span><span class="vip-rebate">${Object.entries(level.rebate_rates || {}).map(([key, value]) => `<b>${safe(key)} ${safe(value)}%</b>`).join('') || 'No rebate'}</span></div>`).join('');
	const packages = (state.vip?.packages || []).map((pkg) => { const image = pkg.image_path || productImages[pkg.level] || productImages[1]; return `<article class="product-card page-enter"><div class="product-card-visual" style="--product-image: url('${safe(image)}')"><img class="product-image" src="${safe(image)}" alt="${safe(pkg.name)} VIP membership"><span class="product-number">VIP ${safe(pkg.level)}</span></div><div class="product-card-body"><div class="product-card-heading"><div><p class="eyebrow">TLS-style membership</p><h2>${safe(pkg.name)}</h2></div><span class="product-badge">${amount(pkg.price)} UGX</span></div><p class="product-description">${safe(pkg.tagline || 'Qualified members receive a monthly salary for the active term.')}</p><div class="product-details"><div><span>Recharge needed</span><strong>${amount(pkg.recharge_requirement)} UGX</strong></div><div><span>Team requirement</span><strong>${pkg.required_members.map((count, i) => `L${i + 1}: ${count}`).join(' · ')}</strong></div><div><span>Monthly salary</span><strong>${amount(pkg.monthly_salary)} UGX / mo</strong></div></div>${state.token ? `<button class="primary-button product-action" data-action="vip-purchase" data-level-id="${safe(pkg.id)}">Apply for VIP ${icon('arrow')}</button>` : '<button class="primary-button" data-action="auth">Sign in to apply</button>'}</div></article>` }).join('');
	if (isVipPage) {
		$('#page-content').innerHTML = `${heading('VIP membership', 'Membership packages', 'Own an active mineral product, meet the recharge target, and build a qualified Level 1, Level 2, and Level 3 team.')}<section class="product-grid">${packages || emptyPanel('No VIP packages are currently available.')}</section>`;
		renderIcons(); setNav(); return;
	}
	const cards = productLevels.map((level, index) => { const image = productImages[level.level] || productImages[1]; return `<article class="product-card product-level-${safe(level.level)} page-enter"><div class="product-card-visual" style="--product-image: url('${safe(image)}')"><img class="product-image" src="${safe(image)}" alt="${safe(level.name)} mineral product"><span class="product-number">PRODUCT ${String(index + 1).padStart(2, '0')}</span></div><div class="product-card-body"><div class="product-card-heading"><div><p class="eyebrow">Mining product</p><h2>${safe(level.name)}</h2></div><span class="product-badge">LV ${safe(level.level)}</span></div><p class="product-description">Activate this plan with a qualifying deposit and receive hashrate based on your active principal.</p><div class="product-details"><div><span>Starting deposit</span><strong>${amount(level.min_deposit)} UGX</strong></div><div><span>Mining window</span><strong>${safe(level.mining_window || 'Forever')}</strong></div><div><span>Target yield</span><strong>${amount(level.target_yield_pct, 2)}%</strong></div></div><button class="primary-button product-action" data-action="product-deposit" data-product-min="${safe(level.min_deposit)}" data-product-name="${safe(level.name)}">${state.token ? 'Choose this product' : 'Sign in to choose'} ${icon('arrow')}</button></div></article>` }).join('');
	const content = isVipPage
		? `<div class="metric-grid"><div class="metric-card"><span>Your current level</span><strong>${safe(state.vip?.current?.name || 'Sign in')}</strong></div><div class="metric-card"><span>Active deposits</span><strong>${state.vip?.current ? `${amount(state.vip.current.deposit_amount)} UGX` : '—'}</strong></div><div class="metric-card"><span>Hashrate</span><strong>${state.vip?.current ? amount(state.vip.current.hashrate, 3) : '—'}</strong></div><div class="metric-card"><span>Indicative daily</span><strong>${state.vip?.current ? `${amount(state.vip.current.estimated_daily)} UGX` : '—'}</strong></div></div><section class="section-heading"><div><h2>Membership tiers</h2><p>Deposit ranges, mining windows and referral rebates</p></div></section><section class="panel list-panel"><div class="list-row vip-table-head"><span class="label">Level</span><span class="label">Tier</span><span class="label">Deposit range</span><span class="label">Mining window</span><span class="label">Rebates</span></div>${rows || emptyPanel('No membership levels are configured.')}</section>`
		: `<section class="product-grid">${cards || emptyPanel('No products are configured.')}</section><div class="disclaimer"><span>${icon('shield')}</span><span><strong>Risk notice:</strong> Returns are indicative only. Actual earnings depend on reported platform revenue and may be lower or zero. You can lose money.</span></div>`;
	$('#page-content').innerHTML = `${heading(pageKicker, pageTitle, pageSubtitle)}<div class="info-banner"><span>${icon('shield')}</span><span><strong>Returns are not guaranteed.</strong> Actual earnings depend on platform revenue and may be lower than the indicative target, or zero.</span></div>
		${content}`;
	renderIcons(); setNav();
}

function renderMining(inPlaceOnly = false) {
	const mining = state.mining;
	const round = mining?.round;
	const isAdult = mining?.adult_confirmed;
	const limits = mining?.limits;
	const myStake = round?.my_stake;
	const tab = state.miningTab || 'arena';

	let statusBadge = '<span class="tag">No active round</span>';
	if (mining?.paused) statusBadge = '<span class="tag danger-button">Paused</span>';
	else if (round?.status === 'open') statusBadge = `<span class="tag tag-good">Open · Starts in ${Math.ceil(Number(round.seconds_to_start ?? 0))}s</span>`;
	else if (round?.status === 'running') statusBadge = `<span class="tag tag-good">Live Round · Tick ${safe(round.tick ?? 0)}</span>`;
	else if (round?.status === 'settled') statusBadge = '<span class="tag">Settled</span>';
	else if (round?.status === 'void') statusBadge = '<span class="tag">Void (Refunded)</span>';

	const arenaEl = $('#mining-arena-section');
	if (inPlaceOnly && arenaEl && state.miningLastStatus === round?.status && state.miningLastRoundId === round?.id && state.miningLastStake === (myStake ? myStake.status : null) && tab === 'arena') {
		const badge = $('#mining-status-badge');
		if (badge) badge.innerHTML = statusBadge;

		const limitsEl = $('#mining-daily-limits');
		if (limitsEl) limitsEl.innerHTML = `Daily Limit: <b>${amount(limits?.daily_limit || 0)} USDT</b> · Staked Today: <b>${amount(limits?.staked_today || 0)} USDT</b>`;

		if (round?.status === 'open') {
			const countdownEl = $('#mining-countdown-title');
			if (countdownEl) countdownEl.textContent = `Starting in ${Math.ceil(Number(round.seconds_to_start ?? 0))}s`;
			const playersEl = $('#mining-players-text');
			if (playersEl) playersEl.innerHTML = `Players in pool: <b>${safe(round.players ?? 0)}</b>`;
			const sidesEl = $('#mining-side-totals');
			if (sidesEl) sidesEl.innerHTML = `USD: <b>${amount(round.side_totals?.usd || 0)}</b> | UGX: <b>${amount(round.side_totals?.ugx || 0)}</b>`;
		} else if (round?.status === 'running') {
			const mUSD = round.multipliers?.usd || '1.0000';
			const mUGX = round.multipliers?.ugx || '1.0000';
			const usdEl = $('#mining-usd-multiplier');
			if (usdEl) usdEl.textContent = `${safe(mUSD)}x`;
			const ugxEl = $('#mining-ugx-multiplier');
			if (ugxEl) ugxEl.textContent = `${safe(mUGX)}x`;
			const poolEl = $('#mining-pool-total');
			if (poolEl) poolEl.innerHTML = `Pool: <b>${amount(round.pool_total)} USDT</b>`;
			const capEl = $('#mining-cap-total');
			if (capEl) capEl.innerHTML = `Payout Cap: <b>${amount(round.cap_total)} USDT (${safe(mining?.rules?.payout_cap_pct || 90)}%)</b>`;
			const timeEl = $('#mining-time-left');
			if (timeEl) timeEl.innerHTML = `Time Left: <b>${Math.ceil(Number(round.seconds_left ?? 0))}s</b>`;
		}
		return;
	}

	state.miningLastStatus = round?.status;
	state.miningLastRoundId = round?.id;
	state.miningLastStake = myStake ? myStake.status : null;
	const currentInp = $('#stake-amount-input');
	if (currentInp && currentInp.value) {
		state.stakeInputAmount = currentInp.value;
	}

	let arenaBody = '';
	if (!state.token) {
		arenaBody = `<div class="empty-state">Sign in to participate in Stake Rounds.<br><br><button class="primary-button" data-action="auth">Sign in ${icon('arrow')}</button></div>`;
	} else if (!isAdult) {
		arenaBody = `<div class="info-banner"><span>${icon('shield')}</span><span><strong>Age Verification Required:</strong> You must confirm you are 18+ to enter rounds.</span></div>
			<button class="primary-button" data-action="stake-confirm-adult-modal">Confirm 18+ to Play</button>`;
	} else if (round?.status === 'running') {
		const mUSD = round.multipliers?.usd || '1.0000';
		const mUGX = round.multipliers?.ugx || '1.0000';
		const myStakeHtml = myStake ? `<div class="info-banner" style="margin-top:14px;"><span>${icon('shield')}</span><span>Your Stake: <strong>${amount(myStake.amount)} USDT</strong> on <strong>${safe(myStake.side?.toUpperCase())}</strong> · Status: <strong>${safe(myStake.status)}</strong></span></div>
			${myStake.status === 'active' ? `<button class="primary-button" style="width:100%;margin-top:10px;font-size:14px;min-height:46px;" data-action="stake-cashout">Cash Out Now</button>` : '<p class="form-note">Cash-out requested. Settling at tick close...</p>'}` : `<p class="form-note">Round is live. You can enter the next round once this round settles.</p>`;

		arenaBody = `<div class="metric-grid" style="grid-template-columns:1fr 1fr;margin:12px 0;">
				<div class="metric-card" style="border-color:#6ed4a744;text-align:center;">
					<span style="font-weight:700;color:var(--lime);">USD Multiplier</span>
					<strong id="mining-usd-multiplier" style="font-size:32px;color:var(--lime);">${safe(mUSD)}x</strong>
					<small>Pool Staked: ${amount(round.side_totals?.usd || 0)} USDT</small>
				</div>
				<div class="metric-card" style="border-color:#536044;text-align:center;">
					<span style="font-weight:700;color:#8dc5f8;">UGX Multiplier</span>
					<strong id="mining-ugx-multiplier" style="font-size:32px;color:#8dc5f8;">${safe(mUGX)}x</strong>
					<small>Pool Staked: ${amount(round.side_totals?.ugx || 0)} USDT</small>
				</div>
			</div>
			<div style="display:flex;justify-content:space-between;color:var(--muted);font-size:11px;margin-bottom:10px;">
				<span id="mining-pool-total">Pool: <b>${amount(round.pool_total)} USDT</b></span>
				<span id="mining-cap-total">Payout Cap: <b>${amount(round.cap_total)} USDT (${safe(mining?.rules?.payout_cap_pct || 90)}%)</b></span>
				<span id="mining-time-left">Time Left: <b>${Math.ceil(Number(round.seconds_left ?? 0))}s</b></span>
			</div>
			${myStakeHtml}`;
	} else if (round?.status === 'open') {
		const myStakeHtml = myStake ? `<div class="info-banner" style="margin-top:14px;"><span>${icon('shield')}</span><span>You entered <strong>${amount(myStake.amount)} USDT</strong> on <strong>${safe(myStake.side?.toUpperCase())}</strong>. Waiting for round to start...</span></div>`
			: `<div style="display:grid;gap:12px;margin-top:14px;">
				<label class="form-field">Stake Amount (USDT)
					<input id="stake-amount-input" type="number" min="${safe(mining?.rules?.min_stake || 1)}" max="${safe(mining?.rules?.max_stake || 500)}" step="1" value="${safe(state.stakeInputAmount ?? '10')}">
				</label>
				<div style="display:flex;gap:8px;margin-bottom:6px;">
					${[5, 10, 25, 50, 100].map(v => `<button type="button" class="segment-button" data-action="set-stake-amount" data-val="${v}">${v} USDT</button>`).join('')}
				</div>
				<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
					<button class="primary-button" data-action="stake-enter" data-side="usd" style="background:#557b22;color:#fff;">Stake USD ↗</button>
					<button class="primary-button" data-action="stake-enter" data-side="ugx" style="background:#2a4d69;color:#fff;">Stake UGX ↘</button>
				</div>
			</div>`;

		arenaBody = `<div style="display:flex;justify-content:space-between;align-items:center;margin:10px 0;">
				<div>
					<p class="eyebrow">Round #${safe(round.id)}</p>
					<h2 id="mining-countdown-title" style="margin:4px 0;font-size:24px;">Starting in ${Math.ceil(Number(round.seconds_to_start ?? 0))}s</h2>
					<p id="mining-players-text" class="mining-copy">Players in pool: <b>${safe(round.players ?? 0)}</b></p>
				</div>
				<div style="text-align:right;">
					<span class="label">Side Totals</span>
					<div id="mining-side-totals" style="font-size:12px;margin-top:4px;">USD: <b>${amount(round.side_totals?.usd || 0)}</b> | UGX: <b>${amount(round.side_totals?.ugx || 0)}</b></div>
				</div>
			</div>
			${myStakeHtml}`;
	} else {
		arenaBody = `<div class="empty-state">${mining?.paused ? 'Rounds are paused by administration.' : 'Preparing next round...'}</div>`;
	}

	let tabContent = '';
	if (tab === 'history') {
		const historyRows = (state.miningHistory?.data || []).map(s => `
			<div class="list-row">
				<span>#${safe(s.id)}</span>
				<strong>${amount(s.amount)} USDT on ${safe(s.side?.toUpperCase())}</strong>
				<span><b class="tag ${s.status === 'cashed' ? 'tag-good' : s.status === 'lost' ? 'danger-button' : ''}">${safe(s.status)}</b></span>
				<span>${s.payout && Number(s.payout) > 0 ? `+${amount(s.payout, 4)} USDT` : (s.status === 'cashed' ? amount(s.payout, 4) : '—')}</span>
				<span>${safe(String(s.created_at || '').slice(0, 16))}</span>
			</div>
		`).join('');
		tabContent = `<section class="panel list-panel">${historyRows || emptyPanel('No stakes placed yet.')}</section>${renderPagination(state.miningHistory, 'mining-history-page')}`;
	} else if (tab === 'rounds') {
		const roundRows = (state.miningRounds?.data || []).map(r => `
			<div class="list-row">
				<span>Round #${safe(r.id)}</span>
				<strong>Pool: ${amount(r.pool_total)} USDT</strong>
				<span>${safe(r.end_reason || r.status)}</span>
				<span>House Take: ${amount(r.house_take)} USDT</span>
				<button class="text-button" data-action="stake-verify" data-round-id="${safe(r.id)}">Verify</button>
			</div>
		`).join('');
		tabContent = `<section class="panel list-panel">${roundRows || emptyPanel('No settled rounds yet.')}</section>${renderPagination(state.miningRounds, 'mining-rounds-page')}`;
	}

	const usdRate = Number(state.forexRates?.rates?.USD?.rate || state.forexRates?.USD_UGX || 4046);
	const eurRate = Number(state.forexRates?.rates?.EUR?.rate || state.forexRates?.EUR_UGX || 4465);
	const investUgx = state.profile?.invest_balance ?? state.home?.wallets?.invest ?? 0;
	const usdBal = Number(state.profile?.usd_balance ?? 0);
	const eurBal = Number(state.profile?.eur_balance ?? 0);

	const forexBanner = state.token ? `
		<section class="panel" style="margin-bottom:14px;background:var(--card-bg,#1a201c);border:1px solid var(--line);border-radius:8px;padding:14px 16px;">
			<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
				<div>
					<div style="font-size:11px;font-weight:700;color:var(--lime);letter-spacing:0.5px;text-transform:uppercase;">Live Forex Exchange Rate</div>
					<div style="font-size:12px;color:var(--muted);margin-top:2px;">
						1 USD ≈ <b>${amount(usdRate, 0)} UGX</b> &nbsp;|&nbsp; 
						1 EUR ≈ <b>${amount(eurRate, 0)} UGX</b>
					</div>
				</div>
				<div style="display:flex;gap:6px;">
					<button class="primary-button" data-action="forex-convert-modal" style="padding:6px 14px;font-size:12px;min-height:34px;">Convert Currency ⇄</button>
					${(usdBal > 0 || eurBal > 0) ? `<button class="secondary-button" data-action="convert-all-to-ugx" style="padding:6px 14px;font-size:12px;min-height:34px;">Cash Out Forex to UGX ⟲</button>` : ''}
				</div>
			</div>
			<div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:10px;background:rgba(0,0,0,0.25);padding:10px 12px;border-radius:6px;font-size:11px;">
				<div><span style="color:var(--muted);">Invest UGX:</span><br><strong style="font-size:13px;color:var(--ink);">${amount(investUgx)} UGX</strong></div>
				<div><span style="color:var(--muted);">Mining USD:</span><br><strong style="font-size:13px;color:var(--lime);">$${amount(usdBal, 2)}</strong></div>
				<div><span style="color:var(--muted);">Mining EUR:</span><br><strong style="font-size:13px;color:#8dc5f8;">€${amount(eurBal, 2)}</strong></div>
			</div>
		</section>` : '';

	$('#page-content').innerHTML = `
		${heading('Arena', 'Mining Rounds', 'Live simulated rate rounds with provably-fair mechanics and guaranteed 90% payout cap.')}
		${forexBanner}
		<section id="mining-arena-section" class="mining-hero" style="display:block;margin-bottom:16px;">
			<div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--line);padding-bottom:12px;margin-bottom:14px;">
				<div style="display:flex;align-items:center;gap:10px;">
					<strong>${safe(mining?.name || 'Stake Rounds')}</strong>
					<span id="mining-status-badge">${statusBadge}</span>
				</div>
				${round?.server_seed_hash ? `<small style="color:var(--muted);font-family:monospace;" title="Committed before round opens">Seed: ${safe(round.server_seed_hash.slice(0, 16))}...</small>` : ''}
			</div>
			${arenaBody}
		</section>

		${state.token ? `
		<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;border:1px solid var(--line);border-radius:6px;background:var(--panel);margin-bottom:16px;font-size:11px;">
			<span id="mining-daily-limits">Daily Limit: <b>${amount(limits?.daily_limit || 0)} USDT</b> · Staked Today: <b>${amount(limits?.staked_today || 0)} USDT</b></span>
			<div style="display:flex;gap:8px;">
				<button class="text-button" data-action="stake-limit-modal">Set Daily Limit</button>
				<button class="text-button" data-action="stake-exclude-modal">Self-Exclude</button>
			</div>
		</div>` : ''}

		<div class="segmented" style="margin-bottom:12px;">
			<button class="segment-button ${tab === 'arena' ? 'is-active' : ''}" data-mining-tab="arena">Live Arena</button>
			<button class="segment-button ${tab === 'history' ? 'is-active' : ''}" data-mining-tab="history">My Stakes</button>
			<button class="segment-button ${tab === 'rounds' ? 'is-active' : ''}" data-mining-tab="rounds">Settled Rounds (Verify)</button>
		</div>

		${tabContent}

		<div class="disclaimer" style="margin-top:20px;">
			<span>${icon('shield')}</span>
			<span><strong>Notice:</strong> ${safe(mining?.notice || 'Stake Rounds is a game of chance. You can lose your entire stake. The rate shown is a simulated index, not the real USD/UGX exchange rate. 18+ only.')}</span>
		</div>
	`;
	renderIcons();
	setNav();
}

function formatChatMentions(text) {
	if (!text) return '';
	return safe(text).replace(/@([a-zA-Z0-9_\-\.]+)/g, '<span class="chat-mention" data-action="quick-tag" data-user="$1">@$1</span>');
}

function scrollChatToBottom(smooth = true) {
	const stream = $('#chat-stream');
	if (stream) {
		stream.scrollTo({ top: stream.scrollHeight, behavior: smooth ? 'smooth' : 'auto' });
	}
}

function renderChatBubble(msg) {
	const isMine = Boolean(msg.is_me);
	const isStaff = Boolean(msg.is_staff);
	const senderInitial = safe(msg.user_avatar || 'M');
	const senderAvatarHtml = msg.user_avatar_url
		? `<img src="${safe(msg.user_avatar_url)}" alt="">`
		: senderInitial;

	let replyHtml = '';
	if (msg.reply_to) {
		replyHtml = `
			<div class="chat-reply-quote" data-action="focus-chat-message" data-msg-id="${safe(msg.reply_to.id)}">
				<div class="chat-quote-sender">${safe(msg.reply_to.user_name)}</div>
				<div class="chat-quote-text">${msg.reply_to.image_url ? '📷 [Photo] ' : ''}${safe(msg.reply_to.message || '')}</div>
			</div>
		`;
	}

	let imageHtml = '';
	if (msg.image_url) {
		imageHtml = `
			<div class="chat-image-wrap">
				<img src="${safe(msg.image_url)}" class="chat-bubble-image" data-action="view-image" data-img-url="${safe(msg.image_url)}" alt="Shared photo" loading="lazy">
			</div>
		`;
	}

	const textHtml = msg.message ? `<div class="chat-msg-text">${formatChatMentions(msg.message)}</div>` : '';

	const actionsHtml = `
		<div class="chat-msg-actions">
			<button type="button" class="chat-action-icon" data-action="reply-chat-message" data-msg-id="${safe(msg.id)}" title="Reply">${icon('reply')}</button>
			<button type="button" class="chat-action-icon" data-action="tag-chat-user" data-user="${safe(msg.user_name)}" title="Tag">${icon('at')}</button>
			${(isMine || state.profile?.is_admin) ? `<button type="button" class="chat-action-icon" data-action="delete-chat-message" data-msg-id="${safe(msg.id)}" title="Delete">${icon('trash')}</button>` : ''}
		</div>
	`;

	return `
		<div class="chat-bubble-row ${isMine ? 'mine' : 'theirs'}" id="chat-msg-${safe(msg.id)}">
			${!isMine ? `<div class="chat-avatar" style="width:32px;height:32px;font-size:12px;flex-shrink:0;">${senderInitial}</div>` : ''}
			<div class="chat-avatar" style="width:32px;height:32px;font-size:12px;flex-shrink:0;">${senderAvatarHtml}</div>
			<div class="chat-bubble ${isMine ? 'mine' : 'theirs'}">
				${actionsHtml}
				${!isMine ? `
					<div class="chat-sender-header">
						<span class="chat-sender-name" data-action="tag-chat-user" data-user="${safe(msg.user_name)}">${safe(msg.user_name)}</span>
						${isStaff ? `<span class="chat-staff-badge">Staff</span>` : ''}
					</div>` : ''}
				${replyHtml}
				${imageHtml}
				${textHtml}
				<div class="chat-bubble-meta">
					<span>${safe(msg.time_clock || '')}</span>
					<span style="display:inline-flex;">${isMine ? '✓✓' : ''}</span>
				</div>
			</div>
		</div>
	`;
}

function appendChatMessages(newMsgs) {
	const stream = $('#chat-stream');
	if (!stream) return;
	const nearBottom = stream.scrollHeight - stream.scrollTop - stream.clientHeight < 140;
	newMsgs.forEach((msg) => {
		if (!$(`#chat-msg-${msg.id}`, stream)) {
			stream.insertAdjacentHTML('beforeend', renderChatBubble(msg));
		}
	});
	if (nearBottom) {
		scrollChatToBottom(true);
	}
}

function renderChatInputPreview() {
	const box = $('#chat-previews-container');
	if (!box) return;

	let previewContent = '';
	if (state.chatReplyingTo) {
		previewContent += `
			<div class="chat-preview-box">
				<div class="chat-preview-content">
					<span style="color:#25D366;">${icon('reply')}</span>
					<div>
						<strong style="color:var(--ink);">${safe(state.chatReplyingTo.user_name)}</strong>
						<div style="color:var(--muted);font-size:10px;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
							${state.chatReplyingTo.image_url ? '📷 [Photo] ' : ''}${safe(state.chatReplyingTo.message || '')}
						</div>
					</div>
				</div>
				<button type="button" class="chat-preview-close" data-action="cancel-chat-reply">✕</button>
			</div>
		`;
	}

	if (state.chatAttachmentPreview) {
		previewContent += `
			<div class="chat-preview-box">
				<div class="chat-preview-content">
					<img src="${safe(state.chatAttachmentPreview)}" class="chat-preview-thumb" alt="Preview">
					<div>
						<strong style="color:var(--ink);">Picture attached</strong>
						<div style="color:var(--muted);font-size:10px;">Ready to send with message</div>
					</div>
				</div>
				<button type="button" class="chat-preview-close" data-action="cancel-chat-attachment">✕</button>
			</div>
		`;
	}

	box.innerHTML = previewContent;
}

function renderChat() {
	const isGroup = (state.chatType || 'group') === 'group';
	const title = isGroup ? 'DigiPowerX Community' : 'Private Consultation & Support';
	const subtitle = isGroup ? '438+ active miners · Public community group' : 'Direct encrypted 1-on-1 thread with account manager';
	const initial = isGroup ? '👥' : '💬';
	const otherMsg = !isGroup ? (state.chatMessages || []).find((m) => !m.is_me && m.user_avatar_url) : null;
	const headerAvatarHtml = (!isGroup && otherMsg?.user_avatar_url)
		? `<img src="${safe(otherMsg.user_avatar_url)}" alt="">`
		: (isGroup ? '👥' : '💬');

	const messagesHtml = (state.chatMessages || []).map((msg) => renderChatBubble(msg)).join('');

	let tagsBar = '';
	if (state.chatShowTags) {
		const tagList = state.chatMembersList.length ? state.chatMembersList : ['Admin', 'Support', 'Everyone'];
		tagsBar = `
			<div style="display:flex;gap:6px;overflow-x:auto;padding:8px 14px;background:#141a16;border-top:1px solid var(--line);flex-shrink:0;">
				<span style="color:var(--muted);font-size:11px;display:flex;align-items:center;">Quick tag:</span>
				${tagList.map(t => `<button type="button" class="segment-button" data-action="quick-tag" data-user="${safe(t)}" style="padding:2px 8px;font-size:11px;border-radius:12px;">@${safe(t)}</button>`).join('')}
			</div>
		`;
	}

	const inputBarHtml = state.token ? `
		<div id="chat-previews-container"></div>
		${tagsBar}
		<form id="chat-send-form" class="chat-input-bar">
			<input type="file" id="chat-file-input" accept="image/*" style="display:none;">
			<button type="button" class="chat-btn-tool" data-action="chat-pick-file" title="Attach picture">
				${icon('paperclip')}
			</button>
			<button type="button" class="chat-btn-tool" data-action="chat-toggle-tags" title="Tag members">
				${icon('at')}
			</button>
			<textarea id="chat-input-text" class="chat-input" rows="1" placeholder="Type a message... (Type @ to tag members)" required></textarea>
			<button type="submit" class="chat-btn-send" title="Send message">
				${icon('send')}
			</button>
		</form>
	` : `
		<div style="padding:14px;background:rgba(25,32,28,0.95);text-align:center;border-top:1px solid var(--line);">
			<span style="color:var(--muted);font-size:12px;margin-right:10px;">Sign in to send messages and pictures</span>
			<button class="primary-button" data-action="auth" style="padding:6px 14px;font-size:12px;">Sign in</button>
		</div>
	`;

	$('#page-content').innerHTML = `
		${heading('Chat & Community', isGroup ? 'Group Chat' : 'Private Support Chat', isGroup ? 'Connect with everyone, share pictures, tag members, and quote replies.' : 'Dedicated private support channel with your account manager.')}
		<section class="chat-container">
			<header class="chat-header">
				<div class="chat-header-user">
					<div class="chat-avatar-wrap">
						<div class="chat-avatar">${initial}</div>
						<div class="chat-avatar">${headerAvatarHtml}</div>
						<span class="chat-online-dot"></span>
					</div>
					<div>
						<div class="chat-header-title">
							${title}
							${!isGroup ? `<span class="chat-staff-badge" style="background:#25D366;color:#101312;">Private</span>` : ''}
						</div>
						<div class="chat-header-sub">
							<span>🟢</span>
							<span>${subtitle}</span>
						</div>
					</div>
				</div>
				<div class="chat-tabs">
					<button type="button" class="chat-tab-btn ${isGroup ? 'is-active' : ''}" data-action="switch-chat-tab" data-tab="group">Group for Everyone</button>
					<button type="button" class="chat-tab-btn ${!isGroup ? 'is-active' : ''}" data-action="switch-chat-tab" data-tab="private">Private Chat</button>
				</div>
			</header>

			<div class="chat-stream" id="chat-stream">
				<div class="chat-date-separator">Official DigiPowerX Group</div>
				${messagesHtml || emptyPanel('No messages yet. Say hello and share an update!')}
			</div>

			${inputBarHtml}
		</section>
	`;

	renderIcons();
	setNav();
	renderChatInputPreview();
	setTimeout(() => scrollChatToBottom(false), 50);
}

async function pollChat() {
	if (state.page !== 'chat') {
		clearInterval(state.chatPollTimer);
		state.chatPollTimer = null;
		return;
	}
	try {
		const type = state.chatType || 'group';
		const lastMsg = state.chatMessages && state.chatMessages.length ? state.chatMessages[state.chatMessages.length - 1] : null;
		const afterId = lastMsg ? lastMsg.id : 0;
		const params = new URLSearchParams({ type, after_id: afterId });
		if (type === 'private' && state.chatTargetUser) params.set('target_user_id', state.chatTargetUser);
		const data = await api(`chat/messages?${params.toString()}`);
		if (data.messages && data.messages.length > 0) {
			state.chatMessages.push(...data.messages);
			appendChatMessages(data.messages);
		}
	} catch {
		// Silent poll
	}
}

function startChatPolling() {
	clearInterval(state.chatPollTimer);
	state.chatPollTimer = setInterval(pollChat, 2500);
}

async function sendChatMessage() {
	const textInput = $('#chat-input-text');
	const text = textInput ? textInput.value.trim() : '';
	const file = state.chatAttachmentFile;
	if (!text && !file) return;

	const btn = $('.chat-btn-send');
	if (btn) btn.disabled = true;

	try {
		const formData = new FormData();
		formData.append('type', state.chatType || 'group');
		if (state.chatType === 'private' && state.chatTargetUser) {
			formData.append('target_user_id', state.chatTargetUser);
		}
		if (text) formData.append('message', text);
		if (file) formData.append('image', file);
		if (state.chatReplyingTo) formData.append('reply_to_id', state.chatReplyingTo.id);

		const res = await api('chat/messages', { method: 'POST', body: formData });
		if (res.chat_message) {
			state.chatMessages.push(res.chat_message);
			appendChatMessages([res.chat_message]);
			scrollChatToBottom(true);
		}

		if (textInput) {
			textInput.value = '';
			textInput.style.height = 'auto';
		}
		state.chatReplyingTo = null;
		state.chatAttachmentFile = null;
		state.chatAttachmentPreview = null;
		renderChatInputPreview();
	} catch (error) {
		toast(error.message);
	} finally {
		if (btn) btn.disabled = false;
	}
}

function renderTeam() {
	if (!state.token || !state.profile) {
		$('#page-content').innerHTML = `${heading('Referral network', 'My team', 'Track your referrals and the commissions they generate.')}
		<section class="panel page-enter" style="text-align:center;padding:52px 24px;max-width:540px;margin:28px auto;border-radius:12px;border:1px solid var(--line);">
			<div style="width:58px;height:58px;border-radius:50%;background:rgba(211,239,117,0.1);color:var(--lime);display:grid;place-items:center;margin:0 auto 16px;">
				${icon('lock')}
			</div>
			<h2 style="font:600 22px var(--display);margin-bottom:8px;">Sign in required</h2>
			<p style="color:var(--muted);font-size:12px;line-height:1.6;margin:0 auto 24px;max-width:400px;">
				Your referral team, invite link, member list, and commission earnings are private and available only after signing in to your account.
			</p>
			<button class="primary-button" data-action="auth" style="min-width:180px;margin:0 auto;">Sign in / Register</button>
		</section>`;
		renderIcons();
		setNav();
		return;
	}
	const stats = state.team?.stats;
	const levels = stats?.levels || [];
	const selected = levels.find((item) => Number(item.level) === state.teamLevel);
	const members = state.teamMembers?.data || [];
	const memberRows = members.map((member) => `<div class="list-row"><span>${safe(member.joined_at || 'Member')}</span><strong>${safe(member.user)}</strong><span>${member.valid ? '<span class="tag tag-good">Active</span>' : '<span class="tag">Pending</span>'}</span><span>${amount(member.deposited)} UGX</span></div>`).join('');
	const inviteLink = state.team?.invite_link || '';
	$('#page-content').innerHTML = `${heading('Referral network', 'My team', 'Track your referrals and the commissions they generate.')}<section class="invite-panel"><div class="invite-copy"><strong>Grow your network</strong><span>Invite code: <b>${safe(state.team?.invite_code || '')}</b></span></div><div class="invite-link"><code>${safe(inviteLink)}</code><button data-action="copy-invite">Copy</button></div><div class="social-row"><button data-action="share" data-share="Invite link">Share</button></div></section>
		<div class="metric-grid"><div class="metric-card"><span>Team size</span><strong>${amount(stats?.team_size, 0)}</strong></div><div class="metric-card"><span>Referral commissions</span><strong>${amount(stats?.ref_commissions)} UGX</strong></div><div class="metric-card"><span>Team deposits</span><strong>${amount(stats?.team_deposits)} UGX</strong></div><div class="metric-card"><span>Team withdrawals</span><strong>${amount(stats?.team_withdrawals)} UGX</strong></div></div>
		<section class="section-heading"><div><h2>Referral levels</h2><p>Member counts and valid active deposits</p></div><button class="text-button" data-action="commissions">View commissions</button></section><div class="metric-grid">${[1, 2, 3].map((number) => { const level = levels.find((item) => Number(item.level) === number); return `<button class="metric-card level-select ${state.teamLevel === number ? 'is-selected' : ''}" data-team-level="${number}"><span>Level ${number}</span><strong>${level ? `${amount(level.count, 0)} members` : '0 members'}</strong><span class="metric-sub">${level ? `${amount(level.valid, 0)} valid` : '0 valid'}</span></button>`; }).join('')}</div>
		<section class="section-heading"><div><h2>Level ${state.teamLevel} members</h2><p>${selected ? `${selected.valid} valid · ${selected.count} total` : 'Referral members'}</p></div></section><section class="panel list-panel">${memberRows || emptyPanel('No members at this level yet.')}</section>${renderPagination(state.teamMembers, 'team-members-page')}`;
	setNav();
}

function menuButton(iconName, label, action) { return `<button data-action="${action}"><span class="menu-icon">${icon(iconName)}</span><span>${safe(label)}</span><span class="menu-chevron">›</span></button>`; }
function renderMe() {
	const profile = state.profile;
	const ownedCards = profile ? state.ownedProducts.filter((purchase) => purchase.status === 'active').map((purchase) => `<article class="panel"><div class="section-heading"><div><h2>${safe(purchase.product?.name || 'Product')}</h2><p>Active since ${safe(String(purchase.activated_at || '').slice(0, 10))}</p></div><span class="tag tag-good">Active</span></div><div class="metric-grid"><div class="metric-card"><span>Daily income</span><strong>${amount(purchase.daily_income)} UGX</strong></div><div class="metric-card"><span>Earned</span><strong>${amount(purchase.earned_amount)} UGX</strong></div><div class="metric-card"><span>Available to cash out</span><strong>${amount(purchase.available_to_cash_out)} UGX</strong></div></div><button class="primary-button" data-action="product-cashout" data-purchase-id="${safe(purchase.id)}">Cash out earnings</button></article>`).join('') : '';

	const avatarHtml = profile?.avatar_url
		? `<img src="${safe(profile.avatar_url)}" alt="Avatar">`
		: safe((profile?.identity_masked || 'G')[0].toUpperCase());

	const avatarBlock = `
		<div class="profile-avatar-wrap">
			<span class="avatar profile-avatar" id="me-avatar">${avatarHtml}</span>
			${profile ? `
				<button type="button" class="avatar-camera-btn" data-action="pick-avatar" title="Upload profile picture" aria-label="Upload profile picture">
					${icon('camera')}
				</button>
				<input type="file" id="avatar-input" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif" style="display:none">
			` : ''}
		</div>
	`;

	const avatarActions = profile ? `
		<div class="avatar-actions">
			<button type="button" class="avatar-action-btn" data-action="pick-avatar">Change photo</button>
			${profile.avatar_url ? `<button type="button" class="avatar-action-btn danger" data-action="remove-avatar">Remove photo</button>` : ''}
		</div>
	` : '';

	$('#page-content').innerHTML = `${heading('Personal workspace', 'My account', 'Manage your wallets, profile and account security.')}<section class="profile-hero">${avatarBlock}<div><h2>${safe(profile?.identity_masked || 'Guest account')}</h2><p>${profile ? `Invite code ${safe(profile.invite_code)} · ${profile.totp_enabled ? 'Authenticator enabled' : 'Authenticator not enabled'}` : 'Sign in to connect your account and load real wallet data.'}</p>${avatarActions}</div></section>
		${profile?.is_admin ? `
		<section class="panel page-enter" style="margin-bottom:16px;border:1px solid #6ed4a7;background:linear-gradient(135deg,rgba(26,55,36,0.9),rgba(17,35,23,0.95));padding:16px 20px;border-radius:10px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
			<div>
				<span class="eyebrow" style="color:var(--lime);">Staff Workspace</span>
				<h2 style="font:600 18px var(--display);margin:2px 0 4px 0;color:#fff;">${safe(profile.admin_role === 'manager' ? 'Customer Manager Console' : profile.is_super_admin ? 'Super Administrator Console' : 'Operations Admin Console')}</h2>
				<p style="color:var(--muted);font-size:12px;margin:0;">Open your administrative console to view customers, equipment ownership, and client actions.</p>
			</div>
			<button class="primary-button" data-page="admin" style="padding:10px 22px;font-weight:700;background:var(--lime);color:#101312;">Open Console →</button>
		</section>` : ''}
		${profile ? `<div class="metric-grid"><div class="metric-card"><span>Invest wallet</span><strong>${amount(state.home?.wallets?.invest)} UGX</strong></div><div class="metric-card"><span>Commission wallet</span><strong>${amount(state.home?.wallets?.commission)} UGX</strong></div><div class="metric-card"><span>Mining USD</span><strong>$${amount(state.profile?.usd_balance || state.home?.wallets?.usd, 2)}</strong></div><div class="metric-card"><span>Mining EUR</span><strong>€${amount(state.profile?.eur_balance || state.home?.wallets?.eur, 2)}</strong></div></div>` : `<div class="info-banner"><span>${icon('lock')}</span><span>Your private account information is shown after you sign in.</span></div>`}
		${profile ? `
		<div style="display:flex;justify-content:space-between;align-items:center;background:linear-gradient(135deg,rgba(26,55,36,0.8),rgba(17,35,23,0.9));border:1px solid #25D36644;border-radius:8px;padding:12px 16px;margin:14px 0 16px 0;">
			<div>
				<strong style="color:#6ed4a7;font-size:13px;display:flex;align-items:center;gap:6px;">${icon('chat')} Private Consultation Chat</strong>
				<p style="color:var(--muted);font-size:11px;margin:2px 0 0 0;">Encrypted 1-on-1 direct channel with your dedicated account manager.</p>
			</div>
			<button class="primary-button" data-action="open-private-chat" style="padding:6px 14px;font-size:12px;background:#25D366;color:#101312;font-weight:700;">Open Chat 💬</button>
		</div>` : ''}
		${ownedCards ? `<p class="account-section-title">Owned products</p><section class="stacked-panels">${ownedCards || emptyPanel('No active products yet.')}</section>` : ''}
		${profile?.is_admin ? `<p class="account-section-title">Administration</p><section class="account-menu"><button data-page="admin"><span class="menu-icon">${icon('settings')}</span><span>Staff Management Console</span><span class="menu-chevron">›</span></button></section>` : ''}
		<p class="account-section-title">Wallet</p><section class="account-menu">${menuButton('deposit', 'Deposit funds', 'deposit')}${menuButton('withdraw', 'Withdraw funds', 'withdraw')}${menuButton('transfer', 'Transfer between wallets', 'transfer')}${menuButton('statement', 'Statements', 'statements')}${menuButton('statement', 'Deposit history', 'deposit-history')}${menuButton('statement', 'Withdrawal history', 'withdrawal-history')}</section><p class="account-section-title">Security and support</p><section class="account-menu">${menuButton('chat', 'Private 1-on-1 Support Chat', 'open-private-chat')}${menuButton('lock', 'Security PIN', 'set-pin')}${menuButton('shield', 'Authenticator', 'totp')}${menuButton('user', 'Change password', 'password')}${menuButton('bell', 'Notifications', 'notifications')}${menuButton('help', 'FAQ and support', 'faq')}</section>
		${profile ? '<button class="secondary-button logout-button" data-action="logout">Sign out</button>' : '<button class="primary-button logout-button" data-action="auth">Sign in or create an account</button>'}`;
	renderIcons(); setNav();
}
function renderAdmin() {
	const role = state.profile?.admin_role || (state.profile?.is_super_admin ? 'super_admin' : 'admin');
	const isSuperAdmin = role === 'super_admin';
	const isOpsAdmin = role === 'admin';
	const isCustomerManager = role === 'manager';

	const activeTab = state.adminTab || 'clients';
	const overview = state.admin?.overview || {};
	const clients = state.admin?.clients?.data || [];
	const clientStats = state.admin?.clientStats || {};
	const products = state.admin?.products || [];
	const prices = state.admin?.prices || [];
	const purchases = state.admin?.purchases?.data || [];
	const vipPurchases = state.admin?.vipPurchases?.data || [];
	const deposits = state.admin?.deposits?.data || [];
	const withdrawals = state.admin?.withdrawals?.data || [];
	const adminsList = state.admin?.adminsList || [];
	const currentFilter = state.adminClientFilter || 'all';
	const searchQuery = state.adminClientSearch || '';

	let roleBadge = '<span class="admin-role-badge admin">⚡ Operations Admin</span>';
	let roleDesc = 'Full operational authority: approve/reject deposits, cash-out withdrawals, product & VIP purchases, manage products, VIP rebates, and market prices.';
	if (isSuperAdmin) {
		roleBadge = '<span class="admin-role-badge super_admin">👑 Super Administrator</span>';
		roleDesc = 'Full system oversight (TLS benchmark). Operations are executed by Operations Admin. You manage and edit staff accounts.';
	} else if (isCustomerManager) {
		roleBadge = '<span class="admin-role-badge manager">🤝 Customer Manager</span>';
		roleDesc = 'Customer console (TLS benchmark): monitor clients, view equipment ownership, assign products paid by clients, and support chat.';
	}

	// Tab bar
	const tabsHtml = `
		<div class="admin-nav-tabs">
			<button class="admin-nav-tab ${activeTab === 'clients' ? 'active' : ''}" data-action="switch-admin-tab" data-admin-tab="clients">👥 Customers & Machines</button>
			${!isCustomerManager ? `<button class="admin-nav-tab ${activeTab === 'transactions' ? 'active' : ''}" data-action="switch-admin-tab" data-admin-tab="transactions">💳 Transactions & Approvals</button>` : ''}
			${!isCustomerManager ? `<button class="admin-nav-tab ${activeTab === 'catalog' ? 'active' : ''}" data-action="switch-admin-tab" data-admin-tab="catalog">📦 Equipment & Pricing</button>` : ''}
			${isSuperAdmin ? `<button class="admin-nav-tab ${activeTab === 'admins' ? 'active' : ''}" data-action="switch-admin-tab" data-admin-tab="admins">🛡️ Staff Roster</button>` : ''}
		</div>
	`;

	let tabContent = '';

	if (activeTab === 'clients') {
		// TLS-style Customers View
		const productOptions = products.map((p) => `<option value="${safe(p.id)}">${safe(p.name)} — ${amount(p.cost_price)} UGX</option>`).join('');

		const clientCards = clients.map((client) => {
			const activeProducts = client.active_products || [];
			const productsListHtml = activeProducts.length > 0
				? activeProducts.map((p) => `
					<div class="mini-lock">
						<div class="mini-lock-info">
							<img class="mini-lock-thumb" src="${safe(p.image_url || '/images/products/mining/bronze.webp')}" alt="">
							<div>
								<p><b>${safe(p.product_name)}</b>${!isCustomerManager && p.principal !== null ? ` (${amount(p.principal)} UGX)` : ''}</p>
								<small>${p.days_left !== null ? p.days_left + ' days left' : 'Ongoing'} · ${amount(p.daily_income)} UGX/day${!isCustomerManager && p.earned_so_far !== null ? ` · ${amount(p.earned_so_far)} UGX earned` : ''}</small>
							</div>
						</div>
					</div>
				`).join('')
				: '<p style="margin:0;font-size:12px;color:var(--muted)">No equipment currently running for this client.</p>';

			return `
				<details class="client-card">
					<summary>
						<strong>
							${safe(client.name || 'Client')}
							${client.archived_at ? '<b class="tag">Archived</b>' : client.is_active ? '<b class="tag tag-good">Active</b>' : '<b class="tag">Disabled</b>'}
						</strong>
						<div class="client-summary-meta">
							<span>${safe(client.phone || client.email || '—')}</span>
							<span><b>${client.active_products_count || 0}</b> machines</span>
							${!isCustomerManager && client.total_earned !== null ? `<span>${amount(client.total_earned)} UGX earned</span>` : ''}
						</div>
					</summary>
					<div class="client-body">
						${!isCustomerManager ? `
						<div class="client-stats-strip">
							<span>Invest Balance: <b>${amount(client.invest_balance)} UGX</b></span>
							<span>Commission: <b>${amount(client.commission_balance)} UGX</b></span>
							<span>Total Held: <b>${amount(client.total_balance)} UGX</b></span>
							<span>Joined: <b>${client.created_at ? new Date(client.created_at).toLocaleDateString() : '—'}</b></span>
						</div>` : `
						<div class="client-stats-strip">
							<span>Joined: <b>${client.created_at ? new Date(client.created_at).toLocaleDateString() : '—'}</b></span>
							<span>Status: <b>${client.is_active ? 'Active' : 'Disabled'}</b></span>
						</div>`}

						<div>
							<strong style="display:block;margin-bottom:6px;font-size:12px;color:var(--ink)">Active Equipment</strong>
							${productsListHtml}
						</div>

						<form class="add-lock-form" data-action="assign-product-form" data-client-id="${safe(client.id)}">
							<select name="product_id" required ${isSuperAdmin ? 'disabled' : ''}>
								<option value="">Add a lock/product this client paid for…</option>
								${productOptions}
							</select>
							<button class="primary-button" type="submit" ${isSuperAdmin ? 'disabled title="Super admin does not modify"' : ''}>+ Assign Equipment</button>
						</form>

						<div class="admin-client-actions" style="margin-top:6px">
							<button class="text-button" data-action="open-client-chat" data-client-id="${safe(client.id)}" data-client-name="${safe(client.name || client.phone || 'Client')}">💬 Message Client</button>
							${isOpsAdmin && !client.archived_at ? `
								<button class="text-button" data-action="toggle-client" data-client-id="${safe(client.id)}">${client.is_active ? 'Deactivate' : 'Activate'}</button>
								<button class="text-button danger-button" data-action="archive-client" data-client-id="${safe(client.id)}">Delete Client</button>
							` : ''}
						</div>
					</div>
				</details>
			`;
		}).join('');

		tabContent = `
			<section class="metric-grid admin-metrics" style="margin-bottom:16px">
				<div class="metric-card"><span>Total Clients</span><strong>${amount(clientStats.total || 0, 0)}</strong></div>
				<div class="metric-card"><span>With Equipment</span><strong>${amount(clientStats.with_product || 0, 0)}</strong></div>
				<div class="metric-card"><span>Without Equipment</span><strong>${amount(clientStats.without_product || 0, 0)}</strong></div>
				${!isCustomerManager ? `<div class="metric-card"><span>Active Equipment Value</span><strong>${amount(clientStats.active_value || 0)} UGX</strong></div>` : ''}
			</section>

			<div class="filter-pills">
				<button class="filter-pill ${currentFilter === 'all' ? 'active' : ''}" data-action="filter-clients" data-filter="all">All Clients <span class="filter-pill-count">${clientStats.total || 0}</span></button>
				<button class="filter-pill ${currentFilter === 'with_product' ? 'active' : ''}" data-action="filter-clients" data-filter="with_product">With Equipment <span class="filter-pill-count">${clientStats.with_product || 0}</span></button>
				<button class="filter-pill ${currentFilter === 'without_product' ? 'active' : ''}" data-action="filter-clients" data-filter="without_product">Without Equipment <span class="filter-pill-count">${clientStats.without_product || 0}</span></button>
			</div>

			<div style="display:flex;gap:8px;margin-bottom:14px">
				<input id="admin-client-search-input" type="text" class="chat-input" style="height:38px;min-height:38px;padding:6px 14px;font-size:12px" placeholder="Search client by phone, email, or name..." value="${safe(searchQuery)}">
				<button class="primary-button" data-action="search-clients-btn" style="padding:6px 16px;white-space:nowrap">Search</button>
				${searchQuery ? `<button class="text-button" data-action="clear-client-search">Clear</button>` : ''}
			</div>

			<section class="catalog">
				${clientCards || emptyPanel('No clients matching this filter.')}
			</section>
			${renderPagination(state.admin?.clients, 'admin-client-page')}
		`;
	} else if (activeTab === 'transactions') {
		const superAdminNotice = isSuperAdmin ? `<div class="info-banner" style="margin-bottom:14px"><span>👁️ <b>Super Administrator Oversight</b>: Real-time monitoring of all financial operations. Action buttons are reserved for operational administrators.</span></div>` : '';
		const currentTxStatus = state.adminTxStatus || 'pending';
		const statusPills = `
			<div class="filter-pills" style="margin-bottom:14px">
				<button class="filter-pill ${currentTxStatus === 'pending' ? 'active' : ''}" data-action="filter-admin-tx-status" data-status="pending">Pending Only</button>
				<button class="filter-pill ${currentTxStatus === 'all' ? 'active' : ''}" data-action="filter-admin-tx-status" data-status="all">All Records</button>
				<button class="filter-pill ${currentTxStatus === 'confirmed' ? 'active' : ''}" data-action="filter-admin-tx-status" data-status="confirmed">Confirmed / Paid</button>
				<button class="filter-pill ${currentTxStatus === 'rejected' ? 'active' : ''}" data-action="filter-admin-tx-status" data-status="rejected">Rejected</button>
			</div>
		`;

		const depositRows = deposits.map((d) => `
			<div class="admin-table-row">
				<strong>${amount(d.amount)} UGX</strong>
				<span>${safe(d.user?.email || d.user?.phone)}</span>
				<span>TX ${safe(d.txid || '—')} · ${safe(d.payment_method || 'Mobile')}</span>
				<span>${safe(d.status)}</span>
				<span>
					${d.status === 'pending' && isOpsAdmin ? `
						<button class="text-button" data-action="confirm-deposit" data-deposit-id="${safe(d.id)}">Confirm</button>
						<button class="text-button danger-button" data-action="reject-deposit" data-deposit-id="${safe(d.id)}">Reject</button>
					` : isSuperAdmin ? '<span class="tag">Oversight</span>' : '—'}
				</span>
			</div>
		`).join('');

		const withdrawalRows = withdrawals.map((w) => `
			<div class="admin-table-row">
				<strong>${amount(w.amount)} UGX</strong>
				<span>${safe(w.user?.email || w.user?.phone)}</span>
				<span>Net: ${amount(w.net_amount)} UGX (${safe(w.wallet)})</span>
				<span>${safe(w.status)}</span>
				<span>
					${w.status === 'pending' && isOpsAdmin ? `
						<button class="text-button" data-action="approve-withdrawal" data-withdrawal-id="${safe(w.id)}">Approve</button>
						<button class="text-button danger-button" data-action="reject-withdrawal" data-withdrawal-id="${safe(w.id)}">Reject</button>
					` : w.status === 'approved' && isOpsAdmin ? `
						<button class="text-button" data-action="complete-withdrawal" data-withdrawal-id="${safe(w.id)}">Mark Paid</button>
						<button class="text-button danger-button" data-action="reject-withdrawal" data-withdrawal-id="${safe(w.id)}">Reject</button>
					` : isSuperAdmin ? '<span class="tag">Oversight</span>' : '—'}
				</span>
			</div>
		`).join('');

		const purchaseRows = purchases.map((purchase) => `
			<div class="admin-table-row">
				<strong>${safe(purchase.product?.name)}</strong>
				<span>${safe(purchase.user?.email || purchase.user?.phone)}</span>
				<span>${safe(purchase.transaction_id || '—')}</span>
				<span>${safe(purchase.status)}</span>
				<span>
					${purchase.status === 'pending' && isOpsAdmin ? `
						<button class="text-button" data-action="activate-purchase" data-purchase-id="${safe(purchase.id)}">Activate</button>
						<button class="text-button danger-button" data-action="reject-purchase" data-purchase-id="${safe(purchase.id)}">Reject</button>
					` : isSuperAdmin ? '<span class="tag">Oversight</span>' : '—'}
				</span>
			</div>
		`).join('');

		const vipPurchaseRows = vipPurchases.map((purchase) => `
			<div class="admin-table-row">
				<strong>${safe(purchase.vip_level?.name)}</strong>
				<span>${safe(purchase.user?.email || purchase.user?.phone)}</span>
				<span>${safe(purchase.transaction_id || '—')}</span>
				<span>${safe(purchase.status)}</span>
				<span>
					${purchase.status === 'pending' && isOpsAdmin ? `
						<button class="text-button" data-action="activate-vip-purchase" data-purchase-id="${safe(purchase.id)}">Activate</button>
						<button class="text-button danger-button" data-action="reject-vip-purchase" data-purchase-id="${safe(purchase.id)}">Reject</button>
					` : isSuperAdmin ? '<span class="tag">Oversight</span>' : '—'}
				</span>
			</div>
		`).join('');

		tabContent = `
			${superAdminNotice}
			${statusPills}
			<section class="metric-grid admin-metrics" style="margin-bottom:16px">
				<div class="metric-card"><span>Total Users</span><strong>${amount(overview.users, 0)}</strong></div>
				<div class="metric-card"><span>Confirmed Deposits</span><strong>${amount(overview.deposits_confirmed)} UGX</strong></div>
				<div class="metric-card"><span>Pending Deposits</span><strong>${amount(overview.pending_deposits, 0)}</strong></div>
				<div class="metric-card"><span>Pending Withdrawals</span><strong>${amount(overview.pending_withdrawals)} UGX</strong></div>
				<div class="metric-card"><span>Completed Withdrawals</span><strong>${amount(overview.withdrawals_completed)} UGX</strong></div>
				<div class="metric-card"><span>Net Position</span><strong>${amount(overview.net_position)} UGX</strong></div>
			</section>

			<section class="admin-section">
				<div class="section-heading"><div><h2>Deposits</h2><p>Review deposit transaction references and credit balances.</p></div></div>
				<div class="admin-table">
					<div class="admin-table-head"><span>Amount</span><span>Member</span><span>Reference</span><span>Status</span><span>Action</span></div>
					${depositRows || emptyPanel('No deposits matching status.')}
				</div>
				${renderPagination(state.admin?.deposits, 'admin-deposit-page')}
			</section>

			<section class="admin-section">
				<div class="section-heading"><div><h2>Cash Out Requests (Withdrawals)</h2><p>Review user cash outs and approve or submit paid confirmations.</p></div></div>
				<div class="admin-table">
					<div class="admin-table-head"><span>Requested</span><span>Member</span><span>Net & Wallet</span><span>Status</span><span>Action</span></div>
					${withdrawalRows || emptyPanel('No cash-out requests matching status.')}
				</div>
				${renderPagination(state.admin?.withdrawals, 'admin-withdrawal-page')}
			</section>

			<section class="admin-section">
				<div class="section-heading"><div><h2>Equipment Purchases</h2><p>Verify payment proofs before activating machines.</p></div></div>
				<div class="admin-table">
					<div class="admin-table-head"><span>Machine</span><span>Member</span><span>TXID</span><span>Status</span><span>Action</span></div>
					${purchaseRows || emptyPanel('No equipment purchases matching status.')}
				</div>
				${renderPagination(state.admin?.purchases, 'admin-purchase-page')}
			</section>

			<section class="admin-section">
				<div class="section-heading"><div><h2>VIP Membership Applications</h2><p>Review and activate VIP upgrade applications.</p></div></div>
				<div class="admin-table">
					<div class="admin-table-head"><span>VIP Level</span><span>Member</span><span>TXID</span><span>Status</span><span>Action</span></div>
					${vipPurchaseRows || emptyPanel('No VIP applications matching status.')}
				</div>
				${renderPagination(state.admin?.vipPurchases, 'admin-vip-purchase-page')}
			</section>
		`;
	} else if (activeTab === 'catalog') {
		const exchanges = { binance: 'Binance', okx: 'OKX', huobi: 'Huobi', coinbase: 'Coinbase' };
		const priceRows = prices.flatMap((row) => Object.entries(row.exchanges || {}).map(([exchange, quote]) => `
			<div class="admin-price-row">
				<strong>${safe(row.symbol)}</strong>
				<span>${safe(exchanges[exchange] || exchange)}</span>
				<input name="price" type="number" step="any" min="0" value="${quote?.source === 'admin' ? safe(quote.price) : ''}" placeholder="${quote?.price ? safe(quote.price) : 'Manual price'}" data-symbol="${safe(row.symbol)}" data-exchange="${safe(exchange)}" ${!isOpsAdmin ? 'disabled' : ''}>
				<input name="change_pct" type="number" step="any" value="${quote?.source === 'admin' ? safe(quote.change_pct) : ''}" placeholder="24h %" ${!isOpsAdmin ? 'disabled' : ''}>
				<label><input name="enabled" type="checkbox" ${quote?.source === 'admin' ? 'checked' : ''} ${!isOpsAdmin ? 'disabled' : ''}> Managed</label>
			</div>
		`)).join('');

		const productRows = products.map((product) => `
			<div class="admin-table-row product-admin-row">
				<strong>${safe(product.code)} · ${safe(product.name)}</strong>
				<label>Price (UGX)<input data-product-field="cost_price" data-product-id="${safe(product.id)}" type="number" min="1" step="1" value="${safe(product.cost_price)}" ${!isOpsAdmin ? 'disabled' : ''}></label>
				<label>Daily income<input data-product-field="daily_income" data-product-id="${safe(product.id)}" type="number" min="0" step="any" value="${safe(product.daily_income)}" ${!isOpsAdmin ? 'disabled' : ''}></label>
				<label>Days<input data-product-field="duration_days" data-product-id="${safe(product.id)}" type="number" min="1" value="${safe(product.duration_days || '')}" ${!isOpsAdmin ? 'disabled' : ''}></label>
				${isOpsAdmin ? `<button class="text-button" data-action="save-product" data-product-id="${safe(product.id)}">Save</button>` : '—'}
			</div>
		`).join('');

		tabContent = `
			<section class="admin-section">
				<div class="section-heading"><div><h2>Mining Equipment Catalog</h2><p>Price, daily return, and duration days for equipment.</p></div></div>
				<div class="admin-table">${productRows || emptyPanel('No products configured.')}</div>
			</section>

			<section class="admin-section">
				<div class="section-heading"><div><h2>Live Prices & Forex Overrides</h2><p>Set manual market prices or allow live oracle quotes.</p></div></div>
				<form id="admin-price-form" class="admin-price-form">
					<div class="admin-price-head"><span>Asset</span><span>Exchange</span><span>Price</span><span>24h %</span><span>Source</span></div>
					${priceRows}
				</form>
				${isOpsAdmin ? `<button class="primary-button" data-action="save-admin-prices">Save Price Controls</button>` : ''}
			</section>

			<section class="admin-section">
				<div class="section-heading"><div><h2>Publish Announcement</h2><p>Control the ticker and event notices displayed to clients.</p></div></div>
				<form id="admin-announcement-form" class="admin-form">
					<label class="form-field">Title<input name="title" required maxlength="255" ${!isOpsAdmin ? 'disabled' : ''}></label>
					<label class="form-field">Message<textarea name="body" rows="3" ${!isOpsAdmin ? 'disabled' : ''}></textarea></label>
					<label class="form-field">Type<select name="kind" ${!isOpsAdmin ? 'disabled' : ''}><option value="ticker">Ticker</option><option value="event">Event</option><option value="notice">Notice</option></select></label>
					<label class="form-check"><input name="is_active" type="checkbox" checked ${!isOpsAdmin ? 'disabled' : ''}><span>Visible to clients</span></label>
					${isOpsAdmin ? `<button class="primary-button" type="submit">Publish Announcement</button>` : ''}
				</form>
			</section>
		`;
	} else if (activeTab === 'admins' && isSuperAdmin) {
		const adminCards = adminsList.map((admin) => `
			<div class="admin-roster-card">
				<div class="admin-roster-info">
					<strong>
						${safe(admin.name)}
						<span class="admin-role-badge ${safe(admin.admin_role)}">${safe(admin.role_label)}</span>
						${admin.is_active ? '<b class="tag tag-good">Active</b>' : '<b class="tag">Disabled</b>'}
					</strong>
					<span>${safe(admin.email || admin.phone || 'No direct contact')} · Added ${admin.created_at ? new Date(admin.created_at).toLocaleDateString() : '—'}</span>
				</div>
				<div class="row-actions">
					${!admin.is_super_admin ? `
						<button class="text-button" data-action="toggle-admin-user" data-admin-id="${safe(admin.id)}">${admin.is_active ? 'Deactivate' : 'Activate'}</button>
						<button class="text-button" data-action="edit-admin-role" data-admin-id="${safe(admin.id)}" data-admin-name="${safe(admin.name)}" data-admin-role="${safe(admin.admin_role)}">Edit Role</button>
					` : '<span class="tag">Super Admin (Primary)</span>'}
				</div>
			</div>
		`).join('');

		tabContent = `
			<section class="admin-section">
				<div class="section-heading">
					<div>
						<h2>Administrator & Manager Roster</h2>
						<p>Super Administrator privilege: Add, configure, and manage Operations Admins and Customer Managers.</p>
					</div>
				</div>
				<div class="catalog">${adminCards || emptyPanel('No other staff accounts configured.')}</div>
			</section>

			<section class="admin-section">
				<div class="section-heading">
					<div>
						<h2>Create Administrator Account</h2>
						<p>Select the administrator category to assign the appropriate privilege scope.</p>
					</div>
				</div>
				<form id="create-admin-form" class="admin-form">
					<label class="form-field">Name<input name="name" required maxlength="120" placeholder="e.g. John Okello"></label>
					<label class="form-field">Email<input name="email" type="email" placeholder="e.g. john@powerdig.com"></label>
					<label class="form-field">Phone<input name="phone" placeholder="e.g. 256700000000"></label>
					<label class="form-field">Role
						<select name="role" required>
							<option value="admin">Operations Admin (Full transactions, withdrawals, deposits, approvals, product & VIP edits, rebates)</option>
							<option value="manager">Customer Manager (Customer list with/without product, equipment assignment, chat, support — no transactions)</option>
						</select>
					</label>
					<label class="form-field">Password<input name="password" type="password" required minlength="8"></label>
					<label class="form-field">Confirm Password<input name="password_confirmation" type="password" required minlength="8"></label>
					<p class="form-note" data-form-error></p>
					<button class="primary-button" type="submit">Create Staff Account</button>
				</form>
			</section>
		`;
	}

	$('#page-content').innerHTML = `
		<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
			<div>
				<p class="kicker">Manager Console (TLS Reference)</p>
				<h1 style="display:flex;align-items:center;gap:10px">Control Panel ${roleBadge}</h1>
				<p style="font-size:12px;color:var(--muted)">${roleDesc}</p>
			</div>
		</div>
		${tabsHtml}
		${tabContent}
	`;

	renderIcons(); setNav();
}
function render() { updateIdentity(); if (state.page === 'products' || state.page === 'vip') renderProducts(); else if (state.page === 'mining') renderMining(); else if (state.page === 'chat') renderChat(); else if (state.page === 'team') renderTeam(); else if (state.page === 'me') renderMe(); else if (state.page === 'admin') renderAdmin(); else renderHome(); }

async function loadPage(page = state.page) {
	state.page = page; setNav(); pageLoading();
	if (page !== 'mining' && state.miningPollTimer) {
		clearInterval(state.miningPollTimer);
		state.miningPollTimer = null;
	}
	if (page !== 'chat' && state.chatPollTimer) {
		clearInterval(state.chatPollTimer);
		state.chatPollTimer = null;
	}
	if (page === 'home') renderHome();
	try {
		if (page === 'home') {
			const requests = [api(state.token ? 'home' : 'home-public').then((home) => {
				state.home = home;
				state.prices = home.prices || state.prices;
				if (home.forex) state.forexRates = home.forex;
			}).catch((error) => { if (state.token) toast(error.message); })];
			await Promise.all(requests);
		} else if (page === 'products') { state.products = await api('products'); state.ownedProducts = state.token ? await api('products/owned') : []; }
		else if (page === 'vip') state.vip = await api('vip');
		else if (page === 'chat') {
			const type = state.chatType || 'group';
			const params = new URLSearchParams({ type });
			if (type === 'private' && state.chatTargetUser) params.set('target_user_id', state.chatTargetUser);
			const [chatData, membersData] = await Promise.all([
				api(`chat/messages?${params.toString()}`).catch(() => ({ messages: [] })),
				api('chat/members').catch(() => ({ members: [] }))
			]);
			state.chatMessages = chatData.messages || [];
			state.chatMembersList = membersData.members || [];
			renderChat();
			startChatPolling();
		}
		else if (page === 'me' && state.token) state.ownedProducts = await api('products/owned');
		else if (page === 'admin') {
			if (!state.profile?.is_admin) throw new Error('Administrator access required.');
			const role = state.profile?.admin_role || (state.profile?.is_super_admin ? 'super_admin' : 'admin');
			state.adminTab = state.adminTab || 'clients';
			if (role === 'manager' && state.adminTab === 'transactions') state.adminTab = 'clients';
			if (!state.profile?.is_super_admin && state.adminTab === 'admins') state.adminTab = 'clients';

			const clientFilter = state.adminClientFilter || 'all';
			const clientSearch = state.adminClientSearch || '';

			const clientParams = new URLSearchParams({ filter: clientFilter, page: state.adminClientPage || 1 });
			if (clientSearch) clientParams.set('q', clientSearch);

			const reqs = [
				(role === 'super_admin' || role === 'admin') ? api('admin/overview').catch(() => ({})) : Promise.resolve({}),
				api('admin/products').catch(() => []),
				api(`admin/users?${clientParams.toString()}`).catch(() => ({ clients: { data: [] }, stats: {} })),
			];

			if (role === 'super_admin' || role === 'admin') {
				const txStatus = state.adminTxStatus || 'pending';
				const txStatusQ = txStatus === 'all' ? '' : `status=${txStatus}&`;
				reqs.push(api('admin/prices').catch(() => ({})));
				reqs.push(api(`admin/purchases?${txStatusQ}page=${state.adminPurchasePage || 1}`).catch(() => ({ data: [] })));
				reqs.push(api(`admin/vip-purchases?${txStatusQ}page=1`).catch(() => ({ data: [] })));
				reqs.push(api(`admin/deposits?${txStatusQ}page=${state.adminDepositPage || 1}`).catch(() => ({ data: [] })));
				reqs.push(api(`admin/withdrawals?${txStatusQ}page=${state.adminWithdrawalPage || 1}`).catch(() => ({ data: [] })));
			}
			if (role === 'super_admin') {
				reqs.push(api('super-admin/admins').catch(() => []));
			}

			const results = await Promise.all(reqs);
			state.admin = {
				overview: results[0] || {},
				products: results[1] || [],
				clientsData: results[2] || { clients: { data: [] }, stats: {} },
				clients: results[2]?.clients || { data: [] },
				clientStats: results[2]?.stats || {},
			};
			if (role === 'super_admin' || role === 'admin') {
				state.admin.prices = results[3]?.prices || [];
				state.admin.purchases = results[4] || { data: [] };
				state.admin.vipPurchases = results[5] || { data: [] };
				state.admin.deposits = results[6] || { data: [] };
				state.admin.withdrawals = results[7] || { data: [] };
			}
			if (role === 'super_admin') {
				state.admin.adminsList = results[8] || [];
			}
		}
		else if (page === 'mining' && state.token) {
			const tab = state.miningTab || 'arena';
			const reqs = [
				api('mining/current').then((data) => { state.mining = data; }),
				api('forex/rates').then((data) => { state.forexRates = data; }).catch(() => { }),
				api('me').then((data) => { state.profile = data; }).catch(() => { })
			];
			if (tab === 'history') reqs.push(api(`mining/history?page=${state.miningHistoryPage || 1}`).then((data) => { state.miningHistory = data; }));
			else if (tab === 'rounds') reqs.push(api(`mining/rounds?page=${state.miningRoundsPage || 1}`).then((data) => { state.miningRounds = data; }));
			await Promise.all(reqs);
			startMiningRefresh();
		}
		else if (page === 'team' && state.token) {
			const [team, members] = await Promise.all([
				api('team'),
				api(`team/level/${state.teamLevel}?page=${state.teamMembersPage || 1}`)
			]);
			state.team = team;
			state.teamMembers = members;
		}
	} catch (error) { toast(error.message); }
	render();
}
async function refreshMining() {
	if (state.page !== 'mining' || !state.token) {
		clearInterval(state.miningPollTimer);
		state.miningPollTimer = null;
		return;
	}
	try {
		state.mining = await api('mining/current');
		renderMining(true);
	} catch {
		// Silent background poll
	}
}
function startMiningRefresh() {
	clearInterval(state.miningPollTimer);
	state.miningPollTimer = setInterval(refreshMining, 2500);
}
async function refreshHomeWithdrawals() {
	if (state.page !== 'home') return;
	try {
		const home = await api(state.token ? 'home' : 'home-public');
		state.home = { ...state.home, ...home };
		state.prices = home.prices || state.prices;
		const list = $('.withdraw-list');
		if (!list) return;
		list.innerHTML = withdrawalRows(home.withdraw_log || []) || emptyPanel('No public withdrawals yet.');
	} catch (error) {
		console.error('Unable to refresh the public withdrawal log.', error);
	}
}
function startHomeRefresh() {
	clearInterval(state.homeRefreshTimer);
	state.homeRefreshTimer = setInterval(refreshHomeWithdrawals, 15000);
}
async function loadProfile() { if (!state.token) return; try { state.profile = await api('me'); } catch { clearSession(); } }
function clearSession() { state.token = null; state.profile = null; state.home = null; sessionStorage.removeItem('dp_token'); updateIdentity(); }
function toast(message) { const element = $('#toast'); element.textContent = message; element.classList.add('is-visible'); clearTimeout(toast.timer); toast.timer = setTimeout(() => element.classList.remove('is-visible'), 3300); }
function showModal(content, kind) { state.modal = kind; $('#modal-content').innerHTML = content; $('#modal-backdrop').hidden = false; $('input,select,button', $('#modal-content'))?.focus(); }
function closeModal() { $('#modal-backdrop').hidden = true; $('#modal-content').innerHTML = ''; state.modal = null; }

function authForm(mode = 'login') {
	const registering = mode === 'register';
	showModal(`<p class="eyebrow">DigiPowerX account</p><h2 id="modal-title">${registering ? 'Create account' : 'Welcome back'}</h2><p class="modal-intro">${registering ? 'Create your member account to connect to the platform.' : 'Sign in to view your account and activity.'}</p><form class="form-stack" id="auth-form" data-mode="${mode}"><label class="form-field">Email or phone<input name="identifier" autocomplete="username" required placeholder="you@example.com"></label><label class="form-field">Password<input type="password" name="password" autocomplete="${registering ? 'new-password' : 'current-password'}" minlength="8" required placeholder="At least 8 characters"></label>${registering ? '<label class="form-field">Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required></label><label class="form-field">Invite code <span class="label">Optional</span><input name="invite_code" inputmode="numeric" placeholder="6-digit code"></label><label class="form-check"><input type="checkbox" name="accept_terms" required><span>I accept the terms and risk acknowledgement. Earnings are not guaranteed.</span></label>' : '<label class="form-field">Authenticator code <span class="label">If enabled</span><input name="totp_code" inputmode="numeric" autocomplete="one-time-code" placeholder="6-digit code"></label>'}<p class="form-note" data-form-error></p><button class="primary-button" type="submit">${registering ? 'Create account' : 'Sign in'}</button></form><button class="form-switch" data-action="auth-switch">${registering ? 'Already have an account? Sign in' : 'New here? Create an account'}</button>`, 'auth');
}

function actionForm(action, options = {}) {
	const forms = {
		deposit: {
			title: options.productName ? `Pay for ${safe(options.productName)}` : 'Deposit funds',
			intro: 'Send the exact amount to the provider number or wallet below, then enter your transaction ID. Your account is credited once matched.',
			fields: (() => {
				const methods = (options.methods && options.methods.length) ? options.methods : [
					{ key: 'mtn', name: 'MTN Mobile Money', number: '0770000000', account_name: 'DigiPowerX', currency: 'UGX' },
					{ key: 'airtel', name: 'Airtel Money', number: '0750000000', account_name: 'DigiPowerX', currency: 'UGX' },
					{ key: 'trc20', name: 'USDT (TRC20)', number: 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t', account_name: 'DigiPowerX USDT Wallet', currency: 'USDT' },
				];
				const first = methods[0];
				return `<div class="form-field"><label>1. Payment method<select name="payment_method" id="deposit-method-select">${methods.map((m) => `<option value="${safe(m.key)}" data-number="${safe(m.number)}" data-name="${safe(m.name)}" data-account="${safe(m.account_name || 'DigiPowerX')}" data-currency="${safe(m.currency || 'USDT')}">${safe(m.name)}</option>`).join('')}</select></label></div>` +
					`<div class="result-box" style="margin-top:2px; display:grid; gap:6px;">` +
					`<div style="display:flex; justify-content:space-between; align-items:center;"><span style="color:var(--muted); font-size:10px;">Provider:</span><strong id="deposit-provider-label" style="color:var(--ink); font-size:11px;">${safe(first.name)}</strong></div>` +
					`<div style="display:flex; justify-content:space-between; align-items:center;"><span style="color:var(--muted); font-size:10px;">Number / Wallet:</span><div style="display:flex; align-items:center; gap:6px;"><code id="deposit-number-label" style="color:var(--lime); font-size:10px; word-break:break-all;">${safe(first.number)}</code><button type="button" class="form-switch" style="padding:0; text-decoration:underline; cursor:pointer;" id="deposit-copy-number">Copy</button></div></div>` +
					`<div style="display:flex; justify-content:space-between; align-items:center;"><span style="color:var(--muted); font-size:10px;">Account name:</span><strong id="deposit-account-label" style="color:var(--ink); font-size:11px;">${safe(first.account_name || 'DigiPowerX')}</strong></div>` +
					`</div>` +
					`<label class="form-field">2. Amount (${safe(options.currency || 'UGX')})<input name="amount" type="number" id="deposit-amount" min="${safe(options.min || 5000)}" value="${safe(options.amount || '')}" step="any" placeholder="Minimum 5,000 UGX" required></label>` +
					`<label class="form-field">3. Transaction ID / Reference<input name="transaction_id" id="deposit-txid" required minlength="4" maxlength="120" placeholder="Enter reference from SMS or TX hash" autocomplete="off"></label>` +
					`<label class="form-check" style="margin-top:4px;"><input type="checkbox" name="confirmed" required><span>I confirm that I have sent the exact amount to the provided number / wallet.</span></label>`;
			})(),
			submit: 'Submit deposit',
		},
		withdraw: { title: 'Request withdrawal', intro: `Minimum ${amount(options.min || 5000)} UGX · ${safe(options.fee_pct || '0')}% fee. Payout is processed manually to your Mobile Money account.`, fields: `<label class="form-field">Wallet<select name="wallet"><option value="commission">Commission · ${amount(options.available?.commission)} UGX</option><option value="invest">Invest · ${amount(options.available?.invest)} UGX</option></select></label><label class="form-field">Payout provider<select name="payout_method"><option value="mtn">MTN Mobile Money</option><option value="airtel">Airtel Money</option></select></label><label class="form-field">Mobile Money phone number<input name="phone" type="tel" value="${safe(options.phone || state.profile?.phone || '')}" placeholder="e.g. 0770000000" required></label><label class="form-field">Amount (UGX)<input name="amount" type="number" min="${safe(options.min || 5000)}" step="1000" placeholder="Minimum 5,000 UGX" required></label><label class="form-field">Security PIN<input name="pin" type="password" inputmode="numeric" maxlength="6" required></label>${state.profile?.totp_enabled ? '<label class="form-field">Authenticator code<input name="totp_code" inputmode="numeric" maxlength="8"></label>' : ''}`, submit: 'Submit withdrawal' },
		transfer: { title: 'Transfer between wallets', intro: 'Move eligible funds between your own wallets in UGX. Locked principal cannot be transferred.', fields: `<label class="form-field">From<select name="from"><option value="commission">Commission</option><option value="invest">Invest</option></select></label><label class="form-field">To<select name="to"><option value="invest">Invest</option><option value="commission">Commission</option></select></label><label class="form-field">Amount (UGX)<input name="amount" type="number" min="1000" step="100" required></label><label class="form-field">Security PIN<input name="pin" type="password" inputmode="numeric" maxlength="6"></label>${state.profile?.totp_enabled ? '<label class="form-field">Authenticator code<input name="totp_code" inputmode="numeric" maxlength="8"></label>' : ''}`, submit: 'Transfer funds' },
		'set-pin': { title: 'Set security PIN', intro: 'Create a six-digit PIN to protect sensitive wallet actions.', fields: `<label class="form-field">Account password<input name="password" type="password" required></label><label class="form-field">New PIN<input name="new_pin" type="password" inputmode="numeric" minlength="6" maxlength="6" required></label>${state.profile?.has_pin ? '<label class="form-field">Current PIN<input name="current_pin" type="password" inputmode="numeric" maxlength="6" required></label>' : ''}`, submit: 'Save PIN' },
		password: { title: 'Change password', intro: 'Use a unique password with at least eight characters.', fields: `<label class="form-field">Current password<input name="current_password" type="password" required></label><label class="form-field">New password<input name="password" type="password" minlength="8" required></label><label class="form-field">Confirm new password<input name="password_confirmation" type="password" minlength="8" required></label>${state.profile?.has_pin ? '<label class="form-field">Security PIN<input name="pin" type="password" inputmode="numeric" maxlength="6"></label>' : ''}`, submit: 'Update password' },
	};
	const config = forms[action];
	showModal(`<p class="eyebrow">Account action</p><h2 id="modal-title">${config.title}</h2><p class="modal-intro">${config.intro}</p><form class="form-stack" id="action-form" data-action="${action}">${config.fields}<p class="form-note" data-form-error></p><button class="primary-button" type="submit">${config.submit}</button></form>`, action);
}

function updateForexPreview() {
	const from = $('#forex-from-select')?.value;
	const to = $('#forex-to-select')?.value;
	const amt = parseFloat($('#forex-amount-input')?.value || 0);
	const preview = $('#forex-estimated-receive');
	if (!preview) return;
	if (!amt || amt <= 0 || from === to) {
		preview.textContent = from === to ? 'Select different currencies' : '—';
		return;
	}
	const usdRate = Number(state.forexRates?.rates?.USD?.rate || state.forexRates?.USD_UGX || 4046.18);
	const eurRate = Number(state.forexRates?.rates?.EUR?.rate || state.forexRates?.EUR_UGX || 4465.57);
	let estimated = 0;
	let symbol = to;
	if (from === 'UGX' && to === 'USD') { estimated = amt / usdRate; symbol = '$ USD'; }
	else if (from === 'UGX' && to === 'EUR') { estimated = amt / eurRate; symbol = '€ EUR'; }
	else if (from === 'USD' && to === 'UGX') { estimated = amt * usdRate; symbol = 'UGX'; }
	else if (from === 'EUR' && to === 'UGX') { estimated = amt * eurRate; symbol = 'UGX'; }
	else if (from === 'USD' && to === 'EUR') { estimated = (amt * usdRate) / eurRate; symbol = '€ EUR'; }
	else if (from === 'EUR' && to === 'USD') { estimated = (amt * eurRate) / usdRate; symbol = '$ USD'; }
	preview.textContent = `≈ ${estimated.toLocaleString('en-US', { minimumFractionDigits: symbol === 'UGX' ? 0 : 2, maximumFractionDigits: symbol === 'UGX' ? 0 : 4 })} ${symbol}`;
}

function forexConvertModal() {
	const usdRate = Number(state.forexRates?.rates?.USD?.rate || state.forexRates?.USD_UGX || 4046.18);
	const eurRate = Number(state.forexRates?.rates?.EUR?.rate || state.forexRates?.EUR_UGX || 4465.57);
	const investUgx = state.profile?.invest_balance ?? state.home?.wallets?.invest ?? 0;
	const usdBal = state.profile?.usd_balance ?? 0;
	const eurBal = state.profile?.eur_balance ?? 0;

	showModal(`
		<p class="eyebrow">Forex Exchange</p>
		<h2 id="modal-title">Convert Mining Currency</h2>
		<p class="modal-intro">Convert UGX to USD/EUR for mining rounds, or convert your mining winnings back to UGX at live market rates.</p>
		<div style="background:var(--panel-hover,#111);padding:10px 12px;border-radius:6px;font-size:12px;margin-bottom:14px;border:1px solid var(--line);">
			<div>Live Market Rate: <strong>1 USD ≈ ${amount(usdRate, 0)} UGX</strong> &nbsp;·&nbsp; <strong>1 EUR ≈ ${amount(eurRate, 0)} UGX</strong></div>
			<div style="color:var(--muted);font-size:11px;margin-top:4px;">Available: <b>${amount(investUgx)} UGX</b> · <b>$${amount(usdBal, 2)} USD</b> · <b>€${amount(eurBal, 2)} EUR</b></div>
		</div>
		<form class="form-stack" id="forex-convert-form">
			<label class="form-field">From Currency
				<select name="from" id="forex-from-select">
					<option value="UGX">UGX (Invest Wallet: ${amount(investUgx)} UGX)</option>
					<option value="USD">USD (Mining: $${amount(usdBal, 2)})</option>
					<option value="EUR">EUR (Mining: €${amount(eurBal, 2)})</option>
				</select>
			</label>
			<label class="form-field">To Currency
				<select name="to" id="forex-to-select">
					<option value="USD">USD (For Mining)</option>
					<option value="EUR">EUR (For Mining)</option>
					<option value="UGX">UGX (Invest Wallet)</option>
				</select>
			</label>
			<label class="form-field">Amount to Convert
				<input name="amount" id="forex-amount-input" type="number" step="any" min="0.000001" required placeholder="Enter amount">
			</label>
			<div class="result-box" style="font-size:12px;margin:4px 0 10px 0;">
				Estimated Receive: <strong id="forex-estimated-receive" style="color:var(--lime);">—</strong>
			</div>
			<p class="form-note" data-form-error></p>
			<button class="primary-button" type="submit">Convert Currency</button>
		</form>
	`, 'forex-convert');
}

async function loadStatementsModal(page = 1) {
	const result = await api(`statements?page=${page}`);
	const rows = (result.data || []).map((row) => `<div class="list-row"><span>${safe(row.created_at || '')}</span><strong>${safe(row.type || 'Activity')}</strong><span>${amount(row.amount)} UGX</span><span>${safe(row.wallet || '')}</span></div>`).join('');
	showModal(`<p class="eyebrow">Wallet activity</p><h2 id="modal-title">Statements</h2><p class="modal-intro">Your ledger entries.</p><section class="panel list-panel">${rows || emptyPanel('No transactions yet.')}</section>${renderPagination(result, 'modal-statements-page')}`, 'statements');
}

async function loadCommissionsModal(page = 1) {
	const result = await api(`team/commissions?page=${page}`);
	const rows = (result.data || []).map((row) => `<div class="list-row"><span>${safe(row.created_at || '')}</span><strong>Referral commission</strong><span>${amount(row.amount)} UGX</span><span>${safe(row.wallet || '')}</span></div>`).join('');
	showModal(`<p class="eyebrow">Referral earnings</p><h2 id="modal-title">Commissions</h2><p class="modal-intro">Commissions credited from confirmed activity.</p><section class="panel list-panel">${rows || emptyPanel('No commissions yet.')}</section>${renderPagination(result, 'modal-commissions-page')}`, 'commissions');
}

async function loadNotificationsModal(page = 1) {
	const result = await api(`notifications?page=${page}`);
	const rows = (result.data || []).map((row) => `<div class="list-row"><span>${safe(row.created_at || '')}</span><strong>${safe(row.title)}</strong><span>${safe(row.body || '')}</span></div>`).join('');
	showModal(`<p class="eyebrow">Updates</p><h2 id="modal-title">Notifications</h2><p class="modal-intro">Account notices and updates.</p><section class="panel list-panel">${rows || emptyPanel('You have no notifications.')}</section>${renderPagination(result, 'modal-notifications-page')}`, 'notifications');
}

async function loadDepositsHistoryModal(page = 1) {
	const result = await api(`deposits?page=${page}`);
	const rows = (result.data || []).map((d) => `<div class="list-row"><span>${safe(d.created_at || '')}</span><strong>${amount(d.amount)} UGX</strong><span>TX: ${safe(d.txid || 'Pending')}</span><span><b class="tag ${d.status === 'confirmed' ? 'tag-good' : d.status === 'rejected' ? 'danger-button' : ''}">${safe(d.status)}</b></span></div>`).join('');
	showModal(`<p class="eyebrow">Wallet history</p><h2 id="modal-title">Deposit History</h2><p class="modal-intro">Your submitted deposits and approvals.</p><section class="panel list-panel">${rows || emptyPanel('No deposits recorded.')}</section>${renderPagination(result, 'modal-deposits-page')}`, 'deposits-history');
}

async function loadWithdrawalsHistoryModal(page = 1) {
	const result = await api(`withdrawals?page=${page}`);
	const rows = (result.data || []).map((w) => `<div class="list-row"><span>${safe(w.created_at || '')}</span><strong>${amount(w.amount)} UGX</strong><span>Net: ${amount(w.net_amount)} UGX</span><span><b class="tag ${w.status === 'completed' ? 'tag-good' : w.status === 'rejected' ? 'danger-button' : ''}">${safe(w.status)}</b></span></div>`).join('');
	showModal(`<p class="eyebrow">Wallet history</p><h2 id="modal-title">Withdrawal History</h2><p class="modal-intro">Your payout requests and status.</p><section class="panel list-panel">${rows || emptyPanel('No withdrawals recorded.')}</section>${renderPagination(result, 'modal-withdrawals-page')}`, 'withdrawals-history');
}

async function startAction(action, options = {}) {
	if (!state.token) { authForm(); return; }
	if (action === 'deposit') actionForm(action, { ...await api('deposit/options'), ...options });
	else if (action === 'withdraw') actionForm(action, await api('withdraw/options'));
	else if (['transfer', 'set-pin', 'password'].includes(action)) actionForm(action);
	else if (action === 'statements') await loadStatementsModal(1);
	else if (action === 'commissions') await loadCommissionsModal(1);
	else if (action === 'notifications') await loadNotificationsModal(1);
	else if (action === 'deposit-history') await loadDepositsHistoryModal(1);
	else if (action === 'withdrawal-history') await loadWithdrawalsHistoryModal(1);
	else if (action === 'faq') {
		const result = await api('faqs');
		const rows = (Array.isArray(result) ? result : result.data || []).map((item) => `<div class="faq-item"><strong>${safe(item.question)}</strong><p>${safe(item.answer)}</p></div>`).join('');
		showModal(`<p class="eyebrow">Help center</p><h2 id="modal-title">FAQs</h2><p class="modal-intro">Answers to common questions.</p><div class="faq-list">${rows || emptyPanel('No FAQs are published yet.')}</div>`, 'faq');
	} else if (['events', 'about', 'app'].includes(action)) {
		const result = await api(action);
		const content = action === 'events'
			? (Array.isArray(result) ? result : result.data || []).map((item) => `<div class="faq-item"><strong>${safe(item.title)}</strong><p>${safe(item.body || item.content || '')}</p></div>`).join('')
			: `<div class="info-banner"><span>${icon(action === 'app' ? 'app' : 'about')}</span><span>${safe(typeof result === 'string' ? result : result?.description || result?.download_url || 'Information is not available yet.')}</span></div>`;
		showModal(`<p class="eyebrow">DigiPowerX</p><h2 id="modal-title">${action === 'events' ? 'Events and updates' : action === 'app' ? 'Mobile app' : 'About us'}</h2><div class="faq-list">${content || emptyPanel('Nothing is published yet.')}</div>`, action);
	} else if (action === 'totp') {
		const result = await api('account/totp/setup', { method: 'POST' });
		showModal(`<p class="eyebrow">Account security</p><h2 id="modal-title">Authenticator setup</h2><p class="modal-intro">Add this secret to your authenticator app, then verify a code to enable it.</p><div class="result-box">${safe(result.secret)}</div><form class="form-stack" id="totp-form"><label class="form-field">Verification code<input name="code" inputmode="numeric" required></label><p class="form-note" data-form-error></p><button class="primary-button" type="submit">Enable authenticator</button></form>`, 'totp');
	}
}

async function submitAuth(form) {
	const data = Object.fromEntries(new FormData(form).entries());
	const mode = form.dataset.mode;
	const errorElement = $('[data-form-error]', form);
	const button = $('button[type="submit"]', form);
	button.disabled = true; errorElement.textContent = '';
	try {
		let response;
		if (mode === 'register') response = await api('auth/register', { method: 'POST', body: JSON.stringify({ email: data.identifier.includes('@') ? data.identifier : null, phone: data.identifier.includes('@') ? null : data.identifier, password: data.password, password_confirmation: data.password_confirmation, invite_code: data.invite_code || null, accept_terms: data.accept_terms === 'on' ? 1 : 0, accept_risk: data.accept_terms === 'on' ? 1 : 0 }) });
		else response = await api('auth/login', { method: 'POST', body: JSON.stringify({ identifier: data.identifier, password: data.password, totp_code: data.totp_code || null }) });
		state.token = response.token; sessionStorage.setItem('dp_token', response.token); state.profile = response.user;
		closeModal(); toast(mode === 'register' ? 'Account created.' : 'Signed in.'); await loadPage(state.page);
	} catch (error) { errorElement.textContent = error.message; }
	finally { button.disabled = false; }
}

async function submitAction(form) {
	const data = Object.fromEntries(new FormData(form).entries());
	const action = form.dataset.action;
	const errorElement = $('[data-form-error]', form);
	const button = $('button[type="submit"]', form);
	errorElement.textContent = ''; button.disabled = true;
	try {
		let result;
		if (action === 'deposit') {
			result = await api('deposits', { method: 'POST', body: JSON.stringify(data) });
			toast('Deposit submitted. A manager will match the transaction and credit your account.');
			closeModal();
			await loadPage(state.page);
			return;
		}
		if (action === 'withdraw') result = await api('withdrawals', { method: 'POST', body: JSON.stringify(data) });
		else if (action === 'transfer') result = await api('transfer', { method: 'POST', body: JSON.stringify(data) });
		else if (action === 'set-pin') result = await api('account/pin', { method: 'POST', body: JSON.stringify(data) });
		else if (action === 'password') result = await api('account/password', { method: 'POST', body: JSON.stringify(data) });
		toast(result?.message || 'Request submitted.'); closeModal(); await loadPage(state.page);
	} catch (error) { errorElement.textContent = error.message; }
	finally { button.disabled = false; }
}

document.addEventListener('click', async (event) => {
	const pageButton = event.target.closest('[data-page]');
	if (pageButton) { await loadPage(pageButton.dataset.page); return; }
	const levelButton = event.target.closest('[data-team-level]');
	if (levelButton) { state.teamLevel = Number(levelButton.dataset.teamLevel); state.teamMembersPage = 1; await loadPage('team'); return; }
	const statusButton = event.target.closest('[data-mining-status]');
	if (statusButton) { state.miningStatus = statusButton.dataset.miningStatus; await loadPage('mining'); return; }
	const miningTabBtn = event.target.closest('[data-mining-tab]');
	if (miningTabBtn) {
		state.miningTab = miningTabBtn.dataset.miningTab;
		state.miningHistoryPage = 1;
		state.miningRoundsPage = 1;
		await loadPage('mining');
		return;
	}
	const button = event.target.closest('[data-action]');
	if (!button) return;
	const action = button.dataset.action;
	try {
		if (action === 'auth') {
			if (state.token && state.profile) {
				showModal(`
					<p class="eyebrow">Connected Account</p>
					<h2 id="modal-title">${safe(state.profile.name || state.profile.identity_masked)}</h2>
					<p class="modal-intro" style="margin-bottom:14px;">${safe(state.profile.email || state.profile.phone || 'Signed in member')}</p>
					<div style="padding:12px 14px;border:1px solid var(--line);border-radius:6px;background:#111613;margin-bottom:18px;font-size:11px;">
						<div style="display:flex;justify-content:space-between;margin-bottom:6px;"><span style="color:var(--muted);">Invite code:</span><b>${safe(state.profile.invite_code || '—')}</b></div>
						<div style="display:flex;justify-content:space-between;"><span style="color:var(--muted);">Status:</span><span style="color:var(--lime);font-weight:600;">Signed in</span></div>
					</div>
					<div style="display:grid;gap:8px;">
						<button class="primary-button" data-page="me" data-action="close-modal">Open My Account</button>
						<button class="secondary-button" data-action="logout" style="color:var(--red);">Sign out</button>
					</div>
				`, 'account-status');
			} else {
				authForm();
			}
		}
		else if (action === 'toggle-theme') toggleTheme();
		else if (action === 'auth-switch') authForm($('#auth-form')?.dataset.mode === 'login' ? 'register' : 'login');
		else if (action === 'close-modal') closeModal();
		else if (action === 'toggle-balance') { state.hiddenBalance = !state.hiddenBalance; renderHome(); }
		else if (action === 'product-deposit') await startAction('deposit', { amount: button.dataset.productMin, productName: button.dataset.productName });
		else if (action === 'product-purchase') showModal(`<p class="eyebrow">Product payment</p><h2 id="modal-title">Buy ${safe(state.products.find((product) => String(product.id) === button.dataset.productId)?.name || 'product')}</h2><p class="modal-intro">Pay from an internal wallet for immediate activation, or submit a manual transaction for admin review.</p><form class="form-stack" id="product-purchase-form" data-product-id="${safe(button.dataset.productId)}"><label class="form-field">Payment source<select name="payment_source"><option value="manual">Manual external payment</option><option value="invest">Invest wallet</option><option value="commission">Commission wallet</option></select></label><label class="form-field">Transaction ID (manual only)<input name="transaction_id" maxlength="120"></label><p class="form-note" data-form-error></p><button class="primary-button" type="submit">Submit purchase</button></form>`, 'product-purchase');
		else if (action === 'vip-purchase') showModal(`<p class="eyebrow">VIP qualification</p><h2 id="modal-title">Apply for ${safe(state.vip?.packages?.find((pkg) => String(pkg.id) === button.dataset.levelId)?.name || 'VIP')}</h2><p class="modal-intro">Your active product and qualified team are checked before payment. Wallet payments activate immediately; manual payments wait for admin review.</p><form class="form-stack" id="vip-purchase-form" data-level-id="${safe(button.dataset.levelId)}"><label class="form-field">Payment source<select name="payment_source"><option value="manual">Manual external payment</option><option value="invest">Invest wallet</option><option value="commission">Commission wallet</option></select></label><label class="form-field">Transaction ID (manual only)<input name="transaction_id" required maxlength="120"></label><p class="form-note" data-form-error></p><button class="primary-button" type="submit">Submit VIP application</button></form>`, 'vip-purchase');
		else if (action === 'product-cashout') showModal(`<p class="eyebrow">Product earnings</p><h2 id="modal-title">Cash out earnings</h2><form class="form-stack" id="product-cashout-form" data-purchase-id="${safe(button.dataset.purchaseId)}"><label class="form-field">Payout provider<select name="payout_method"><option value="mtn">MTN Mobile Money</option><option value="airtel">Airtel Money</option></select></label><label class="form-field">Mobile Money phone number<input name="phone" type="tel" value="${safe(state.profile?.phone || '')}" placeholder="e.g. 0770000000" required></label><p class="form-note" data-form-error></p><button class="primary-button" type="submit">Request cash-out</button></form>`, 'product-cashout');
		else if (action === 'vip') await loadPage('vip');
		else if (action === 'invite') await loadPage('team');
		else if (['events', 'about', 'app'].includes(action)) await startAction(action);
		else if (action === 'share') { await navigator.clipboard.writeText(state.team?.invite_link || window.location.href); toast(`Shared via ${button.dataset.share}.`); }
		else if (action === 'save-admin-prices') {
			const overrides = {};
			$$('.admin-price-row', $('#admin-price-form')).forEach((row) => {
				const price = $('input[name="price"]', row);
				const change = $('input[name="change_pct"]', row);
				const enabled = $('input[name="enabled"]', row);
				const symbol = price.dataset.symbol;
				const exchange = price.dataset.exchange;
				overrides[symbol] ??= {};
				overrides[symbol][exchange] = { enabled: enabled.checked, price: price.value || null, change_pct: change.value || 0 };
			});
			await api('admin/prices', { method: 'PUT', body: JSON.stringify({ overrides }) });
			toast('Price controls saved.'); await loadPage('admin');
		}
		else if (action === 'toggle-client') {
			await api(`admin/users/${button.dataset.clientId}/toggle`, { method: 'POST' });
			toast('Client status updated.'); await loadPage('admin');
		}
		else if (action === 'archive-client') {
			if (!window.confirm('Archive and anonymize this client? Financial records will be preserved and the client will not be able to sign in.')) return;
			await api(`admin/users/${button.dataset.clientId}`, { method: 'DELETE' });
			toast('Client archived.'); await loadPage('admin');
		}
		else if (action === 'save-product') {
			const productId = button.dataset.productId;
			const fields = $$(`[data-product-id="${productId}"]`);
			const payload = Object.fromEntries(fields.map((field) => [field.dataset.productField, field.value || null]));
			const product = state.admin.products.find((item) => String(item.id) === productId);
			await api(`admin/products/${productId}`, { method: 'PUT', body: JSON.stringify({ code: product.code, name: product.name, image_path: product.image_path, tagline: product.tagline, sort_order: product.sort_order, is_active: product.is_active, ...payload }) });
			toast('Product updated.'); await loadPage('admin');
		}
		else if (action === 'switch-admin-tab') {
			state.adminTab = button.dataset.adminTab;
			renderAdmin();
		}
		else if (action === 'filter-clients') {
			state.adminClientFilter = button.dataset.filter;
			state.adminClientPage = 1;
			await loadPage('admin');
		}
		else if (action === 'search-clients-btn') {
			const input = $('#admin-client-search-input');
			state.adminClientSearch = input ? input.value.trim() : '';
			state.adminClientPage = 1;
			await loadPage('admin');
		}
		else if (action === 'clear-client-search') {
			state.adminClientSearch = '';
			state.adminClientPage = 1;
			await loadPage('admin');
		}
		else if (action === 'admin-client-page') {
			state.adminClientPage = parseInt(button.dataset.page, 10);
			await loadPage('admin');
		}
		else if (action === 'admin-deposit-page') {
			state.adminDepositPage = parseInt(button.dataset.page, 10);
			await loadPage('admin');
		}
		else if (action === 'admin-withdrawal-page') {
			state.adminWithdrawalPage = parseInt(button.dataset.page, 10);
			await loadPage('admin');
		}
		else if (action === 'admin-purchase-page') {
			state.adminPurchasePage = parseInt(button.dataset.page, 10);
			await loadPage('admin');
		}
		else if (action === 'filter-admin-tx-status') {
			state.adminTxStatus = button.dataset.status;
			state.adminDepositPage = 1;
			state.adminWithdrawalPage = 1;
			state.adminPurchasePage = 1;
			await loadPage('admin');
		}
		else if (action === 'team-members-page') {
			state.teamMembersPage = parseInt(button.dataset.page, 10);
			await loadPage('team');
		}
		else if (action === 'mining-history-page') {
			state.miningHistoryPage = parseInt(button.dataset.page, 10);
			await loadPage('mining');
		}
		else if (action === 'mining-rounds-page') {
			state.miningRoundsPage = parseInt(button.dataset.page, 10);
			await loadPage('mining');
		}
		else if (action === 'modal-statements-page') {
			await loadStatementsModal(parseInt(button.dataset.page, 10));
		}
		else if (action === 'modal-commissions-page') {
			await loadCommissionsModal(parseInt(button.dataset.page, 10));
		}
		else if (action === 'modal-notifications-page') {
			await loadNotificationsModal(parseInt(button.dataset.page, 10));
		}
		else if (action === 'modal-deposits-page') {
			await loadDepositsHistoryModal(parseInt(button.dataset.page, 10));
		}
		else if (action === 'modal-withdrawals-page') {
			await loadWithdrawalsHistoryModal(parseInt(button.dataset.page, 10));
		}
		else if (action === 'deposit-history') {
			await loadDepositsHistoryModal(1);
		}
		else if (action === 'withdrawal-history') {
			await loadWithdrawalsHistoryModal(1);
		}
		else if (action === 'open-client-chat') {
			state.chatType = 'private';
			state.chatTargetUser = button.dataset.clientId;
			state.page = 'chat';
			await loadPage('chat');
		}
		else if (action === 'confirm-deposit') {
			await api(`admin/deposits/${button.dataset.depositId}/confirm`, { method: 'POST' });
			toast('Deposit confirmed.');
			await loadPage('admin');
		}
		else if (action === 'reject-deposit') {
			const note = window.prompt('Reason for rejecting deposit:');
			if (note) {
				await api(`admin/deposits/${button.dataset.depositId}/reject`, { method: 'POST', body: JSON.stringify({ note }) });
				toast('Deposit rejected.');
				await loadPage('admin');
			}
		}
		else if (action === 'approve-withdrawal') {
			await api(`admin/withdrawals/${button.dataset.withdrawalId}/approve`, { method: 'POST' });
			toast('Withdrawal approved.');
			await loadPage('admin');
		}
		else if (action === 'complete-withdrawal') {
			const txid = window.prompt('Enter transaction reference / payment TXID (optional):', '');
			await api(`admin/withdrawals/${button.dataset.withdrawalId}/complete`, { method: 'POST', body: JSON.stringify({ txid }) });
			toast('Withdrawal marked as completed.');
			await loadPage('admin');
		}
		else if (action === 'reject-withdrawal') {
			const note = window.prompt('Reason for rejecting withdrawal:');
			if (note) {
				await api(`admin/withdrawals/${button.dataset.withdrawalId}/reject`, { method: 'POST', body: JSON.stringify({ note }) });
				toast('Withdrawal rejected and refunded.');
				await loadPage('admin');
			}
		}
		else if (action === 'toggle-admin-user') {
			await api(`super-admin/admins/${button.dataset.adminId}/toggle`, { method: 'POST' });
			toast('Staff account status updated.');
			await loadPage('admin');
		}
		else if (action === 'edit-admin-role') {
			const adminId = button.dataset.adminId;
			const currentRole = button.dataset.adminRole;
			const adminName = button.dataset.adminName;
			const newRole = window.prompt(`Change role for ${adminName}:\nEnter "admin" for Operations Admin, or "manager" for Customer Manager:`, currentRole);
			if (newRole && (newRole === 'admin' || newRole === 'manager')) {
				await api(`super-admin/admins/${adminId}`, {
					method: 'PUT',
					body: JSON.stringify({ name: adminName, role: newRole })
				});
				toast(`Role updated to ${newRole === 'admin' ? 'Operations Admin' : 'Customer Manager'}.`);
				await loadPage('admin');
			} else if (newRole) {
				toast('Invalid role. Please enter "admin" or "manager".');
			}
		}
		else if (action === 'activate-purchase') { await api(`admin/purchases/${button.dataset.purchaseId}/activate`, { method: 'POST' }); toast('Product purchase activated.'); await loadPage('admin'); }
		else if (action === 'reject-purchase') { const note = window.prompt('Reason for rejection:'); if (note) { await api(`admin/purchases/${button.dataset.purchaseId}/reject`, { method: 'POST', body: JSON.stringify({ note }) }); toast('Product purchase rejected.'); await loadPage('admin'); } }
		else if (action === 'activate-vip-purchase') { await api(`admin/vip-purchases/${button.dataset.purchaseId}/activate`, { method: 'POST' }); toast('VIP application activated.'); await loadPage('admin'); }
		else if (action === 'reject-vip-purchase') { const note = window.prompt('Reason for rejection:'); if (note) { await api(`admin/vip-purchases/${button.dataset.purchaseId}/reject`, { method: 'POST', body: JSON.stringify({ note }) }); toast('VIP application rejected.'); await loadPage('admin'); } }
		else if (action === 'refresh') { state.home = null; await loadPage(state.page); }
		else if (action === 'copy-invite') { await navigator.clipboard.writeText(state.team?.invite_link || ''); toast('Invite link copied.'); }
		else if (action === 'logout') { if (state.token) await api('auth/logout', { method: 'POST' }); clearSession(); state.team = null; state.vip = null; state.mining = null; closeModal(); await loadPage('home'); toast('Signed out.'); }
		else if (action === 'set-stake-amount') {
			state.stakeInputAmount = button.dataset.val;
			const inp = $('#stake-amount-input');
			if (inp) {
				inp.value = button.dataset.val;
				inp.focus();
			}
		}
		else if (action === 'stake-enter') {
			const inp = $('#stake-amount-input');
			const amountVal = inp?.value || '10';
			const side = button.dataset.side;
			await api('mining/enter', { method: 'POST', body: JSON.stringify({ side, amount: amountVal }) });
			toast(`Stake placed: ${amountVal} USDT on ${side.toUpperCase()}`);
			await loadPage('mining');
		}
		else if (action === 'stake-cashout') {
			await api('mining/cashout', { method: 'POST' });
			toast('Cash-out requested. Processing at tick close.');
			await loadPage('mining');
		}
		else if (action === 'stake-confirm-adult-modal') {
			showModal(`<p class="eyebrow">Age verification</p><h2 id="modal-title">Confirm 18+ to Play</h2><p class="modal-intro">Stake Rounds is a game of chance. You must be at least 18 years old to take part.</p><form class="form-stack" id="stake-confirm-adult-form"><label class="form-field">Date of Birth<input type="date" name="date_of_birth" required></label><label class="form-check"><input type="checkbox" name="accept" required><span>I confirm I am 18+ and accept the rules and risks.</span></label><p class="form-note" data-form-error></p><button class="primary-button" type="submit">Confirm 18+</button></form>`, 'adult-confirm');
		}
		else if (action === 'stake-limit-modal') {
			showModal(`<p class="eyebrow">Responsible gaming</p><h2 id="modal-title">Set Daily Stake Limit</h2><p class="modal-intro">Lowering limits applies immediately; raising limits applies after a 24-hour cooling-off period.</p><form class="form-stack" id="stake-limit-form"><label class="form-field">Daily limit (USDT)<input type="number" min="1" max="100000" step="1" name="daily_limit" value="${safe(state.mining?.limits?.daily_limit || 100)}" required></label><p class="form-note" data-form-error></p><button class="primary-button" type="submit">Save Limit</button></form>`, 'stake-limit');
		}
		else if (action === 'stake-exclude-modal') {
			showModal(`<p class="eyebrow">Responsible gaming</p><h2 id="modal-title">Self-Exclusion</h2><p class="modal-intro">Excluding locks you out of Stake Rounds for the selected period. This cannot be shortened or cancelled.</p><form class="form-stack" id="stake-exclude-form"><label class="form-field">Exclusion Duration<select name="days"><option value="1">24 Hours</option><option value="7">7 Days</option><option value="30">30 Days</option><option value="90">90 Days</option><option value="180">180 Days</option><option value="365">365 Days</option></select></label>${state.profile?.has_pin ? '<label class="form-field">Security PIN<input name="pin" type="password" inputmode="numeric" maxlength="6" required></label>' : ''}<p class="form-note" data-form-error></p><button class="primary-button" style="background:#e55353;" type="submit">Confirm Self-Exclusion</button></form>`, 'stake-exclude');
		}
		else if (action === 'stake-verify') {
			const roundId = button.dataset.roundId;
			const res = await api(`mining/rounds/${roundId}/verify`);
			showModal(`<p class="eyebrow">Provably fair audit</p><h2 id="modal-title">Verify Round #${safe(res.id)}</h2><p class="modal-intro">Every round is deterministic and can be independently verified.</p><div class="result-box" style="text-align:left;font-size:11px;line-height:1.6;overflow:auto;max-height:280px;"><strong>Server Seed (revealed):</strong><br><code>${safe(res.server_seed)}</code><br><br><strong>Server Seed Hash:</strong><br><code>${safe(res.server_seed_hash)}</code><br><br><strong>Client Seed:</strong><br><code>${safe(res.client_seed)}</code><br><br><strong>End Reason:</strong> ${safe(res.end_reason)}<br><strong>House Take:</strong> ${amount(res.house_take)} USDT<br><br><strong>Algorithm:</strong><br><code>${safe(res.algorithm)}</code></div><button class="primary-button" style="margin-top:14px;" data-action="close-modal">Close</button>`, 'stake-verify');
		}
		else if (action === 'claim') { const result = await api('mining/claim', { method: 'POST', body: JSON.stringify({}) }).catch(() => ({ claimed: 0 })); toast(`Claimed ${amount(result.claimed)} USDT.`); await loadPage('mining'); }
		else if (action === 'forex-convert-modal') {
			if (!state.forexRates) {
				state.forexRates = await api('forex/rates').catch(() => null);
			}
			forexConvertModal();
		}
		else if (action === 'convert-all-to-ugx') {
			const usd = Number(state.profile?.usd_balance || 0);
			const eur = Number(state.profile?.eur_balance || 0);
			if (usd <= 0 && eur <= 0) {
				toast('No USD or EUR balance to convert.');
				return;
			}
			button.disabled = true;
			try {
				if (usd > 0) {
					await api('forex/convert', { method: 'POST', body: JSON.stringify({ from: 'USD', to: 'UGX', amount: usd }) });
				}
				if (eur > 0) {
					await api('forex/convert', { method: 'POST', body: JSON.stringify({ from: 'EUR', to: 'UGX', amount: eur }) });
				}
				state.profile = await api('me');
				toast('All forex balances converted to UGX successfully!');
				await loadPage('mining');
			} catch (err) {
				toast(err.message);
			} finally {
				button.disabled = false;
			}
		}
		else if (action === 'open-private-chat') {
			state.chatType = 'private';
			state.chatTargetUser = null;
			await loadPage('chat');
		}
		else if (action === 'switch-chat-tab') {
			state.chatType = button.dataset.tab;
			state.chatTargetUser = null;
			await loadPage('chat');
		}
		else if (action === 'chat-pick-file') {
			$('#chat-file-input')?.click();
		}
		else if (action === 'chat-toggle-tags') {
			state.chatShowTags = !state.chatShowTags;
			renderChat();
		}
		else if (action === 'quick-tag' || action === 'tag-chat-user') {
			const u = button.dataset.user;
			const inp = $('#chat-input-text');
			if (inp) {
				inp.value = (inp.value ? inp.value.trim() + ' ' : '') + `@${u} `;
				inp.focus();
			}
		}
		else if (action === 'reply-chat-message') {
			const msgId = Number(button.dataset.msgId);
			const target = (state.chatMessages || []).find((m) => m.id === msgId);
			if (target) {
				state.chatReplyingTo = target;
				renderChatInputPreview();
				$('#chat-input-text')?.focus();
			}
		}
		else if (action === 'cancel-chat-reply') {
			state.chatReplyingTo = null;
			renderChatInputPreview();
		}
		else if (action === 'cancel-chat-attachment') {
			state.chatAttachmentFile = null;
			state.chatAttachmentPreview = null;
			renderChatInputPreview();
		}
		else if (action === 'delete-chat-message') {
			if (window.confirm('Delete this message?')) {
				await api(`chat/messages/${button.dataset.msgId}`, { method: 'DELETE' });
				state.chatMessages = state.chatMessages.filter((m) => String(m.id) !== button.dataset.msgId);
				$(`#chat-msg-${button.dataset.msgId}`)?.remove();
				toast('Message deleted.');
			}
		}
		else if (action === 'view-image') {
			const imgUrl = button.dataset.imgUrl;
			const existing = $('#chat-lightbox-view');
			if (existing) existing.remove();
			const lb = document.createElement('div');
			lb.className = 'chat-lightbox';
			lb.id = 'chat-lightbox-view';
			lb.innerHTML = `<button type="button" class="chat-lightbox-close" data-action="close-lightbox">×</button><img src="${safe(imgUrl)}" alt="Full photo">`;
			document.body.appendChild(lb);
		}
		else if (action === 'close-lightbox') {
			$('#chat-lightbox-view')?.remove();
		}
		else if (action === 'focus-chat-message') {
			const el = $(`#chat-msg-${button.dataset.msgId}`);
			if (el) {
				el.scrollIntoView({ behavior: 'smooth', block: 'center' });
				const bubble = el.querySelector('.chat-bubble');
				if (bubble) {
					bubble.style.filter = 'brightness(1.5)';
					setTimeout(() => { bubble.style.filter = 'none'; }, 1000);
				}
			}
		}
		else if (action === 'pick-avatar') {
			$('#avatar-input')?.click();
		}
		else if (action === 'remove-avatar') {
			if (window.confirm('Are you sure you want to remove your profile picture?')) {
				const res = await api('account/avatar', { method: 'DELETE' });
				if (state.profile) state.profile.avatar_url = null;
				updateIdentity();
				renderMe();
				toast(res.message || 'Profile picture removed.');
			}
		}
		else await startAction(action);
	} catch (error) { toast(error.message); }
});

document.addEventListener('submit', async (event) => {
	const form = event.target;
	if (form.dataset.action === 'assign-product-form') {
		event.preventDefault();
		const clientId = form.dataset.clientId;
		const productId = form.product_id?.value;
		if (!productId) { toast('Please choose an equipment product to assign.'); return; }
		try {
			await api(`admin/users/${clientId}/assign-product`, {
				method: 'POST',
				body: JSON.stringify({ product_id: productId })
			});
			toast('Equipment successfully assigned to client.');
			await loadPage('admin');
		} catch (error) { toast(error.message); }
	}
	else if (form.id === 'auth-form') { event.preventDefault(); await submitAuth(form); }
	else if (form.id === 'create-admin-form') {
		event.preventDefault();
		const errorElement = $('[data-form-error]', form);
		try {
			await api('super-admin/admins', { method: 'POST', body: JSON.stringify(Object.fromEntries(new FormData(form).entries())) });
			form.reset(); toast('Administrator account created.'); await loadPage('admin');
		} catch (error) { errorElement.textContent = error.message; }
	}
	else if (form.id === 'action-form') { event.preventDefault(); await submitAction(form); }
	else if (form.id === 'txid-form') {
		event.preventDefault(); const button = $('button[type="submit"]', form); const errorElement = $('[data-form-error]', form); button.disabled = true;
		try { await api(`deposits/${form.dataset.depositId}/txid`, { method: 'POST', body: JSON.stringify(Object.fromEntries(new FormData(form).entries())) }); closeModal(); toast('Transaction ID submitted for review.'); await loadPage(state.page); }
		catch (error) { errorElement.textContent = error.message; }
		finally { button.disabled = false; }
	} else if (form.id === 'totp-form') {
		event.preventDefault(); const button = $('button[type="submit"]', form); const errorElement = $('[data-form-error]', form); button.disabled = true;
		try { const data = Object.fromEntries(new FormData(form).entries()); await api('account/totp/enable', { method: 'POST', body: JSON.stringify(data) }); if (state.profile) state.profile.totp_enabled = true; closeModal(); renderMe(); toast('Authenticator enabled.'); }
		catch (error) { errorElement.textContent = error.message; }
		finally { button.disabled = false; }
	} else if (form.id === 'admin-announcement-form') {
		event.preventDefault();
		const data = Object.fromEntries(new FormData(form).entries());
		try {
			await api('admin/announcements', { method: 'POST', body: JSON.stringify({ ...data, is_active: data.is_active === 'on' }) });
			form.reset(); toast('Announcement published.'); await loadPage('admin');
		} catch (error) { toast(error.message); }
	}
	else if (form.id === 'stake-confirm-adult-form') {
		event.preventDefault(); const button = $('button[type="submit"]', form); const errorElement = $('[data-form-error]', form); button.disabled = true;
		try { const data = Object.fromEntries(new FormData(form).entries()); await api('mining/profile/confirm-adult', { method: 'POST', body: JSON.stringify({ date_of_birth: data.date_of_birth, accept: data.accept === 'on' ? 1 : 0 }) }); closeModal(); toast('Age confirmed.'); await loadPage('mining'); }
		catch (error) { errorElement.textContent = error.message; }
		finally { button.disabled = false; }
	}
	else if (form.id === 'stake-limit-form') {
		event.preventDefault(); const button = $('button[type="submit"]', form); const errorElement = $('[data-form-error]', form); button.disabled = true;
		try { const data = Object.fromEntries(new FormData(form).entries()); await api('mining/profile/limit', { method: 'POST', body: JSON.stringify({ daily_limit: data.daily_limit }) }); closeModal(); toast('Daily limit updated.'); await loadPage('mining'); }
		catch (error) { errorElement.textContent = error.message; }
		finally { button.disabled = false; }
	}
	else if (form.id === 'stake-exclude-form') {
		event.preventDefault(); const button = $('button[type="submit"]', form); const errorElement = $('[data-form-error]', form); button.disabled = true;
		try { const data = Object.fromEntries(new FormData(form).entries()); await api('mining/profile/exclude', { method: 'POST', body: JSON.stringify(data) }); closeModal(); toast('Self-exclusion active.'); await loadPage('mining'); }
		catch (error) { errorElement.textContent = error.message; }
		finally { button.disabled = false; }
	}
	else if (form.id === 'product-purchase-form') {
		event.preventDefault();
		const button = $('button[type="submit"]', form);
		const errorElement = $('[data-form-error]', form);
		button.disabled = true;
		try {
			await api(`products/${form.dataset.productId}/purchases`, { method: 'POST', body: JSON.stringify({ payment_source: form.elements.payment_source.value, transaction_id: form.elements.transaction_id.value || null }) });
			closeModal(); toast('Purchase submitted for admin review.'); await loadPage('products');
		} catch (error) { errorElement.textContent = error.message; } finally { button.disabled = false; }
	}
	else if (form.id === 'vip-purchase-form') {
		event.preventDefault();
		const button = $('button[type="submit"]', form);
		const errorElement = $('[data-form-error]', form);
		button.disabled = true;
		try {
			await api(`vip/${form.dataset.levelId}/purchases`, { method: 'POST', body: JSON.stringify({ payment_source: form.elements.payment_source.value, transaction_id: form.elements.transaction_id.value || null }) });
			closeModal(); toast('VIP application submitted.'); await loadPage('vip');
		} catch (error) { errorElement.textContent = error.message; } finally { button.disabled = false; }
	}
	else if (form.id === 'product-cashout-form') {
		event.preventDefault();
		const button = $('button[type="submit"]', form);
		const errorElement = $('[data-form-error]', form);
		button.disabled = true;
		try {
			await api(`purchases/${form.dataset.purchaseId}/cash-out`, { method: 'POST', body: JSON.stringify({ payout_method: form.elements.payout_method?.value || 'mtn', phone: form.elements.phone?.value || state.profile?.phone || '' }) });
			closeModal(); toast('Cash-out request submitted for admin approval.'); await loadPage('me');
		} catch (error) { errorElement.textContent = error.message; } finally { button.disabled = false; }
	}
	else if (form.id === 'forex-convert-form') {
		event.preventDefault();
		const button = $('button[type="submit"]', form);
		const errorElement = $('[data-form-error]', form);
		button.disabled = true;
		errorElement.textContent = '';
		try {
			const data = Object.fromEntries(new FormData(form).entries());
			const res = await api('forex/convert', { method: 'POST', body: JSON.stringify(data) });
			if (res.user && state.profile) {
				state.profile.invest_balance = res.user.invest_balance;
				state.profile.usd_balance = res.user.usd_balance;
				state.profile.eur_balance = res.user.eur_balance;
			} else {
				state.profile = await api('me');
			}
			closeModal();
			toast(res.message || 'Conversion completed.');
			await loadPage('mining');
		} catch (error) {
			errorElement.textContent = error.message;
		} finally {
			button.disabled = false;
		}
	}
	else if (form.id === 'chat-send-form') {
		event.preventDefault();
		await sendChatMessage();
	}
});

document.addEventListener('change', (event) => {
	if (event.target.id === 'deposit-method-select') {
		const opt = event.target.selectedOptions?.[0];
		if (!opt) return;
		const prov = $('#deposit-provider-label');
		const num = $('#deposit-number-label');
		const acc = $('#deposit-account-label');
		if (prov) prov.textContent = opt.dataset.name || opt.text;
		if (num) num.textContent = opt.dataset.number || '';
		if (acc) acc.textContent = opt.dataset.account || 'DigiPowerX';
	}
	if (event.target.id === 'forex-from-select' || event.target.id === 'forex-to-select') {
		updateForexPreview();
	}
	if (event.target.id === 'avatar-input' && event.target.files?.[0]) {
		const file = event.target.files[0];
		if (!file.type.startsWith('image/')) {
			toast('Please select a valid image file (JPEG, PNG, WebP, GIF).');
			event.target.value = '';
			return;
		}
		if (file.size > 5 * 1024 * 1024) {
			toast('Avatar image must not exceed 5MB.');
			event.target.value = '';
			return;
		}
		const fd = new FormData();
		fd.append('avatar', file);
		toast('Uploading profile picture...');
		api('account/avatar', { method: 'POST', body: fd })
			.then((res) => {
				if (state.profile) {
					state.profile.avatar_url = res.avatar_url;
				}
				updateIdentity();
				renderMe();
				toast(res.message || 'Profile picture updated successfully!');
			})
			.catch((err) => {
				toast(err.message || 'Failed to upload profile picture.');
			})
			.finally(() => {
				event.target.value = '';
			});
	}
	if (event.target.id === 'chat-file-input' && event.target.files?.[0]) {
		const file = event.target.files[0];
		const reader = new FileReader();
		reader.onload = (e) => {
			state.chatAttachmentFile = file;
			state.chatAttachmentPreview = e.target.result;
			renderChatInputPreview();
		};
		reader.readAsDataURL(file);
	}
});

document.addEventListener('click', (event) => {
	if (event.target.id === 'deposit-copy-number' || event.target.closest('#deposit-copy-number')) {
		const num = $('#deposit-number-label')?.textContent;
		if (num) {
			navigator.clipboard.writeText(num).then(() => toast('Copied to clipboard: ' + num)).catch(() => { });
		}
	}
});

$('#refresh-button').addEventListener('click', async () => { if (state.token) state.profile = await api('me').catch(() => state.profile); await loadPage(state.page); });
$('#modal-backdrop').addEventListener('click', (event) => { if (event.target.id === 'modal-backdrop') closeModal(); });
document.addEventListener('keydown', (event) => {
	if (event.key === 'Escape' && state.modal) closeModal();
	if (event.key === 'Escape' && $('#chat-lightbox-view')) $('#chat-lightbox-view').remove();
	if (event.target.id === 'chat-input-text' && event.key === 'Enter' && !event.shiftKey) {
		event.preventDefault();
		sendChatMessage();
	}
});
document.addEventListener('input', (event) => {
	if (event.target.id === 'stake-amount-input') {
		state.stakeInputAmount = event.target.value;
	}
	if (event.target.id === 'forex-amount-input') {
		updateForexPreview();
	}
});
async function boot() { renderIcons(); updateThemeToggle(); await loadProfile(); await loadPage('home'); startHomeRefresh(); }
boot();
