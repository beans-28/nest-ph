<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Account Closed</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-darker:#345a3e;
    --green-accent:#3f6b4a; --green-btn:#3f6b4a; --green-btn-hover:#2f5439;
    --bg-page:#f4f6f4; --card-bg:#ffffff;
    --text-dark:#1f2a22; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e5e9e4;
    --font-body: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  }
  *{box-sizing:border-box;}
  html,body{ margin:0; padding:0; font-family:var(--font-body); background:var(--bg-page); color:var(--text-dark); }

  .topbar{
    display:flex; align-items:center; gap:16px;
    background:linear-gradient(90deg, var(--green-darker), var(--green-dark));
    padding:16px 32px;
  }
  .topbar-logo{ display:flex; align-items:center; gap:8px; font-weight:700; font-size:16px; color:#fff; }
  .topbar-logo .logo-mark{ width:16px; height:16px; border:2px solid #fff; display:inline-block; position:relative; }
  .topbar-logo .logo-mark::before, .topbar-logo .logo-mark::after{ content:''; position:absolute; background:#fff; width:2px; height:12px; top:0; left:5px; }
  .topbar-right{ margin-left:auto; display:flex; align-items:center; gap:14px; }
  .topbar-username{ color:#fff; font-size:13.5px; font-weight:600; }
  .logout-link{ background:rgba(255,255,255,0.14); color:#fff; border:1px solid rgba(255,255,255,0.3); padding:8px 16px; border-radius:7px; font-size:12.5px; font-weight:600; cursor:pointer; }
  .logout-link:hover{ background:rgba(255,255,255,0.22); }

  .page-wrap{ max-width:960px; margin:40px auto; padding:0 24px 48px; }
  .page-title{ font-size:19px; font-weight:700; color:var(--green-accent); margin:0 0 22px; }

  .moveout-section{ display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap; }
  .moveout-card{ flex:1 1 560px; background:var(--card-bg); border:1px solid var(--border); border-radius:14px; overflow:hidden; }
  .moveout-hero{ display:flex; align-items:center; gap:18px; padding:26px 28px; background:#f5f8f4; }
  .moveout-check{ width:52px; height:52px; border-radius:50%; background:var(--green-dark); color:#fff; display:flex; align-items:center; justify-content:center; font-size:26px; font-weight:700; flex-shrink:0; }
  .moveout-hero-text h2{ margin:0 0 6px; font-size:17px; color:var(--text-dark); }
  .moveout-hero-text p{ margin:0; font-size:12.5px; color:var(--text-mid); line-height:1.55; max-width:420px; }

  .stay-summary{ display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; padding:22px 28px; border-top:1px solid var(--border); border-bottom:1px solid var(--border); }
  .stay-summary-item{ display:flex; flex-direction:column; gap:4px; }
  .stay-summary-item .label{ font-size:11.5px; color:var(--text-mid); font-weight:600; }
  .stay-summary-item .value{ font-size:14.5px; color:var(--text-dark); font-weight:700; }
  .stay-summary-item .value.status-closed{ color:var(--green-accent); }

  .moveout-cta{ padding:20px 28px; }
  .cta-prompt{ display:flex; align-items:center; gap:14px; flex-wrap:wrap; justify-content:flex-end; }
  .cta-hint{ font-size:11.5px; color:var(--text-light); }
  .cta-thanks{ text-align:right; }
  .cta-thanks strong{ display:block; color:var(--green-accent); font-size:14px; margin-bottom:2px; }
  .cta-thanks p{ margin:0; font-size:12.5px; color:var(--text-mid); }

  .contact-admin-card{ flex:0 1 300px; background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:24px 26px; }
  .contact-admin-card h3{ margin:0 0 8px; font-size:16px; color:var(--text-dark); }
  .contact-admin-card p{ margin:0 0 16px; font-size:12.5px; color:var(--text-mid); line-height:1.6; }
  .contact-admin-details{ margin-bottom:16px; }
  .contact-admin-details p{ margin:0 0 4px; font-size:12.5px; color:var(--text-dark); font-weight:600; }
  .contact-btn{ display:inline-block; text-decoration:none; text-align:center; }

  .btn-review{ background:var(--green-dark); border:1px solid var(--green-darker); color:#fff; padding:10px 20px; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; white-space:nowrap; }
  .btn-review:hover{ background:var(--green-btn-hover); }
  .btn-outline{ background:#fff; border:1px solid var(--green-darker); color:var(--green-accent); padding:8px 18px; border-radius:6px; font-weight:700; font-size:12.5px; cursor:pointer; }

  @media (max-width: 720px){
    .stay-summary{ grid-template-columns:repeat(2, 1fr); }
  }

  .review-modal-overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); align-items:center; justify-content:center; z-index:100; }
  .review-modal-overlay.open{ display:flex; }
  .review-modal{ background:#fff; border-radius:10px; padding:28px; width:100%; max-width:480px; box-shadow:0 12px 32px rgba(0,0,0,0.2); }
  .review-modal h2{ margin:0 0 4px; font-size:20px; color:var(--text-dark); }
  .review-modal-sub{ margin:0 0 18px; font-size:13px; color:var(--text-mid); }
  .review-modal-error{ display:none; background:#f7d9d7; color:#a3372e; font-size:12.5px; padding:8px 12px; border-radius:6px; margin-bottom:14px; }
  .review-modal-error.visible{ display:block; }
  .star-picker{ display:flex; gap:6px; margin-bottom:18px; }
  .star-btn{ background:none; border:none; font-size:32px; line-height:1; color:#d8d8d8; cursor:pointer; padding:0; }
  .star-btn.filled{ color:#f5b301; }
  .review-modal textarea{ width:100%; min-height:90px; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; font-family:inherit; resize:vertical; margin-bottom:18px; }
  .review-modal-actions{ display:flex; justify-content:flex-end; gap:10px; }
</style>
</head>
<body>

  <div class="topbar">
    <div class="topbar-logo"><span class="logo-mark"></span><span>NEST.PH</span></div>
    <div class="topbar-right">
      <span class="topbar-username">{{ $tenant->full_name ?? 'Tenant' }}</span>
      <button class="logout-link" id="logoutBtn">Log Out</button>
    </div>
  </div>

  <div class="page-wrap">
    <h1 class="page-title">Account Summary</h1>

    <div class="moveout-section">
      <div class="moveout-card">
        <div class="moveout-hero">
          <div class="moveout-check">&#10003;</div>
          <div class="moveout-hero-text">
            <h2>You've been moved out</h2>
            <p>Thank you for being part of our community. We hope you had a comfortable and memorable stay with us.</p>
          </div>
        </div>

        <div class="stay-summary">
          <div class="stay-summary-item">
            <span class="label">Move-in Date</span>
            <span class="value">{{ $moveOutContract && $moveOutContract->start_date ? \Carbon\Carbon::parse($moveOutContract->start_date)->format('M j, Y') : '—' }}</span>
          </div>
          <div class="stay-summary-item">
            <span class="label">Move-out Date</span>
            <span class="value">{{ $moveOutContract && $moveOutContract->terminated_at ? \Carbon\Carbon::parse($moveOutContract->terminated_at)->format('M j, Y') : '—' }}</span>
          </div>
          <div class="stay-summary-item">
            <span class="label">Length of Stay</span>
            <span class="value">{{ $lengthOfStay ?? '—' }}</span>
          </div>
          <div class="stay-summary-item">
            <span class="label">Account Status</span>
            <span class="value status-closed">Closed</span>
          </div>
        </div>

        <div class="moveout-cta" id="moveoutCta">
          @if($tenant->review)
            <div class="cta-thanks">
              <strong>Thanks for your review!</strong>
              <p>You rated your stay {{ $tenant->review->rating }} / 5 stars.</p>
            </div>
          @else
            <div class="cta-prompt">
              <span class="cta-hint">Takes less than 2 minutes</span>
              <button class="btn-review" id="openReviewModalBtn">Leave a Review</button>
            </div>
          @endif
        </div>
      </div>

      <div class="contact-admin-card">
        <h3>Need help?</h3>
        <p>If you have any concerns after moving out, feel free to contact us.</p>
        @if($dormContactEmail || $dormContactNumber)
          <div class="contact-admin-details">
            @if($dormContactEmail)<p>{{ $dormContactEmail }}</p>@endif
            @if($dormContactNumber)<p>{{ $dormContactNumber }}</p>@endif
          </div>
        @endif
        @if($dormContactEmail)
          <a href="mailto:{{ $dormContactEmail }}" class="btn-outline contact-btn">Contact Admin</a>
        @endif
      </div>
    </div>
  </div>

  @if(! $tenant->review)
    <div class="review-modal-overlay" id="reviewModalOverlay">
      <div class="review-modal">
        <h2>Leave a Review</h2>
        <p class="review-modal-sub">Share your experience to help future tenants.</p>

        <div class="review-modal-error" id="reviewModalError"></div>

        <div class="star-picker" id="starPicker">
          <button type="button" class="star-btn" data-value="1">&#9733;</button>
          <button type="button" class="star-btn" data-value="2">&#9733;</button>
          <button type="button" class="star-btn" data-value="3">&#9733;</button>
          <button type="button" class="star-btn" data-value="4">&#9733;</button>
          <button type="button" class="star-btn" data-value="5">&#9733;</button>
        </div>

        <textarea id="reviewComment" maxlength="1000" placeholder="Share details about your stay, the things you liked, and areas for improvement..."></textarea>

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
  if (!overlay || !openBtn) return; // already reviewed — nothing to wire up

  const cancelBtn = document.getElementById('cancelReviewBtn');
  const submitReviewBtn = document.getElementById('submitReviewBtn');
  const errorEl = document.getElementById('reviewModalError');
  const starButtons = document.querySelectorAll('#starPicker .star-btn');
  const commentEl = document.getElementById('reviewComment');
  const moveoutCta = document.getElementById('moveoutCta');

  let selectedRating = 0;

  function setRating(value){
    selectedRating = value;
    starButtons.forEach(btn => {
      btn.classList.toggle('filled', Number(btn.dataset.value) <= value);
    });
  }

  starButtons.forEach(btn => {
    btn.addEventListener('click', () => setRating(Number(btn.dataset.value)));
  });

  openBtn.addEventListener('click', () => {
    errorEl.classList.remove('visible');
    setRating(0);
    commentEl.value = '';
    overlay.classList.add('open');
  });

  cancelBtn.addEventListener('click', () => overlay.classList.remove('open'));

  submitReviewBtn.addEventListener('click', async function(){
    errorEl.classList.remove('visible');

    if (selectedRating < 1) {
      errorEl.textContent = 'Please select a star rating before submitting.';
      errorEl.classList.add('visible');
      return;
    }

    this.disabled = true;
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]').content;
      const res = await fetch('{{ route('reviews.store') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ rating: selectedRating, comment: commentEl.value.trim() }),
      });
      const body = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(body.message || 'Something went wrong. Please try again.');

      overlay.classList.remove('open');
      if (moveoutCta) {
        moveoutCta.innerHTML = `<div class="cta-thanks"><strong>Thanks for your review!</strong><p>You rated your stay ${selectedRating} / 5 stars.</p></div>`;
      }
    } catch (e) {
      errorEl.textContent = e.message;
      errorEl.classList.add('visible');
    }
    this.disabled = false;
  });
})();
</script>

</body>
</html>