<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    {{-- Page-switch fade opt-in: must be inline and first in <head> or Chrome decides before it sees it (see typography.css). --}}
    <style>@view-transition{navigation:auto}</style><link rel="stylesheet" href="/css/motion.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>My Profile · {{ $brandDormName }}</title>
<link rel="icon" href="{{ $brandFaviconUrl }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}">
    <script src="{{ asset('js/smooth-nav.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('css/tenant.css') }}">
<style>
  /* Shared color variables, page reset, sidebar, topbar, and focus styles
     now live in public/css/tenant.css (linked above). This page adds one
     extra variable (--green-mid) that tenant.css doesn't define, plus its
     own profile/change-password content styling below. */
  :root{
  }

  .content{ padding:26px 34px 48px 34px; flex:1; max-width:1180px; }
  .page-head{ display:flex; align-items:flex-start; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:30px; height:30px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; margin-top:2px; }
  .page-head-text p{ font-size:12.5px; color:var(--text-mid); margin:2px 0 0 0; }

  .tabs-row{ display:flex; gap:8px; margin-bottom:20px; }
  .tab-pill{ padding:9px 18px; border-radius:20px; font-size:12.5px; font-weight:600; cursor:pointer; border:1px solid var(--border); background:#fff; color:var(--text-mid); }
  .tab-pill.active{ background:var(--green-dark); color:#fff; border-color:var(--green-dark); }

  .tab-panel{ display:none; }
  .tab-panel.active{ display:block; }

  .settings-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:28px 30px; max-width:640px; }
  .settings-card h2{ font-size:16px; font-weight:700; margin:0 0 6px 0; color:var(--text-dark); }
  .settings-card .intro{ font-size:12.5px; color:var(--text-mid); line-height:1.6; margin-bottom:24px; }

  /* ===== Profile tab ===== */
  .request-edit-banner{
    display:flex; align-items:center; gap:14px; justify-content:space-between;
    background:var(--sage-50); border:1px solid var(--sage-200); border-radius:12px;
    padding:14px 18px; margin-bottom:22px; max-width:640px;
  }
  .request-edit-banner p{ margin:0; font-size:12.5px; color:var(--text-dark); line-height:1.5; }
  .request-edit-banner p strong{ color:var(--green-accent); }
  .request-edit-btn{
    flex-shrink:0; background:var(--green-btn); color:#fff; border:none; border-radius:8px;
    padding:9px 16px; font-size:12.5px; font-weight:700; cursor:pointer; white-space:nowrap;
    text-decoration:none; display:inline-block; transition:background 0.15s ease;
  }
  .request-edit-btn:hover{ background:var(--green-btn-hover); }

  .profile-section{ margin-bottom:26px; }
  .profile-section:last-child{ margin-bottom:0; }
  .profile-section-title{
    font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em;
    color:var(--green-accent); margin:0 0 14px 0; padding-bottom:8px; border-bottom:1px solid var(--border);
  }
  .info-grid{ display:grid; grid-template-columns:repeat(2, 1fr); gap:16px 28px; }
  .info-item label{ display:block; font-size:11px; text-transform:uppercase; letter-spacing:0.03em; color:var(--text-light); font-weight:600; margin-bottom:4px; }
  .info-item .value{ font-size:14px; color:var(--text-dark); font-weight:500; }
  .info-item .value.muted{ color:var(--text-light); font-weight:400; font-style:italic; }

  .lease-actions{ margin-top:16px; display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
  .btn-outline-sm{
    display:inline-flex; align-items:center; gap:6px; border:1px solid var(--green-dark); color:var(--green-dark);
    background:#fff; border-radius:8px; padding:8px 14px; font-size:12.5px; font-weight:700;
    text-decoration:none; cursor:pointer; transition:background 0.15s ease, color 0.15s ease;
  }
  .btn-outline-sm:hover{ background:var(--green-dark); color:#fff; }
  .muted-note{ font-size:12px; color:var(--text-light); margin:0; line-height:1.5; }
  .ok-note, .review-note{ flex-basis:100%; font-size:12px; border-radius:8px; padding:10px 12px; margin:0; line-height:1.5; }
  .ok-note{ color:#1f5a2c; background:#d9f2dd; border:1px solid #a9dcb1; }
  .review-note{ color:#7a5a12; background:#f6ecd6; border:1px solid #ead7a8; }
  .notarize-box{ flex-basis:100%; display:flex; flex-direction:column; gap:10px; }
  .notarize-form label{ display:block; font-size:12px; font-weight:700; color:var(--text-dark); margin-bottom:6px; }
  .notarize-form label span{ font-weight:400; color:var(--text-light); }
  .notarize-row{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
  .notarize-row input[type=file]{ flex:1 1 220px; min-width:0; font-size:12.5px; border:1px solid #d5dbd5; border-radius:8px; padding:7px 10px; background:#fff; }
  .notarize-row .btn-outline-sm{ min-height:40px; }
  .btn-outline-sm:disabled{ opacity:.55; cursor:not-allowed; }
  .notarize-msg{ font-size:12px; margin:6px 0 0; min-height:1em; }
  .notarize-msg.error{ color:#a8403a; }
  .btn-outline-sm.solid{ background:var(--green-dark); color:#fff; }
  .btn-outline-sm.solid:hover:not(:disabled){ background:var(--green-btn-hover, #45604a); }
  .btn-outline-sm:focus-visible, .notarize-row input[type=file]:focus-visible{ outline:2px solid var(--green-dark); outline-offset:2px; }
  .required-note.is-rejected{ color:#8f3530; background:#fbeceb; border-color:#f2cfcc; }
  .required-note{ flex-basis:100%; font-size:12px; color:#7a5a12; background:#f6ecd6; border:1px solid #ead7a8; border-radius:8px; padding:10px 12px; margin:0; line-height:1.5; }

  /* ===== Change Password tab ===== */
  .pw-form-group{ margin-bottom:20px; }
  .pw-form-group label{ display:block; font-size:12.5px; font-weight:600; color:var(--green-accent); margin-bottom:7px; }
  .pw-input-wrap{ position:relative; }
  .pw-form-group input{ width:100%; border:none; border-bottom:1px solid var(--border); padding:8px 32px 8px 2px; font-size:14px; background:transparent; color:var(--text-dark); outline:none; font-family:inherit; }
  .pw-form-group input:focus{ border-bottom-color:var(--green-dark); }
  .pw-toggle{ position:absolute; right:2px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--text-light); padding:4px; display:flex; }
  .pw-toggle:hover{ color:var(--green-dark); }
  .pw-toggle svg{ width:16px; height:16px; }

  .field-error{ color:#c0463d; font-size:12px; margin-top:6px; display:none; }
  .field-error.visible{ display:block; }
  .pw-hint{ font-size:11px; color:var(--text-light); margin-top:-10px; margin-bottom:20px; }

  .form-banner{ display:none; align-items:flex-start; gap:8px; padding:12px 14px; border-radius:8px; font-size:12.5px; margin-bottom:20px; line-height:1.5; }
  .form-banner.visible{ display:flex; }
  .form-banner.success{ background:var(--sage-100); color:var(--sage-600); }
  .form-banner.error{ background:#f7d9d7; color:#c0463d; }

  .pw-submit-btn{ background:var(--green-btn); color:#fff; border:none; border-radius:8px; padding:13px 0; width:100%; font-weight:700; font-size:13.5px; letter-spacing:0.03em; cursor:pointer; margin-top:4px; transition:background 0.15s ease; }
  .pw-submit-btn:hover:not(:disabled){ background:var(--green-btn-hover); }
  .pw-submit-btn:disabled{ opacity:0.7; cursor:not-allowed; }
  .pw-submit-btn .spinner{ display:none; width:13px; height:13px; border:2px solid rgba(255,255,255,0.35); border-top:2px solid #fff; border-radius:50%; animation:pwspin 0.8s linear infinite; margin-right:8px; vertical-align:-2px; }
  .pw-submit-btn.loading .spinner{ display:inline-block; }
  @keyframes pwspin{ to{ transform:rotate(360deg); } }

  /* ===== Submitted ID (side panel) ===== */
  .profile-layout{ display:flex; align-items:flex-start; gap:24px; }
  .profile-photo{ display:flex; align-items:center; gap:16px; margin-bottom:22px; }
  .profile-photo .photo{ width:96px; height:96px; border-radius:8px; object-fit:cover; flex-shrink:0; border:1px solid var(--border); background:color-mix(in srgb, var(--green-accent, #2f6f3c) 12%, #fff); display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:700; color:var(--green-darker, #1f5a2c); }
  .photo-edit{ position:relative; padding:0; border:0; background:none; border-radius:10px; cursor:pointer; flex-shrink:0; }
  .photo-edit .photo[hidden]{ display:none; }
  .photo-edit:focus-visible{ outline:2px solid var(--green-dark); outline-offset:3px; }
  .photo-edit-badge{
    position:absolute; right:-6px; bottom:-6px; width:32px; height:32px; border-radius:50%;
    display:grid; place-items:center; background:var(--green-btn); color:#fff; border:3px solid #fff;
    box-shadow:0 2px 6px rgba(0,0,0,0.2); transition:transform .16s cubic-bezier(.32,.72,0,1);
  }
  .photo-edit-badge svg{ width:15px; height:15px; }
  .photo-edit:hover .photo-edit-badge{ transform:scale(1.08); }
  .photo-edit:active .photo-edit-badge{ transform:scale(0.95); }
  .link-btn{ background:none; border:0; padding:0; font:inherit; color:var(--green-accent); font-weight:700; text-decoration:underline; text-underline-offset:3px; cursor:pointer; }

  /* Crop window */
  .crop-dialog{ border:0; border-radius:18px; padding:0; width:min(420px, calc(100vw - 24px)); box-shadow:0 24px 60px rgba(0,0,0,0.35); }
  .crop-dialog::backdrop{ background:rgba(20,18,16,0.55); }
  .crop-inner{ padding:20px 22px 22px; }
  .crop-inner h2{ font-size:17px; font-weight:700; margin:0 0 4px; color:var(--text-dark); }
  .crop-inner p{ font-size:12.5px; color:var(--text-mid); margin:0 0 16px; line-height:1.5; }
  .crop-stage{
    position:relative; width:100%; aspect-ratio:1; border-radius:12px; overflow:hidden; background:#1c1a18;
    touch-action:none; cursor:grab; user-select:none;
  }
  .crop-stage:active{ cursor:grabbing; }
  .crop-stage img{ position:absolute; top:0; left:0; max-width:none; transform-origin:0 0; pointer-events:none; }
  .crop-stage::after{ content:''; position:absolute; inset:0; box-shadow:inset 0 0 0 2px rgba(255,255,255,0.85); border-radius:12px; pointer-events:none; }
  .crop-zoom{ display:flex; align-items:center; gap:10px; margin-top:14px; }
  .crop-zoom svg{ width:16px; height:16px; color:var(--text-mid); flex-shrink:0; }
  .crop-zoom input{ flex:1; accent-color:var(--green-btn); }
  .crop-actions{ display:flex; justify-content:flex-end; gap:10px; margin-top:18px; }
  .crop-actions button{ border-radius:8px; padding:10px 18px; font:inherit; font-size:13.5px; font-weight:700; cursor:pointer; }
  .crop-cancel{ background:#fff; border:1px solid var(--border); color:var(--text-dark); }
  .crop-save{ background:var(--green-btn); border:1px solid var(--green-btn); color:#fff; }
  .crop-save:disabled{ opacity:.6; cursor:wait; }
  .crop-error{ color:#a8403a; font-size:12.5px; margin:10px 0 0; min-height:1em; }

  .profile-photo .name{ font-size:16px; font-weight:700; }
  .profile-photo .hint{ font-size:12px; color:var(--text-light, #6b7479); margin-top:4px; line-height:1.5; }
  .profile-layout > .settings-card{ flex:1 1 640px; }
  .id-card{
    flex:0 0 320px; background:var(--card-bg); border:1px solid var(--border); border-radius:14px;
    padding:22px 22px 18px; position:sticky; top:20px;
  }
  .id-card .profile-section-title{ margin-bottom:16px; }
  .id-frame{
    display:block; position:relative; border-radius:10px; overflow:hidden; background:var(--sage-50);
    border:1px solid var(--sage-200); aspect-ratio:1.586 / 1; box-shadow:0 6px 18px rgba(0,0,0,0.08);
    transition:transform 0.2s ease, box-shadow 0.2s ease;
  }
  .id-frame:hover{ transform:translateY(-2px); box-shadow:0 10px 24px rgba(0,0,0,0.14); }
  .id-frame img{ width:100%; height:100%; object-fit:cover; display:block; }
  .id-frame .id-zoom{
    position:absolute; right:10px; bottom:10px; background:rgba(255,255,255,0.92); color:var(--green-dark);
    font-size:11px; font-weight:700; padding:5px 10px; border-radius:20px; opacity:0; transition:opacity 0.2s ease;
  }
  .id-frame:hover .id-zoom{ opacity:1; }
  .id-placeholder{
    height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px;
    color:var(--green-accent); font-size:12.5px; font-weight:600; text-align:center; padding:16px;
  }
  .id-placeholder svg{ width:34px; height:34px; }
  .id-caption{ font-size:11.5px; color:var(--text-light); margin:12px 0 0 0; line-height:1.5; }
  @media (max-width: 1024px){
    .profile-layout{ flex-direction:column; }
    .profile-layout > .settings-card{ flex:none; width:100%; }
    .id-card{ position:static; flex:none; width:100%; max-width:640px; box-sizing:border-box; }
  }

  @media (max-width: 640px){
    .info-grid{ grid-template-columns:1fr; }
    .request-edit-banner{ flex-direction:column; align-items:flex-start; }
  }
</style>
</head>
<body>
<div class="app">

  @include('partials.tenant-sidebar')

  <div class="main">
    <div class="topbar">
      <div class="hamburger-icon" id="hamburgerBtn" tabindex="0" aria-label="Toggle sidebar"><svg width="20" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg></div>
      <div class="topbar-right">
        <span class="topbar-username">{{ $tenant->full_name ?? 'Tenant' }}</span>
        @include('partials.tenant-avatar')
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}" tabindex="0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <div class="page-head-text">
          <h1>My Profile</h1>
          <p>{{ $tenant->full_name ?? 'Tenant' }}@if($contract?->bed?->room?->room_no) &middot; Room {{ $contract->bed->room->room_no }}@endif</p>
        </div>
      </div>

      <div class="tabs-row" role="tablist" aria-label="Account settings">
        <div class="tab-pill active" role="tab" aria-selected="true" aria-controls="profilePanel" id="profileTab" data-panel="profilePanel" tabindex="0">Profile</div>
        <div class="tab-pill" role="tab" aria-selected="false" aria-controls="passwordPanel" id="passwordTab" data-panel="passwordPanel" tabindex="0">Change Password</div>
      </div>

      {{-- ===== PROFILE TAB ===== --}}
      <div class="tab-panel active" id="profilePanel" role="tabpanel" aria-labelledby="profileTab">

        <div class="request-edit-banner">
          <p>You can change your picture yourself. The rest is view-only. <strong>Spotted something wrong, or need it updated?</strong> Submit a ticket and an admin will make the change for you.</p>
          <a href="{{ route('tenant.tickets') }}" class="request-edit-btn">Submit a Ticket</a>
        </div>

        @php
          $idDocPath = $tenant->id_document_path ?? null;
          $idDocUrl = $idDocPath ? \Illuminate\Support\Facades\Storage::disk('public')->url($idDocPath) : null;
          $idDocIsPdf = $idDocPath && str_ends_with(strtolower($idDocPath), '.pdf');
        @endphp

        <div class="profile-layout">
        <div class="settings-card">

          <div class="profile-photo">
            {{-- Click the picture to choose and crop a new one --}}
            <button type="button" class="photo-edit" id="photoEditBtn" aria-label="Change your profile picture">
              <img class="photo" id="profilePhotoImg" src="{{ $tenant?->id_photo_url }}" alt="Your profile picture" @if(!$tenant?->id_photo_url) hidden @endif>
              <span class="photo" id="profilePhotoInitials" aria-hidden="true" @if($tenant?->id_photo_url) hidden @endif>{{ $tenant?->initials ?? '?' }}</span>
              <span class="photo-edit-badge" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/></svg></span>
            </button>
            <input type="file" id="photoInput" accept="image/jpeg,image/png,image/webp" hidden>
            <div>
              <div class="name">{{ $tenant->full_name ?? 'Tenant' }}</div>
              <div class="hint" id="photoHint">
                @if($tenant?->id_photo_url)
                  This is your 2x2 ID picture. <button type="button" class="link-btn" id="photoChangeLink">Change picture</button>
                @else
                  No picture yet. <button type="button" class="link-btn" id="photoChangeLink">Add your 2x2 ID picture</button>
                @endif
              </div>
            </div>
          </div>

          <div class="profile-section">
            <h3 class="profile-section-title">Personal Information</h3>
            <div class="info-grid">
              <div class="info-item">
                <label>Full Name</label>
                <div class="value">{{ $tenant->full_name ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Email</label>
                <div class="value">{{ $tenant->email ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Contact Number</label>
                <div class="value">{{ $tenant->contact_number ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Date of Birth</label>
                <div class="value">{{ $tenant->date_of_birth ? \Carbon\Carbon::parse($tenant->date_of_birth)->format('M j, Y') : '—' }}</div>
              </div>
              <div class="info-item">
                <label>Home Address</label>
                <div class="value">{{ $tenant->home_address ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Tenant Type</label>
                <div class="value">{{ $tenant->tenant_type ? ucwords(str_replace('_', ' ', $tenant->tenant_type)) : '—' }}</div>
              </div>
            </div>
          </div>

          <div class="profile-section">
            <h3 class="profile-section-title">Emergency Contact</h3>
            <div class="info-grid">
              <div class="info-item">
                <label>Name</label>
                <div class="value">{{ $tenant->emergency_contact_name ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Contact Number</label>
                <div class="value">{{ $tenant->emergency_contact_number ?? '—' }}</div>
              </div>
            </div>
          </div>

          <div class="profile-section">
            <h3 class="profile-section-title">Room &amp; Lease</h3>
            <div class="info-grid">
              <div class="info-item">
                <label>Room</label>
                <div class="value">{{ $contract?->bed?->room?->room_no ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Bed</label>
                <div class="value">{{ $contract?->bed?->bed_label ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Lease Start</label>
                <div class="value">{{ $contract?->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('M j, Y') : '—' }}</div>
              </div>
              <div class="info-item">
                <label>Lease End</label>
                <div class="value">{{ $contract?->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('M j, Y') : '—' }}</div>
              </div>
              <div class="info-item">
                <label>Monthly Rate</label>
                <div class="value">{{ $contract ? '₱' . number_format($contract->monthly_rate, 2) : '—' }}</div>
              </div>
            </div>

            <div class="lease-actions">
              @if($contractDocumentUrl)
                <a href="{{ $contractDocumentUrl }}" data-lightbox="my-contract" target="_blank" rel="noopener" class="btn-outline-sm">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                  View Lease Contract
                </a>
              @else
                <p class="muted-note">Your signed lease contract hasn't been uploaded to your account yet. If you believe this is a mistake, submit a ticket to ask the admin about it.</p>
              @endif

              @php($nStatus = $contract?->notarized_status ?? 'none')
              @if($contract && $nStatus !== 'none' && $contract->notarized_document_path)
                <a href="{{ route('tenant.notarized-contract') }}" target="_blank" rel="noopener" class="btn-outline-sm">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                  {{ $nStatus === 'approved' ? 'View Notarized Contract' : 'View My Uploaded Copy' }}
                </a>
              @endif

              @if($notarizationRequired && $contract)
                @if($nStatus === 'approved')
                  <p class="ok-note" role="status"><strong>Notarized copy approved.</strong> Nothing else to do.</p>
                @elseif($nStatus === 'submitted')
                  <p class="review-note" role="status"><strong>Notarized copy submitted.</strong> The admin is reviewing it. You'll see the result here.</p>
                @else
                  <div class="notarize-box" id="notarizeBox">
                    @if($nStatus === 'rejected')
                      <p class="required-note is-rejected" role="status"><strong>Your notarized copy was sent back.</strong><br>Admin's reason: &ldquo;{{ $contract->notarized_rejection_reason }}&rdquo;<br>Please upload a new, clear copy below.</p>
                    @else
                      <p class="required-note" role="status"><strong>Notarized copy needed.</strong> This dorm requires your lease contract to be notarized. {{ $contractDocumentUrl ? 'Print your signed contract using View Lease Contract above' : 'Get a printed copy of your signed contract from the admin' }}, have it notarized, then upload a clear scan or photo here.</p>
                    @endif
                    <form id="notarizedForm" class="notarize-form" enctype="multipart/form-data">
                      <label for="notarizedFile">Notarized contract <span>(PDF, JPG or PNG, up to 10 MB)</span></label>
                      <div class="notarize-row">
                        <input type="file" id="notarizedFile" name="notarized_document" accept=".pdf,.jpg,.jpeg,.png" aria-describedby="notarizedMsg">
                        <button type="submit" class="btn-outline-sm solid" id="notarizedSubmit">Submit for Review</button>
                      </div>
                      <p class="notarize-msg" id="notarizedMsg" role="status" aria-live="polite"></p>
                    </form>
                  </div>
                @endif
              @endif
            </div>
          </div>

        </div>

        <aside class="id-card" aria-label="Submitted ID">
          <h3 class="profile-section-title">Submitted ID</h3>
          @if($idDocUrl)
            <a href="{{ $idDocUrl }}" data-lightbox="my-id" target="_blank" rel="noopener" class="id-frame" title="Open full size">
              @if($idDocIsPdf)
                <div class="id-placeholder">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                  Your ID was uploaded as a PDF<br>Tap to view
                </div>
              @else
                <img src="{{ $idDocUrl }}" alt="Your submitted valid ID">
                <span class="id-zoom">View full size</span>
              @endif
            </a>
            <p class="id-caption">This is the ID you submitted when you moved in. Only you and the admin can see it.</p>
          @else
            <div class="id-frame" style="pointer-events:none;">
              <div class="id-placeholder">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="8" cy="12" r="2.5"/><path d="M13 10h5M13 14h4"/></svg>
                No ID on file yet
              </div>
            </div>
            <p class="id-caption">If you already submitted one, send a ticket and the admin will attach it to your account.</p>
          @endif
        </aside>
        </div>
      </div>

      {{-- ===== CHANGE PASSWORD TAB ===== --}}
      <div class="tab-panel" id="passwordPanel" role="tabpanel" aria-labelledby="passwordTab">
        <div class="settings-card">
          <h2>Change Password</h2>
          <p class="intro">Update the password you use to log in. Choose something you don't use anywhere else.</p>

          <div class="form-banner" id="formBanner" role="status" aria-live="polite"></div>

          <form id="changePasswordForm" autocomplete="off">
            <div class="pw-form-group">
              <label for="currentPassword">Current Password</label>
              <div class="pw-input-wrap">
                <input id="currentPassword" type="password" placeholder="Enter current password" autocomplete="current-password" required aria-describedby="currentPasswordError">
                <button type="button" class="pw-toggle" data-target="currentPassword" aria-label="Show password"></button>
              </div>
              <div class="field-error" id="currentPasswordError" role="alert"></div>
            </div>

            <div class="pw-form-group">
              <label for="newPassword">New Password</label>
              <div class="pw-input-wrap">
                <input id="newPassword" type="password" placeholder="Enter new password" autocomplete="new-password" minlength="8" required aria-describedby="newPasswordError newPasswordRules">
                <button type="button" class="pw-toggle" data-target="newPassword" aria-label="Show password"></button>
              </div>
              <div class="field-error" id="newPasswordError" role="alert"></div>
            </div>
            @include('partials.password-rules', ['for' => 'newPassword'])

            <div class="pw-form-group">
              <label for="confirmPassword">Confirm New Password</label>
              <div class="pw-input-wrap">
                <input id="confirmPassword" type="password" placeholder="Re-enter new password" autocomplete="new-password" minlength="8" required aria-describedby="confirmPasswordError">
                <button type="button" class="pw-toggle" data-target="confirmPassword" aria-label="Show password"></button>
              </div>
              <div class="field-error" id="confirmPasswordError" role="alert"></div>
            </div>

            <button type="submit" class="pw-submit-btn" id="submitBtn">
              <span class="spinner"></span>
              <span class="btn-text">Update Password</span>
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  (function(){
    const form = document.getElementById('notarizedForm');
    if(!form) return;
    form.addEventListener('submit', async function(e){
      e.preventDefault();
      const input = document.getElementById('notarizedFile');
      const btn = document.getElementById('notarizedSubmit');
      const msg = document.getElementById('notarizedMsg');
      const file = input.files[0];
      msg.className = 'notarize-msg';
      if(!file){ msg.textContent = 'Choose the scan or photo of your notarized contract.'; msg.classList.add('error'); return; }
      if(file.size > 10 * 1024 * 1024){ msg.textContent = 'That file is over 10 MB. Upload a smaller scan.'; msg.classList.add('error'); return; }
      const data = new FormData();
      data.append('notarized_document', file);
      btn.disabled = true;
      btn.textContent = 'Uploading…';
      try{
        const res = await fetch(@json(route('tenant.notarized-contract.upload')), {
          method:'POST',
          headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept':'application/json' },
          body: data,
        });
        const body = await res.json().catch(() => ({}));
        if(!res.ok) throw new Error(body.message || (body.errors ? Object.values(body.errors)[0][0] : 'Upload failed. Please try again.'));
        msg.textContent = body.message;
        setTimeout(() => window.location.reload(), 900);
      }catch(err){
        msg.textContent = err.message;
        msg.classList.add('error');
        btn.disabled = false;
        btn.textContent = 'Submit for Review';
      }
    });
  })();
</script>
<script>
(function(){
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
  });

  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
      const logoutRes = await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
      window.location.href = (await logoutRes.json().catch(() => ({}))).redirect || '/';
    });
  }

  const SIDEBAR_COLLAPSE_KEY = 'nestph_sidebar_collapsed';
  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const sidebar = document.getElementById('sidebar');
  if (hamburgerBtn && sidebar) {
    if (localStorage.getItem(SIDEBAR_COLLAPSE_KEY) === '1') {
      sidebar.classList.add('collapsed');
    }
    hamburgerBtn.addEventListener('click', () => {
      const collapsed = sidebar.classList.toggle('collapsed');
      localStorage.setItem(SIDEBAR_COLLAPSE_KEY, collapsed ? '1' : '0');
    });
  }

  document.querySelectorAll('[tabindex="0"]').forEach(el => {
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        el.click();
      }
    });
  });

  // ===== Tabs (Profile / Change Password) =====
  document.querySelectorAll('.tab-pill').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.tab-pill').forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
      tab.classList.add('active');
      tab.setAttribute('aria-selected', 'true');
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      const panel = document.getElementById(tab.dataset.panel);
      if (panel) panel.classList.add('active');
    });
  });

  // ===== Change Password form =====
  const EYE_OPEN = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>';
  const EYE_CLOSED = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.94 10.94 0 0112 19c-7 0-11-7-11-7a20.3 20.3 0 015.06-5.94M9.9 4.24A10.94 10.94 0 0112 4c7 0 11 7 11 7a20.3 20.3 0 01-3.22 4.39M14.12 14.12a3 3 0 11-4.24-4.24"/><path d="M1 1l22 22"/></svg>';

  document.querySelectorAll('.pw-toggle').forEach(btn => {
    btn.innerHTML = EYE_OPEN;
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      const showing = input.type === 'text';
      input.type = showing ? 'password' : 'text';
      btn.innerHTML = showing ? EYE_OPEN : EYE_CLOSED;
      btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
  });

  const form = document.getElementById('changePasswordForm');
  const currentInput = document.getElementById('currentPassword');
  const newInput = document.getElementById('newPassword');
  const confirmInput = document.getElementById('confirmPassword');
  const currentError = document.getElementById('currentPasswordError');
  const newError = document.getElementById('newPasswordError');
  const confirmError = document.getElementById('confirmPasswordError');
  const banner = document.getElementById('formBanner');
  const submitBtn = document.getElementById('submitBtn');

  function clearErrors(){
    [currentError, newError, confirmError].forEach(el => { el.textContent = ''; el.classList.remove('visible'); });
    banner.classList.remove('visible', 'success', 'error');
    banner.textContent = '';
  }

  function showFieldError(el, msg){
    el.textContent = msg;
    el.classList.add('visible');
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();

    if (newInput.value !== confirmInput.value) {
      showFieldError(confirmError, 'New password and confirmation do not match.');
      return;
    }

    submitBtn.disabled = true;
    submitBtn.classList.add('loading');

    try {
      const res = await fetch("{{ route('password.update') }}", {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
          current_password: currentInput.value,
          password: newInput.value,
          password_confirmation: confirmInput.value,
        }),
      });

      if (res.ok) {
        banner.textContent = 'Your password has been updated.';
        banner.classList.add('visible', 'success');
        form.reset();
        document.querySelectorAll('.pw-toggle').forEach(btn => { btn.innerHTML = EYE_OPEN; });
      } else if (res.status === 422) {
        const data = await res.json();
        const errors = data.errors || {};
        let handled = false;
        if (errors.current_password) { showFieldError(currentError, errors.current_password[0]); handled = true; }
        if (errors.password) { showFieldError(newError, errors.password[0]); handled = true; }
        if (!handled) {
          banner.textContent = data.message || 'Something went wrong. Please try again.';
          banner.classList.add('visible', 'error');
        }
      } else {
        let data = {};
        try { data = await res.json(); } catch (e) { /* not JSON, ignore */ }
        banner.textContent = data.message || `Something went wrong (error ${res.status}). Please try again.`;
        banner.classList.add('visible', 'error');
      }
    } catch (err) {
      banner.textContent = 'Network error. Please check your connection and try again.';
      banner.classList.add('visible', 'error');
    }

    submitBtn.disabled = false;
    submitBtn.classList.remove('loading');
  });
})();
</script>

{{-- Profile picture crop window --}}
<dialog class="crop-dialog" id="cropDialog" aria-labelledby="cropTitle">
  <div class="crop-inner">
    <h2 id="cropTitle">Crop your picture</h2>
    <p>Drag to position your face in the square, and zoom if needed. A clear, front-facing photo works best, like a 2x2 ID picture.</p>
    <div class="crop-stage" id="cropStage"><img id="cropImg" alt=""></div>
    <div class="crop-zoom">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="6"/><path d="M8 11h6M20 20l-4.5-4.5"/></svg>
      <input type="range" id="cropZoom" min="1" max="3" step="0.01" value="1" aria-label="Zoom">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="6"/><path d="M8 11h6M11 8v6M20 20l-4.5-4.5"/></svg>
    </div>
    <p class="crop-error" id="cropError" role="alert"></p>
    <div class="crop-actions">
      <button type="button" class="crop-cancel" id="cropCancel">Cancel</button>
      <button type="button" class="crop-save" id="cropSave">Save picture</button>
    </div>
  </div>
</dialog>

<script>
// Profile picture: pick a file, crop it to a square in the browser, upload a 600x600 JPEG.
(function () {
  const input = document.getElementById('photoInput');
  const dialog = document.getElementById('cropDialog');
  const stage = document.getElementById('cropStage');
  const img = document.getElementById('cropImg');
  const zoom = document.getElementById('cropZoom');
  const err = document.getElementById('cropError');
  const saveBtn = document.getElementById('cropSave');
  if (!input || !dialog) return;

  let natW = 0, natH = 0, base = 1, scale = 1, x = 0, y = 0, objectUrl = null;
  const V = () => stage.clientWidth;

  function clamp() {
    const v = V(), w = natW * scale, h = natH * scale;
    x = Math.min(0, Math.max(v - w, x));
    y = Math.min(0, Math.max(v - h, y));
  }
  function draw() {
    clamp();
    img.style.width = (natW * scale) + 'px';
    img.style.height = (natH * scale) + 'px';
    img.style.transform = 'translate(' + x + 'px,' + y + 'px)';
  }
  function setZoom(z, cx, cy) {
    const v = V();
    cx = cx ?? v / 2; cy = cy ?? v / 2;
    const old = scale;
    scale = base * z;
    // keep the point under the cursor (or the center) in place
    x = cx - (cx - x) * (scale / old);
    y = cy - (cy - y) * (scale / old);
    draw();
  }

  function open() { input.value = ''; input.click(); }
  document.getElementById('photoEditBtn').addEventListener('click', open);
  document.getElementById('photoChangeLink')?.addEventListener('click', open);

  input.addEventListener('change', () => {
    const file = input.files[0];
    if (!file) return;
    if (!/^image\/(jpeg|png|webp)$/.test(file.type)) { alert('Please choose a JPG, PNG or WebP picture.'); return; }
    if (objectUrl) URL.revokeObjectURL(objectUrl);
    objectUrl = URL.createObjectURL(file);
    img.onload = () => {
      natW = img.naturalWidth; natH = img.naturalHeight;
      err.textContent = '';
      dialog.showModal();
      base = V() / Math.min(natW, natH);  // smallest size that fills the square
      scale = base; zoom.value = 1;
      x = (V() - natW * scale) / 2; y = (V() - natH * scale) / 2;
      draw();
    };
    img.src = objectUrl;
  });

  // Drag to move (mouse, touch, pen)
  let drag = null;
  stage.addEventListener('pointerdown', e => { drag = { px: e.clientX, py: e.clientY, x, y }; stage.setPointerCapture(e.pointerId); });
  stage.addEventListener('pointermove', e => { if (!drag) return; x = drag.x + e.clientX - drag.px; y = drag.y + e.clientY - drag.py; draw(); });
  stage.addEventListener('pointerup', () => { drag = null; });
  stage.addEventListener('pointercancel', () => { drag = null; });
  // Zoom: slider, or mouse wheel over the picture
  zoom.addEventListener('input', () => setZoom(Number(zoom.value)));
  stage.addEventListener('wheel', e => {
    e.preventDefault();
    const r = stage.getBoundingClientRect();
    const z = Math.min(3, Math.max(1, Number(zoom.value) - e.deltaY * 0.002));
    zoom.value = z; setZoom(z, e.clientX - r.left, e.clientY - r.top);
  }, { passive: false });
  // Keyboard: arrow keys move the picture
  stage.tabIndex = 0;
  stage.setAttribute('aria-label', 'Picture position. Use arrow keys to move.');
  stage.addEventListener('keydown', e => {
    const step = 10, map = { ArrowLeft: [step, 0], ArrowRight: [-step, 0], ArrowUp: [0, step], ArrowDown: [0, -step] };
    if (!map[e.key]) return;
    e.preventDefault(); x += map[e.key][0]; y += map[e.key][1]; draw();
  });

  document.getElementById('cropCancel').addEventListener('click', () => dialog.close());

  saveBtn.addEventListener('click', () => {
    const OUT = 600, src = V() / scale;
    const canvas = document.createElement('canvas');
    canvas.width = OUT; canvas.height = OUT;
    canvas.getContext('2d').drawImage(img, -x / scale, -y / scale, src, src, 0, 0, OUT, OUT);
    saveBtn.disabled = true; saveBtn.textContent = 'Saving…'; err.textContent = '';
    canvas.toBlob(async blob => {
      const fd = new FormData();
      fd.append('photo', blob, 'profile.jpg');
      try {
        const res = await fetch('{{ route('tenant.account.photo') }}', {
          method: 'POST', body: fd,
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        });
        const body = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(body.message || 'Could not save your picture. Please try again.');
        const url = body.url + '?t=' + Date.now();
        const photo = document.getElementById('profilePhotoImg');
        photo.src = url; photo.hidden = false;
        document.getElementById('profilePhotoInitials').hidden = true;
        // Top-bar avatar too, if it shows a picture
        const avatar = document.querySelector('.topbar a.topbar-icon');
        if (avatar) {
          avatar.style.padding = '0'; avatar.style.overflow = 'hidden';
          avatar.innerHTML = '<img src="' + url + '" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;display:block;">';
        }
        dialog.close();
      } catch (e) {
        err.textContent = e.message;
      }
      saveBtn.disabled = false; saveBtn.textContent = 'Save picture';
    }, 'image/jpeg', 0.9);
  });
})();
</script>
@include('partials.photo-lightbox')
</body>
</html>