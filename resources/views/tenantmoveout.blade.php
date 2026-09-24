<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH - Account Closed</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
{{-- Shared tenant palette (forest-green scale, cream background) and topbar --}}
<link rel="stylesheet" href="{{ asset('css/tenant.css') }}">
<style>
  /* This page has no sidebar, so the topbar sits directly in <body>. */
  .topbar-logo{ display:flex; align-items:center; gap:8px; font-weight:700; font-size:16px; color:#fff; }
  .topbar-logo .logo-img{ height:24px; width:auto; }
  .logout-link{ background:rgba(255,255,255,0.92); color:var(--sage-700); border:none; padding:8px 14px; border-radius:999px; font-size:12.5px; font-weight:700; cursor:pointer; font-family:inherit; }
  .logout-link:hover{ background:#fff; }
  .logout-link:focus-visible{ outline:2px solid #fff; outline-offset:2px; }

  .page-wrap{ max-width:900px; margin:32px auto; padding:0 24px 48px; }
  .page-title{ font-size:22px; font-weight:700; color:var(--text-dark); margin:0 0 18px; letter-spacing:-0.01em; }

  .moveout-section{ display:grid; grid-template-columns:1fr 280px; gap:20px; align-items:start; }
  .card{ background:var(--card-bg); border:1px solid var(--border); border-radius:16px; overflow:hidden; }

  /* Farewell */
  .moveout-hero{ display:flex; align-items:center; gap:16px; padding:24px 26px; background:var(--sage-50); }
  .moveout-check{ width:48px; height:48px; border-radius:50%; background:var(--sage-600); color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .moveout-check svg{ width:24px; height:24px; }
  .moveout-hero-text h2{ margin:0 0 4px; font-size:18px; color:var(--text-dark); }
  .moveout-hero-text p{ margin:0; font-size:13px; color:var(--text-mid); line-height:1.55; max-width:440px; }

  .stay-summary{ display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; padding:20px 26px; border-top:1px solid var(--border); }
  .stay-summary-item{ display:flex; flex-direction:column; gap:4px; min-width:0; }
  .stay-summary-item .label{ font-size:11px; color:var(--text-mid); font-weight:700; text-transform:uppercase; letter-spacing:.4px; }
  .stay-summary-item .value{ font-size:15px; color:var(--text-dark); font-weight:700; }
  .stay-summary-item .value.status-closed{ color:var(--sage-600); }

  /* Review prompt: the main thing to do on this page */
  .review-block{ padding:22px 26px 24px; border-top:1px solid var(--border); }
  .review-block h3{ margin:0 0 4px; font-size:17px; color:var(--text-dark); }
  .review-block p{ margin:0 0 14px; font-size:13px; color:var(--text-mid); line-height:1.55; }
  .quick-stars{ display:flex; gap:4px; margin-bottom:14px; }
  .quick-star{ background:none; border:none; padding:4px; cursor:pointer; color:#d9d4c7; border-radius:8px; line-height:0; }
  .quick-star svg{ width:34px; height:34px; display:block; }
  /* Hovering a star lights it and every star before it */
  .quick-stars:hover .quick-star{ color:#f5b301; }
  .quick-stars .quick-star:hover ~ .quick-star{ color:#d9d4c7; }
  .quick-star:focus-visible{ outline:2px solid var(--sage-600); outline-offset:1px; }

  .review-thanks{ display:flex; align-items:center; gap:14px; }
  .review-thanks .stars-static{ color:#f5b301; font-size:0; display:flex; gap:2px; }
  .review-thanks .stars-static svg{ width:20px; height:20px; }
  .review-thanks .stars-static .off{ color:#d9d4c7; }
  .review-thanks strong{ display:block; font-size:15px; color:var(--text-dark); margin-bottom:2px; }
  .review-thanks span{ font-size:13px; color:var(--text-mid); }

  /* Contact */
  .contact-admin-card{ padding:22px 22px 24px; }
  .contact-admin-card h3{ margin:0 0 6px; font-size:16px; color:var(--text-dark); }
  .contact-admin-card p{ margin:0 0 14px; font-size:13px; color:var(--text-mid); line-height:1.6; }
  .contact-admin-details{ margin-bottom:16px; display:flex; flex-direction:column; gap:6px; }
  .contact-admin-details a{ font-size:13px; color:var(--sage-700); font-weight:600; text-decoration:none; overflow-wrap:anywhere; }
  .contact-admin-details a:hover{ text-decoration:underline; }

  .btn-review{ background:var(--sage-600); border:1px solid var(--sage-600); color:#fff; padding:11px 20px; border-radius:10px; font-weight:700; font-size:13.5px; cursor:pointer; font-family:inherit; }
  .btn-review:hover:not(:disabled){ background:var(--sage-700); }
  .btn-review:disabled{ opacity:.6; cursor:not-allowed; }
  .btn-outline{ display:inline-block; text-decoration:none; text-align:center; background:#fff; border:1px solid var(--sage-300); color:var(--sage-700); padding:10px 18px; border-radius:10px; font-weight:700; font-size:13px; cursor:pointer; font-family:inherit; }
  .btn-outline:hover{ background:var(--sage-50); }
  .btn-review:focus-visible, .btn-outline:focus-visible{ outline:2px solid var(--sage-600); outline-offset:2px; }

  /* Review modal */
  body.modal-open{ overflow:hidden; }
  .review-modal-overlay{ display:none; position:fixed; inset:0; background:rgba(31,70,48,0.45); align-items:center; justify-content:center; z-index:100; padding:20px; }
  .review-modal-overlay.open{ display:flex; }
  .review-modal{ background:#fff; border-radius:18px; padding:26px; width:100%; max-width:480px; box-shadow:0 20px 50px rgba(31,70,48,0.25); max-height:calc(100dvh - 40px); overflow-y:auto; }
  .review-modal h2{ margin:0 0 4px; font-size:20px; color:var(--text-dark); }
  .review-modal-sub{ margin:0 0 18px; font-size:13px; color:var(--text-mid); line-height:1.5; }
  .review-modal-error{ display:none; background:#f7d9d7; color:#a8382f; font-size:13px; padding:9px 12px; border-radius:8px; margin-bottom:14px; }
  .review-modal-error.visible{ display:block; }
  .field-label{ display:block; font-size:12px; font-weight:700; color:var(--text-mid); text-transform:uppercase; letter-spacing:.4px; margin-bottom:8px; }
  .star-row{ display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
  .star-picker{ display:flex; gap:2px; }
  .star-btn{ background:none; border:none; line-height:0; color:#d9d4c7; cursor:pointer; padding:5px; border-radius:8px; }
  .star-btn svg{ width:32px; height:32px; display:block; }
  .star-btn.filled{ color:#f5b301; }
  .star-btn:focus-visible{ outline:2px solid var(--sage-600); outline-offset:0; }
  .star-caption{ font-size:14px; font-weight:700; color:var(--text-dark); min-height:20px; }
  .review-modal textarea{ width:100%; min-height:110px; border:1px solid var(--border); border-radius:10px; padding:11px 12px; font-size:14px; font-family:inherit; color:var(--text-dark); resize:vertical; display:block; }
  .review-modal textarea:focus{ outline:none; border-color:var(--sage-600); box-shadow:0 0 0 3px var(--sage-100); }
  .char-count{ font-size:12px; color:var(--text-light); text-align:right; margin:6px 0 18px; }
  .review-modal-actions{ display:flex; justify-content:flex-end; gap:10px; }

  /* Tablet */
  @media (max-width: 820px){
    .moveout-section{ grid-template-columns:1fr; }
    .stay-summary{ grid-template-columns:repeat(2, 1fr); }
  }

  /* Phone: tighter spacing, and the review form becomes a bottom sheet */
  @media (max-width: 640px){
    .topbar{ padding:10px 16px; min-height:56px; }
    .topbar-right{ padding-left:12px; min-width:0; }
    .topbar-username{ max-width:120px; font-size:13px; }
    .page-wrap{ margin:18px auto; padding:0 16px 40px; }
    .page-title{ font-size:19px; margin-bottom:14px; }
    .moveout-hero{ padding:20px 18px; align-items:flex-start; }
    .moveout-check{ width:40px; height:40px; }
    .moveout-check svg{ width:20px; height:20px; }
    .stay-summary{ padding:18px; gap:14px; }
    .review-block{ padding:20px 18px 22px; }
    .quick-stars{ justify-content:space-between; max-width:320px; }
    .quick-star svg{ width:40px; height:40px; }
    .review-block .btn-review{ width:100%; }
    .contact-admin-card .btn-outline{ width:100%; }

    .review-modal-overlay{ align-items:flex-end; padding:0; }
    .review-modal{ max-width:none; border-radius:20px 20px 0 0; padding:22px 18px calc(18px + env(safe-area-inset-bottom)); max-height:92dvh; }
    .review-modal textarea{ font-size:16px; } /* 16px stops iPhones from zooming into the field */
    .star-btn svg{ width:38px; height:38px; }
    .review-modal-actions{ flex-direction:column-reverse; }
    .review-modal-actions button{ width:100%; padding:13px; }
  }
</style>
</head>
<body>

  <div class="topbar">
    <div class="topbar-logo"><img src="{{ asset('images/nestph.png') }}" alt="" class="logo-img"><span>NEST.PH</span></div>
    <div class="topbar-right">
      <span class="topbar-username">{{ $tenant->full_name ?? 'Tenant' }}</span>
      <button type="button" class="logout-link" id="logoutBtn">Log Out</button>
    </div>
  </div>

  <main class="page-wrap">
    <h1 class="page-title">Account Summary</h1>

    <div class="moveout-section">
      <div class="card">
        <div class="moveout-hero">
          <div class="moveout-check" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></div>
          <div class="moveout-hero-text">
            <h2>You've moved out</h2>
            <p>Thank you for being part of our community. We hope you had a comfortable and memorable stay with us.</p>
          </div>
        </div>

        <div class="stay-summary">
          <div class="stay-summary-item">
            <span class="label">Move-in</span>
            <span class="value">{{ $moveOutContract && $moveOutContract->start_date ? \Carbon\Carbon::parse($moveOutContract->start_date)->format('M j, Y') : '—' }}</span>
          </div>
          <div class="stay-summary-item">
            <span class="label">Move-out</span>
            <span class="value">{{ $moveOutContract && $moveOutContract->terminated_at ? \Carbon\Carbon::parse($moveOutContract->terminated_at)->format('M j, Y') : '—' }}</span>
          </div>
          <div class="stay-summary-item">
            <span class="label">Length of Stay</span>
            <span class="value">{{ $lengthOfStay ?? '—' }}</span>
          </div>
          <div class="stay-summary-item">
            <span class="label">Account</span>
            <span class="value status-closed">Closed</span>
          </div>
        </div>

        <div class="review-block" id="moveoutCta">
          @if($tenant->review)
            <div class="review-thanks">
              <div class="stars-static" role="img" aria-label="Rated {{ $tenant->review->rating }} out of 5">
                @for ($i = 1; $i <= 5; $i++)
                  <svg viewBox="0 0 24 24" fill="currentColor" class="{{ $i <= $tenant->review->rating ? '' : 'off' }}"><path d="M12 2.5l2.9 6.06 6.6.7-4.9 4.55 1.28 6.55L12 16.9l-5.88 3.46 1.28-6.55L2.5 9.26l6.6-.7z"/></svg>
                @endfor
              </div>
              <div>
                <strong>Thanks for your review!</strong>
                <span>You rated your stay {{ $tenant->review->rating }} out of 5.</span>
              </div>
            </div>
          @else
            <h3>How was your stay?</h3>
            <p>Your review helps future tenants choose. It takes less than 2 minutes.</p>
            <div class="quick-stars" id="quickStars" aria-label="Pick a rating to start your review">
              @for ($i = 1; $i <= 5; $i++)
                <button type="button" class="quick-star" data-value="{{ $i }}" aria-label="Rate {{ $i }} out of 5">
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.5l2.9 6.06 6.6.7-4.9 4.55 1.28 6.55L12 16.9l-5.88 3.46 1.28-6.55L2.5 9.26l6.6-.7z"/></svg>
                </button>
              @endfor
            </div>
            <button type="button" class="btn-review" id="openReviewModalBtn">Leave a Review</button>
          @endif
        </div>
      </div>

      <div class="card contact-admin-card">
        <h3>Need help?</h3>
        <p>Any concerns after moving out? Feel free to contact us.</p>
        @if($dormContactEmail || $dormContactNumber)
          <div class="contact-admin-details">
            @if($dormContactEmail)<a href="mailto:{{ $dormContactEmail }}">{{ $dormContactEmail }}</a>@endif
            @if($dormContactNumber)<a href="tel:{{ preg_replace('/[^0-9+]/', '', $dormContactNumber) }}">{{ $dormContactNumber }}</a>@endif
          </div>
        @endif
        @if($dormContactEmail)
          <a href="mailto:{{ $dormContactEmail }}" class="btn-outline">Contact Admin</a>
        @endif
      </div>
    </div>
  </main>

  @if(! $tenant->review)
    <div class="review-modal-overlay" id="reviewModalOverlay">
      <div class="review-modal" role="dialog" aria-modal="true" aria-labelledby="reviewModalTitle">
        <h2 id="reviewModalTitle">Leave a Review</h2>
        <p class="review-modal-sub">Share your experience to help future tenants.</p>

        <div class="review-modal-error" id="reviewModalError" role="alert"></div>

        <span class="field-label" id="ratingLabel">Your rating</span>
        <div class="star-row">
          <div class="star-picker" id="starPicker" role="radiogroup" aria-labelledby="ratingLabel">
            @for ($i = 1; $i <= 5; $i++)
              <button type="button" class="star-btn" data-value="{{ $i }}" role="radio" aria-checked="false" aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.5l2.9 6.06 6.6.7-4.9 4.55 1.28 6.55L12 16.9l-5.88 3.46 1.28-6.55L2.5 9.26l6.6-.7z"/></svg>
              </button>
            @endfor
          </div>
          <span class="star-caption" id="starCaption" aria-live="polite"></span>
        </div>

        <label class="field-label" for="reviewComment">Tell us more (optional)</label>
        <textarea id="reviewComment" maxlength="1000"></textarea>
        <div class="char-count"><span id="reviewCharCount">0</span> / 1000</div>

        <div class="review-modal-actions">
          <button type="button" class="btn-outline" id="cancelReviewBtn">Cancel</button>
          <button type="button" class="btn-review" id="submitReviewBtn">Submit Review</button>
        </div>
      </div>
    </div>
  @endif

<script>
(function(){
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  document.getElementById('logoutBtn').addEventListener('click', async () => {
    await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
    window.location.href = '/';
  });
})();
</script>

<script>
(function(){
  const overlay = document.getElementById('reviewModalOverlay');
  const openBtn = document.getElementById('openReviewModalBtn');
  if (!overlay || !openBtn) return; // already reviewed, nothing to wire up

  const cancelBtn = document.getElementById('cancelReviewBtn');
  const submitReviewBtn = document.getElementById('submitReviewBtn');
  const errorEl = document.getElementById('reviewModalError');
  const starButtons = document.querySelectorAll('#starPicker .star-btn');
  const quickStars = document.querySelectorAll('#quickStars .quick-star');
  const captionEl = document.getElementById('starCaption');
  const commentEl = document.getElementById('reviewComment');
  const charCountEl = document.getElementById('reviewCharCount');
  const moveoutCta = document.getElementById('moveoutCta');
  const CAPTIONS = ['', 'Poor', 'Fair', 'Good', 'Very good', 'Excellent'];
  const STAR_PATH = 'M12 2.5l2.9 6.06 6.6.7-4.9 4.55 1.28 6.55L12 16.9l-5.88 3.46 1.28-6.55L2.5 9.26l6.6-.7z';

  let selectedRating = 0;
  let returnFocusTo = null;

  function setRating(value){
    selectedRating = value;
    starButtons.forEach(btn => {
      const filled = Number(btn.dataset.value) <= value;
      btn.classList.toggle('filled', filled);
      btn.setAttribute('aria-checked', Number(btn.dataset.value) === value ? 'true' : 'false');
    });
    captionEl.textContent = CAPTIONS[value] || '';
  }

  function openModal(rating, trigger){
    returnFocusTo = trigger || openBtn;
    errorEl.classList.remove('visible');
    setRating(rating || 0);
    commentEl.value = '';
    charCountEl.textContent = '0';
    overlay.classList.add('open');
    document.body.classList.add('modal-open');
    // Rating already picked from the page? Go straight to the comment box.
    (rating ? commentEl : starButtons[0]).focus();
  }

  function closeModal(){
    overlay.classList.remove('open');
    document.body.classList.remove('modal-open');
    if (returnFocusTo && document.body.contains(returnFocusTo)) returnFocusTo.focus();
  }

  starButtons.forEach(btn => {
    btn.addEventListener('click', () => setRating(Number(btn.dataset.value)));
  });
  quickStars.forEach(btn => {
    btn.addEventListener('click', () => openModal(Number(btn.dataset.value), btn));
  });
  openBtn.addEventListener('click', () => openModal(0, openBtn));
  commentEl.addEventListener('input', () => { charCountEl.textContent = commentEl.value.length; });

  cancelBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });
  document.addEventListener('keydown', (e) => {
    if (!overlay.classList.contains('open')) return;
    if (e.key === 'Escape') closeModal();
    // Keep Tab inside the dialog
    if (e.key === 'Tab') {
      const focusables = overlay.querySelectorAll('button:not(:disabled), textarea');
      const first = focusables[0], last = focusables[focusables.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });

  function renderThanks(rating){
    const wrap = document.createElement('div');
    wrap.className = 'review-thanks';
    const stars = document.createElement('div');
    stars.className = 'stars-static';
    stars.setAttribute('role', 'img');
    stars.setAttribute('aria-label', 'Rated ' + rating + ' out of 5');
    for (let i = 1; i <= 5; i++) {
      const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
      svg.setAttribute('viewBox', '0 0 24 24');
      svg.setAttribute('fill', 'currentColor');
      if (i > rating) svg.setAttribute('class', 'off');
      const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
      path.setAttribute('d', STAR_PATH);
      svg.appendChild(path);
      stars.appendChild(svg);
    }
    const text = document.createElement('div');
    const strong = document.createElement('strong');
    strong.textContent = 'Thanks for your review!';
    const span = document.createElement('span');
    span.textContent = 'You rated your stay ' + rating + ' out of 5.';
    text.append(strong, span);
    wrap.append(stars, text);
    moveoutCta.replaceChildren(wrap);
  }

  submitReviewBtn.addEventListener('click', async function(){
    errorEl.classList.remove('visible');

    if (selectedRating < 1) {
      errorEl.textContent = 'Pick a star rating first.';
      errorEl.classList.add('visible');
      starButtons[0].focus();
      return;
    }

    this.disabled = true;
    this.textContent = 'Submitting...';
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]').content;
      const res = await fetch('{{ route('reviews.store') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ rating: selectedRating, comment: commentEl.value.trim() }),
      });
      const body = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(body.message || 'Something went wrong. Please try again.');

      returnFocusTo = null;
      closeModal();
      if (moveoutCta) {
        renderThanks(selectedRating);
        moveoutCta.setAttribute('tabindex', '-1');
        moveoutCta.focus();
      }
    } catch (e) {
      errorEl.textContent = e.message;
      errorEl.classList.add('visible');
    }
    this.disabled = false;
    this.textContent = 'Submit Review';
  });
})();
</script>

</body>
</html>
