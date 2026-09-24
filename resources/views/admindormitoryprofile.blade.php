<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH - Dormitory Profile</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  :root{
    --green-dark:#3f6b4a; --green-mid:#4f7c57;
    --green-sidebar-top:#5b8a63; --green-sidebar-bottom:#2c4a35;
    --green-accent:#2f6f3c; --green-btn:#2f6b3a; --green-btn-hover:#255a2f;
    --status-occupied:#d9564f; --status-vacant:#7fc98a; --status-vacant-bg:#d9f2dd;
    --status-maintenance:#c9962f; --status-maintenance-bg:#f6ecd6;
    --purple:#7a4fc9; --purple-bg:#e9defa;
    --blue:#33629e; --blue-bg:#e3ecf7;
    --orange:#c9962f; --orange-bg:#f6ecd6;
    --bg-page:#eef1ee; --card-bg:#ffffff;
    --text-dark:#243026; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e2e6e2;
    --font-body:'Roboto',-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;
  }
  /* Shared sidebar/topbar/content-header/reset styles now live in
     public/css/admin.css (linked above). The rules below are kept because
     this page deliberately customizes them (different topbar-icon look,
     tighter .content padding, and a reduced .page-head margin-bottom to
     make room for .page-sub) — see admin-css migration notes. */
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:#eaf0ea; display:flex; align-items:center; justify-content:center; color:var(--green-accent); }
  .topbar-icon svg{ width:17px; height:17px; }

  .content{ padding:24px 28px 60px 28px; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:6px; }
  .page-head h1{ font-size:22px; font-weight:700; margin:0; color:var(--green-accent); }
  .page-sub{ font-size:12.5px; color:var(--text-mid); margin:0 0 22px 46px; }

  /* ===== Layout: form column + sticky preview column ===== */
  .profile-grid{ display:grid; grid-template-columns:1fr 340px; gap:22px; align-items:start; }
  @media (max-width:1080px){ .profile-grid{ grid-template-columns:1fr; } }
  /* Grid columns grow to fit their widest unbreakable text by default, so one
     long file name or word made the page scroll sideways on phones. */
  .profile-grid > *{ min-width:0; }
  .profile-grid{ overflow-wrap:anywhere; }
  @media (max-width:640px){ .page-sub{ margin-left:0; } .card{ padding:18px 16px; } }

  .card{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:22px 24px; margin-bottom:20px; }
  .card h2{ font-size:15px; font-weight:700; color:var(--green-accent); margin:0 0 4px 0; }
  .card .card-sub{ font-size:12px; color:var(--text-light); margin:0 0 18px 0; }

  /* Cover photo */
  .cover-wrap{ position:relative; border-radius:12px; overflow:hidden; background:#e2e6e2; height:220px; margin-bottom:0; }
  .cover-wrap img{ width:100%; height:100%; object-fit:cover; display:block; }
  .cover-empty{ width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-light); font-size:13px; flex-direction:column; gap:8px; }
  .cover-empty svg{ width:30px; height:30px; }
  .cover-actions{ position:absolute; bottom:14px; left:14px; right:14px; display:flex; gap:10px; flex-wrap:wrap; }

  .btn{ font-size:12.5px; font-weight:600; padding:10px 18px; border-radius:7px; border:1px solid var(--border); background:#fff; color:var(--text-mid); cursor:pointer; font-family:var(--font-body); display:inline-flex; align-items:center; gap:7px; }
  .btn:hover:not(:disabled){ background:#f7f9f7; }
  .btn:disabled{ opacity:.5; cursor:not-allowed; }
  .btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .btn.primary:hover:not(:disabled){ background:var(--green-btn-hover); }
  .btn.warn{ background:#fbeceb; border-color:#f2cfcc; color:var(--status-occupied); }
  .btn.warn:hover:not(:disabled){ background:#f6d9d7; }
  .btn.sm{ padding:7px 13px; font-size:11.5px; }
  .btn svg{ width:14px; height:14px; }

  /* Form fields */
  .field-row{ display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
  .field-row.full{ grid-template-columns:1fr; }
  @media (max-width:640px){ .field-row{ grid-template-columns:1fr; } }
  .field{ display:flex; flex-direction:column; gap:6px; }
  .field label{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .field .req{ color:var(--status-occupied); }
  .field input, .field textarea{ border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13.5px; font-family:var(--font-body); color:var(--text-dark); }
  .field textarea{ resize:vertical; min-height:90px; }
  .field input:focus, .field textarea:focus{ outline:none; border-color:var(--green-accent); }
  .char-count{ font-size:11px; color:var(--text-light); text-align:right; }

  .form-actions{ display:flex; justify-content:flex-end; gap:10px; margin-top:4px; }

  /* House rules */
  .rules-list{ list-style:none; margin:0; padding:0; }
  .rule-row{ display:flex; align-items:center; gap:10px; padding:10px 4px; border-bottom:1px solid #f0f2f0; }
  .rule-row:last-child{ border-bottom:none; }
  .rule-num{ width:20px; height:20px; border-radius:50%; background:#eaf0ea; color:var(--green-accent); font-size:10.5px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .rule-text{ flex:1; font-size:13px; color:var(--text-dark); }
  .rule-text input{ width:100%; border:1px solid var(--green-accent); border-radius:6px; padding:6px 8px; font-size:13px; font-family:var(--font-body); display:none; }
  .rule-row.editing .rule-text span{ display:none; }
  .rule-row.editing .rule-text input{ display:block; }
  .rule-actions{ display:flex; gap:6px; flex-shrink:0; }
  .icon-btn{ width:28px; height:28px; border-radius:6px; border:1px solid var(--border); background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); }
  .icon-btn:hover{ background:#f7f9f7; }
  .icon-btn.danger:hover{ background:#fbeceb; border-color:#f2cfcc; color:var(--status-occupied); }
  .icon-btn svg{ width:13px; height:13px; }
  .rules-empty{ font-size:12.5px; color:var(--text-light); font-style:italic; padding:14px 4px; }
  .add-rule-row{ display:flex; gap:10px; margin-top:14px; }
  .add-rule-row input{ flex:1; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; font-family:var(--font-body); }

  /* Amenities */
  .amenities-grid{ display:grid; grid-template-columns:1fr 1fr; gap:10px 20px; }
  @media (max-width:640px){ .amenities-grid{ grid-template-columns:1fr; } }
  .amenity-check{ display:flex; align-items:center; gap:10px; padding:8px 4px; cursor:pointer; }
  .amenity-check input{ width:17px; height:17px; accent-color:var(--green-accent); cursor:pointer; }
  .amenity-check .amenity-icon{ width:20px; height:20px; color:var(--green-accent); flex-shrink:0; }
  .amenity-check .amenity-icon svg{ width:100%; height:100%; }
  .amenity-check span.label-text{ font-size:13px; color:var(--text-dark); }

  /* Legitimacy documents */
  .doc-row{ display:flex; align-items:center; justify-content:space-between; gap:14px; padding:14px 0; border-bottom:1px solid #f0f2f0; }
  .doc-row:last-child{ border-bottom:none; }
  .doc-info{ display:flex; align-items:center; gap:12px; min-width:0; }
  .doc-icon{ width:38px; height:38px; border-radius:9px; background:#eaf0ea; color:var(--green-accent); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .doc-icon svg{ width:18px; height:18px; }
  .doc-title{ font-size:13px; font-weight:600; color:var(--text-dark); }
  .doc-status{ font-size:11.5px; color:var(--text-light); margin-top:2px; }
  .doc-status.uploaded{ color:var(--green-accent); font-weight:600; }
  .doc-actions{ display:flex; gap:8px; flex-shrink:0; }

  .badge-pill{ display:inline-flex; align-items:center; gap:8px; background:#fff; border:1px solid var(--border); border-radius:8px; padding:9px 13px; color:var(--text-dark); font-size:11px; font-weight:600; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
  .badge-pill svg{ width:15px; height:15px; color:var(--green-accent); flex-shrink:0; }

  /* Right column: public listing preview */
  .preview-col{ position:sticky; top:90px; }
  .preview-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; overflow:hidden; }
  .preview-head{ padding:16px 18px 0 18px; }
  .preview-head h3{ font-size:13px; font-weight:700; color:var(--green-accent); margin:0 0 3px 0; }
  .preview-head p{ font-size:11.5px; color:var(--text-light); margin:0 0 14px 0; line-height:1.5; }
  .preview-cover{ width:100%; height:150px; background:#e2e6e2; margin:0; }
  .preview-cover img{ width:100%; height:100%; object-fit:cover; display:block; }
  .preview-body{ padding:16px 18px 20px 18px; }
  .preview-badge{ margin-bottom:10px; }
  .preview-name{ font-size:16px; font-weight:700; color:var(--text-dark); margin-bottom:4px; }
  .preview-addr{ display:flex; align-items:center; gap:5px; font-size:12px; color:var(--text-mid); margin-bottom:12px; }
  .preview-addr svg{ width:13px; height:13px; flex-shrink:0; }
  .preview-desc{ font-size:12px; color:var(--text-mid); line-height:1.6; margin-bottom:16px; }
  .preview-section-label{ font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); margin-bottom:10px; }
  .preview-amenities{ display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-bottom:16px; }
  .preview-amenity-icon{ width:30px; height:30px; border-radius:8px; background:#eaf0ea; color:var(--green-accent); display:flex; align-items:center; justify-content:center; }
  .preview-amenity-icon svg{ width:15px; height:15px; }
  .preview-rules{ list-style:none; margin:0; padding:0; }
  .preview-rules li{ display:flex; align-items:flex-start; gap:7px; font-size:11.5px; color:var(--text-mid); margin-bottom:8px; line-height:1.5; }
  .preview-rules li svg{ width:13px; height:13px; color:var(--green-accent); flex-shrink:0; margin-top:1px; }
  .preview-empty{ font-size:11.5px; color:var(--text-light); font-style:italic; }

  .toast{ position:fixed; bottom:22px; right:22px; background:var(--green-accent); color:#fff; padding:12px 20px; border-radius:8px; font-size:13px; display:none; z-index:99; box-shadow:0 6px 18px rgba(0,0,0,.2); }
  .toast.error{ background:var(--status-occupied); }
  .toast.visible{ display:block; }
  .badge-pill img{ width:22px; height:22px; border-radius:4px; object-fit:cover; flex-shrink:0; }

  .legit-docs-grid{ display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:18px; }
  @media (max-width:640px){ .legit-docs-grid{ grid-template-columns:1fr; } }
  .legit-doc-card{ border:1px solid var(--border); border-radius:12px; padding:18px; background:#fbfcfb; }
  .legit-doc-card h3{ font-size:13.5px; font-weight:700; color:var(--text-dark); margin:0 0 14px 0; }
  .legit-doc-preview{ width:100%; height:120px; border-radius:9px; overflow:hidden; background:#fff; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; margin-bottom:14px; }
  .legit-doc-preview img{ width:100%; height:100%; object-fit:cover; display:block; }
  .legit-doc-file-icon{ color:var(--green-accent); }
  .legit-doc-file-icon svg{ width:34px; height:34px; }
  .legit-doc-empty{ display:flex; flex-direction:column; align-items:center; gap:6px; color:var(--text-light); font-size:11px; }
  .legit-doc-empty svg{ width:28px; height:28px; }
  .legit-doc-meta{ margin-bottom:14px; }
  .legit-doc-meta-row{ display:flex; justify-content:space-between; gap:10px; font-size:11.5px; padding:5px 0; border-bottom:1px solid #f0f2f0; }
  .legit-doc-meta-row:last-child{ border-bottom:none; }
  .legit-doc-meta-row .k{ font-weight:700; color:var(--text-mid); }
  .legit-doc-meta-row .v{ color:var(--text-dark); text-align:right; word-break:break-word; max-width:60%; }
  .legit-update-btn{ width:100%; background:#fff; border:1.5px solid var(--green-accent); color:var(--green-accent); font-weight:700; font-size:12px; padding:9px; border-radius:6px; cursor:pointer; font-family:var(--font-body); }
  .legit-update-btn:hover{ background:#f0f7f1; }
  .legit-remove-link{ display:block; width:100%; text-align:center; background:none; border:none; color:var(--status-occupied); font-size:11.5px; font-weight:600; margin-top:8px; cursor:pointer; font-family:var(--font-body); }
  .legit-remove-link:hover{ text-decoration:underline; }
  .rv-card-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:12px; }
  .rv-card-head .btn{ flex-shrink:0; }
  @media (max-width:640px){ .rv-card-head{ flex-direction:column; gap:0; } .rv-card-head .btn{ margin-bottom:18px; } }
  .rv-summary{ font-size:12.5px; color:var(--text-mid); margin:0 0 14px 0; }
  .rv-summary strong{ color:var(--text-dark); }
  .rv-toolbar{ display:flex; flex-direction:column; gap:12px; margin-bottom:14px; }
  .rv-tabs{ display:flex; gap:6px; flex-wrap:wrap; }
  .rv-tab{ font-size:12px; font-weight:600; padding:7px 12px; border-radius:7px; border:1px solid var(--border); background:#fff; color:var(--text-mid); cursor:pointer; font-family:var(--font-body); }
  .rv-tab:hover{ background:#f7f9f7; }
  .rv-tab.needs-review{ color:var(--text-dark); font-weight:700; border-color:var(--text-light); }
  .rv-tab.active, .rv-tab.active:hover{ background:var(--green-accent); border-color:var(--green-accent); color:#fff; }
  .rv-filters{ display:grid; grid-template-columns:1fr 160px; gap:12px; }
  @media (max-width:640px){ .rv-filters{ grid-template-columns:1fr; } }
  .rv-field{ display:flex; flex-direction:column; gap:6px; min-width:0; }
  .rv-field label{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .rv-field input, .rv-field select{ border:1px solid var(--border); border-radius:8px; padding:9px 10px; font-size:12.5px; font-family:var(--font-body); color:var(--text-dark); background:#fff; }
  .rv-field input:focus, .rv-field select:focus{ outline:none; border-color:var(--green-accent); box-shadow:0 0 0 2px #eaf0ea; }
  #reviewModerationCard .btn:focus-visible, #reviewModerationCard .rv-tab:focus-visible{ outline:2px solid var(--green-accent); outline-offset:2px; }
  .rv-list{ list-style:none; margin:0; padding:0; }
  .rv-row{ padding:14px 4px; border-bottom:1px solid #f0f2f0; }
  .rv-row:last-child{ border-bottom:none; }
  .rv-head{ display:flex; justify-content:space-between; gap:10px; align-items:baseline; }
  .rv-name{ font-size:13px; font-weight:700; color:var(--text-dark); }
  .rv-date{ font-size:11.5px; color:var(--text-mid); margin-left:8px; }
  .rv-status{ font-size:11.5px; font-weight:700; flex-shrink:0; }
  .rv-row[data-status="published"] .rv-status{ color:var(--green-accent); }
  .rv-row[data-status="hidden"] .rv-status{ color:var(--text-dark); }
  .rv-row[data-status="removed"] .rv-status{ color:var(--text-mid); }
  .rv-stars{ color:#f5b301; font-size:13px; letter-spacing:1px; margin:4px 0; }
  .rv-comment{ font-size:13px; color:var(--text-dark); line-height:1.6; white-space:pre-line; word-break:break-word; }
  .rv-comment.empty{ color:var(--text-mid); font-style:italic; }
  .rv-row[data-status="removed"] .rv-comment{ color:var(--text-mid); }
  .rv-meta{ font-size:11.5px; color:var(--text-mid); margin-top:6px; }
  .rv-meta:empty{ display:none; }
  .rv-actions{ display:flex; gap:8px; margin-top:10px; flex-wrap:wrap; }
  .rv-row[data-status="published"] .act-publish,
  .rv-row[data-status="published"] .act-restore,
  .rv-row[data-status="hidden"] .act-hide,
  .rv-row[data-status="hidden"] .act-restore,
  .rv-row[data-status="removed"] .act-publish,
  .rv-row[data-status="removed"] .act-hide,
  .rv-row[data-status="removed"] .act-remove{ display:none; }
  .rv-empty{ font-size:12.5px; color:var(--text-mid); font-style:italic; padding:14px 4px; display:none; margin:0; }
</style>
</head>
<body>
<div class="app">

@include('partials.admin-sidebar')

  <div class="main">
    <div class="topbar">
      <div class="hamburger" id="hamburgerBtn" tabindex="0" aria-label="Toggle sidebar"><span></span><span></span><span></span></div>
      <div class="topbar-right">
        <div class="topbar-icon" tabindex="0" aria-label="Account"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}" tabindex="0" aria-label="Back to dashboard"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Dormitory Profile</h1>
      </div>
      <p class="page-sub">Set up your dormitory's public listing: basic info, house rules, amenities, and legitimacy documents.</p>

      <div class="profile-grid">
        <div class="form-col">

          {{-- Cover Photo --}}
          <div class="card">
            <h2>Cover Photo</h2>
            <p class="card-sub">Shown at the top of your public Dormitory Profile.</p>
            <div class="cover-wrap" id="coverWrap">
              @if($coverPhotoUrl)
                <img src="{{ $coverPhotoUrl }}" id="coverImg" alt="Cover photo">
              @else
                <div class="cover-empty" id="coverEmpty">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M21 16l-5-5-9 9"/></svg>
                  <span>No cover photo uploaded yet</span>
                </div>
              @endif
              <div class="cover-actions">
                <button type="button" class="btn" id="changeCoverBtn">Change Cover Photo</button>
              </div>
            </div>
            <input type="file" id="coverPhotoInput" accept=".jpg,.jpeg,.png,.webp" style="display:none;">
          </div>

          {{-- Dormitory Information --}}
          <div class="card">
            <h2>Dormitory Information</h2>
            <p class="card-sub">This is how prospective tenants will see your dormitory profile.</p>

            <div class="field-row">
              <div class="field">
                <label for="dormName">Dormitory Name <span class="req">*</span></label>
                <input type="text" id="dormName" maxlength="150" value="{{ $profile->dorm_name }}">
              </div>
              <div class="field">
                <label for="contactNumber">Contact Number <span class="req">*</span></label>
                <input type="text" id="contactNumber" maxlength="20" value="{{ $profile->contact_number }}">
              </div>
            </div>
            <div class="field-row">
              <div class="field">
                <label for="contactEmail">Email Address</label>
                <input type="email" id="contactEmail" maxlength="150" value="{{ $profile->contact_email }}">
              </div>
              <div class="field">
                <label for="address">Complete Address <span class="req">*</span></label>
                <input type="text" id="address" maxlength="255" value="{{ $profile->address }}">
              </div>
            </div>
            <div class="field-row full">
              <div class="field">
                <label for="description">Description <span class="req">*</span></label>
                <textarea id="description" maxlength="500">{{ $profile->description }}</textarea>
                <div class="char-count"><span id="descCount">0</span> / 500</div>
              </div>
            </div>

            <div class="form-actions">
              <button type="button" class="btn" id="cancelBtn">Cancel</button>
              <button type="button" class="btn primary" id="saveProfileBtn">Save Changes</button>
            </div>
          </div>

          {{-- House Rules --}}
          <div class="card">
            <h2>House Rules</h2>
            <p class="card-sub">List of rules that all tenants must follow.</p>

            <ul class="rules-list" id="rulesList">
              @forelse($houseRules as $i => $rule)
                <li class="rule-row" data-id="{{ $rule->id }}">
                  <span class="rule-num">{{ $i + 1 }}</span>
                  <div class="rule-text">
                    <span>{{ $rule->rule_text }}</span>
                    <input type="text" value="{{ $rule->rule_text }}" maxlength="500" aria-label="Edit house rule text">
                  </div>
                  <div class="rule-actions">
                    <button type="button" class="icon-btn edit-rule-btn" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg></button>
                    <button type="button" class="icon-btn danger delete-rule-btn" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m3 0l-1 14a2 2 0 01-2 2H7a2 2 0 01-2-2L4 6"/></svg></button>
                  </div>
                </li>
              @empty
                <li class="rules-empty" id="rulesEmptyMsg">No house rules added yet.</li>
              @endforelse
            </ul>

            <div class="add-rule-row">
              <input type="text" id="newRuleInput" placeholder="Add a new rule…" maxlength="500" aria-label="New house rule text">
              <button type="button" class="btn primary sm" id="addRuleBtn">+ Add Rule</button>
            </div>
          </div>

          {{-- Amenities --}}
          <div class="card">
            <h2>Amenities</h2>
            <p class="card-sub">Select the amenities available in your dormitory.</p>

            <div class="amenities-grid" id="amenitiesGrid">
              @foreach($amenities as $amenity)
                <label class="amenity-check">
                  <input type="checkbox" data-id="{{ $amenity->id }}" data-key="{{ $amenity->key }}" {{ $amenity->is_enabled ? 'checked' : '' }}>
                  <span class="amenity-icon">@include('partials-amenity-icon', ['key' => $amenity->key])</span>
                  <span class="label-text">{{ $amenity->label }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Review Moderation --}}
          <div class="card" id="reviewModerationCard">
            <div class="rv-card-head">
              <div>
                <h2>Review Moderation</h2>
                <p class="card-sub">Reviews with offensive language (English or Filipino), links, contact details, or spam patterns are hidden automatically and wait here for your decision.</p>
              </div>
              <button type="button" class="btn sm" id="rvRescanBtn">Re-scan Reviews</button>
            </div>

            <p class="rv-summary">Public rating: <strong id="rvAverage">{{ number_format($reviewAverage, 1) }}</strong> from <strong id="rvPublicCount">{{ $reviewCounts['published'] }}</strong> public <span id="rvPublicNoun">{{ $reviewCounts['published'] == 1 ? 'review' : 'reviews' }}</span>.</p>

            <div class="rv-toolbar">
              <div class="rv-tabs" role="group" aria-label="Filter by status">
                <button type="button" class="rv-tab active" data-filter="all" aria-pressed="true">All (<span data-count="all">{{ $reviewCounts['all'] }}</span>)</button>
                <button type="button" class="rv-tab" data-filter="published" aria-pressed="false">Public (<span data-count="published">{{ $reviewCounts['published'] }}</span>)</button>
                <button type="button" class="rv-tab {{ $reviewCounts['hidden'] > 0 ? 'needs-review' : '' }}" data-filter="hidden" aria-pressed="false">Hidden (<span data-count="hidden">{{ $reviewCounts['hidden'] }}</span>)</button>
                <button type="button" class="rv-tab" data-filter="removed" aria-pressed="false">Removed (<span data-count="removed">{{ $reviewCounts['removed'] }}</span>)</button>
              </div>
              <div class="rv-filters">
                <div class="rv-field">
                  <label for="rvSearch">Search name or comment</label>
                  <input type="search" id="rvSearch" maxlength="100">
                </div>
                <div class="rv-field">
                  <label for="rvRating">Rating</label>
                  <select id="rvRating">
                    <option value="">All ratings</option>
                    @for ($s = 5; $s >= 1; $s--)
                      <option value="{{ $s }}">{{ $s }} {{ $s == 1 ? 'star' : 'stars' }}</option>
                    @endfor
                  </select>
                </div>
              </div>
            </div>

            <ul class="rv-list" id="rvList">
              @foreach($reviews as $review)
                <li class="rv-row"
                    data-id="{{ $review->id }}"
                    data-status="{{ $review->status }}"
                    data-rating="{{ $review->rating }}"
                    data-search="{{ mb_strtolower(($review->tenant?->full_name ?? '') . ' ' . ($review->comment ?? '')) }}">
                  <div class="rv-head">
                    <div>
                      <span class="rv-name">{{ $review->tenant?->full_name ?? 'Former tenant' }}</span>
                      <span class="rv-date">{{ $review->created_at->format('M j, Y') }}</span>
                    </div>
                    <span class="rv-status">{{ $review->status_label }}</span>
                  </div>
                  <div class="rv-stars" role="img" aria-label="Rated {{ $review->rating }} out of 5">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                  @if($review->comment)
                    <div class="rv-comment">{{ $review->comment }}</div>
                  @else
                    <div class="rv-comment empty">Rating only, no written comment.</div>
                  @endif
                  <div class="rv-meta">{{ $review->moderationSummary() }}</div>
                  <div class="rv-actions">
                    <button type="button" class="btn sm primary act-publish">Publish</button>
                    <button type="button" class="btn sm primary act-restore">Restore</button>
                    <button type="button" class="btn sm act-hide">Hide</button>
                    <button type="button" class="btn sm warn act-remove">Remove</button>
                  </div>
                </li>
              @endforeach
            </ul>
            <p class="rv-empty" id="rvEmpty" tabindex="-1">No reviews match this view.</p>
          </div>

          {{-- Legitimacy Documents --}}
          <div class="card">
            <h2>Legitimacy Documents</h2>
            <p class="card-sub">Uploading BIR Registration displays a verification badge on your public profile per BIR RMC No. 038-2026.</p>

            <div class="doc-row">
              <div class="doc-info">
                <div class="doc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg></div>
                <div>
                  <div class="doc-title">Policies & House Rules File (PDF)</div>
                  <div class="doc-status {{ $policiesFileName ? 'uploaded' : '' }}" id="policiesFileStatus">{{ $policiesFileName ?? 'No file uploaded' }}</div>
                </div>
              </div>
              <div class="doc-actions">
                <button type="button" class="btn sm" id="policiesFileBtn">{{ $policiesFileName ? 'Replace' : 'Upload' }}</button>
              </div>
            </div>
            <input type="file" id="policiesFileInput" accept=".pdf" style="display:none;">

            <div class="legit-docs-grid">

              {{-- Business Permit --}}
              <div class="legit-doc-card">
                <h3>Business Permit</h3>
                <div class="legit-doc-preview" id="businessPermitPreview">
                  @if($businessPermitImageUrl)
                    <img src="{{ $businessPermitImageUrl }}" alt="">
                  @elseif($businessPermitName)
                    <div class="legit-doc-file-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg></div>
                  @else
                    <div class="legit-doc-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg><span>No file uploaded</span></div>
                  @endif
                </div>
                <div class="legit-doc-meta">
                  <div class="legit-doc-meta-row"><span class="k">Document File</span><span class="v" id="businessPermitFileNameMeta">{{ $businessPermitName ?? '—' }}</span></div>
                  <div class="legit-doc-meta-row"><span class="k">File Type</span><span class="v" id="businessPermitFileTypeMeta">{{ $businessPermitExt ?? '—' }}</span></div>
                </div>
                <button type="button" class="legit-update-btn" id="businessPermitBtn">{{ $businessPermitName ? 'Update Document' : 'Upload Document' }}</button>
                <button type="button" class="legit-remove-link" id="businessPermitRemoveBtn" style="{{ $businessPermitName ? '' : 'display:none;' }}">Remove</button>
              </div>

              {{-- BIR Registration --}}
              <div class="legit-doc-card">
                <h3>BIR Registration</h3>
                <div class="legit-doc-preview" id="birRegistrationPreview">
                  @if($birRegistrationImageUrl)
                    <img src="{{ $birRegistrationImageUrl }}" alt="">
                  @elseif($birRegistrationName)
                    <div class="legit-doc-file-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg></div>
                  @else
                    <div class="legit-doc-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg><span>No file uploaded</span></div>
                  @endif
                </div>
                <div class="legit-doc-meta">
                  <div class="legit-doc-meta-row"><span class="k">Document File</span><span class="v" id="birRegistrationFileNameMeta">{{ $birRegistrationName ?? '—' }}</span></div>
                  <div class="legit-doc-meta-row"><span class="k">File Type</span><span class="v" id="birRegistrationFileTypeMeta">{{ $birRegistrationExt ?? '—' }}</span></div>
                </div>
                <button type="button" class="legit-update-btn" id="birRegistrationBtn">{{ $birRegistrationName ? 'Update Document' : 'Upload Document' }}</button>
                <button type="button" class="legit-remove-link" id="birRegistrationRemoveBtn" style="{{ $birRegistrationName ? '' : 'display:none;' }}">Remove</button>
              </div>

            </div>
            <input type="file" id="businessPermitInput" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
            <input type="file" id="birRegistrationInput" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
          </div>

        </div>

        {{-- Public Listing Preview --}}
        <div class="preview-col">
          <div class="preview-card">
            <div class="preview-head">
              <h3>Public Listing Preview</h3>
              <p>This is how prospective tenants will see your dormitory profile.</p>
            </div>
            <div class="preview-cover">
              @if($coverPhotoUrl)
                <img src="{{ $coverPhotoUrl }}" id="previewCoverImg" alt="">
              @else
                <img src="" id="previewCoverImg" alt="" style="display:none;">
              @endif
            </div>
            <div class="preview-body">
              <div class="preview-badge" id="previewBadgeWrap" style="{{ $profile->isBirVerified() ? '' : 'display:none;' }}">
                <div class="badge-pill" id="previewBadgePill">
                  @if($birRegistrationImageUrl)
                    <img src="{{ $birRegistrationImageUrl }}" alt="">
                  @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                  @endif
                  <span>Registered with the Bureau of Internal Revenue</span>
                </div>
              </div>
              <div class="preview-name" id="previewName">{{ $profile->dorm_name }}</div>
              <div class="preview-addr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg><span id="previewAddr">{{ $profile->address }}</span></div>
              <div class="preview-desc" id="previewDesc">{{ $profile->description }}</div>

              <div class="preview-section-label">Amenities</div>
              <div class="preview-amenities" id="previewAmenities">
                @foreach($amenities->where('is_enabled', true) as $amenity)
                  <div class="preview-amenity-icon" data-key="{{ $amenity->key }}" title="{{ $amenity->label }}">@include('partials-amenity-icon', ['key' => $amenity->key])</div>
                @endforeach
              </div>

              <div class="preview-section-label">House Rules</div>
              <ul class="preview-rules" id="previewRules">
                @forelse($houseRules as $rule)
                  <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg><span>{{ $rule->rule_text }}</span></li>
                @empty
                  <li class="preview-empty">No house rules added yet.</li>
                @endforelse
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script>
(function(){
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

  function toast(msg, isError = false){
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.toggle('error', !!isError);
    t.classList.add('visible');
    clearTimeout(window._nestToastTimer);
    window._nestToastTimer = setTimeout(() => t.classList.remove('visible'), 3200);
  }

  async function api(url, options = {}){
    const headers = Object.assign({ 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, options.headers || {});
    const res = await fetch(url, Object.assign({}, options, { headers }));
    let body = {};
    try { body = await res.json(); } catch(e) {}
    if(!res.ok){
      const err = new Error(body.message || 'Something went wrong.');
      err.status = res.status;
      err.body = body;
      throw err;
    }
    return body;
  }

  const $ = (id) => document.getElementById(id);

  // ===== Live preview binding =====
  function bindLivePreview(inputId, previewId){
    const input = $(inputId);
    const preview = $(previewId);
    if(!input || !preview) return;
    input.addEventListener('input', () => { preview.textContent = input.value; });
  }
  bindLivePreview('dormName', 'previewName');
  bindLivePreview('address', 'previewAddr');
  bindLivePreview('description', 'previewDesc');

  const descInput = $('description');
  const descCount = $('descCount');
  function updateDescCount(){ descCount.textContent = descInput.value.length; }
  descInput.addEventListener('input', updateDescCount);
  updateDescCount();

  // ===== Save Changes (basic info) =====
  $('saveProfileBtn').addEventListener('click', async function(){
    const dormName = $('dormName').value.trim();
    const contactNumber = $('contactNumber').value.trim();
    const address = $('address').value.trim();
    const description = $('description').value.trim();

    if(!dormName || !contactNumber || !address || !description){
      return toast('Please fill in all required fields.', true);
    }

    this.disabled = true;
    try {
      await api('{{ route('dormitory-profile.update') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          dorm_name: dormName,
          contact_number: contactNumber,
          contact_email: $('contactEmail').value.trim(),
          address: address,
          description: description,
        }),
      });
      toast('Dormitory profile updated successfully.');
    } catch(e){
      toast(e.message, true);
    }
    this.disabled = false;
  });

  $('cancelBtn').addEventListener('click', () => window.location.reload());

  // ===== Cover Photo =====
  $('changeCoverBtn').addEventListener('click', () => $('coverPhotoInput').click());
  $('coverPhotoInput').addEventListener('change', async function(){
    const file = this.files[0];
    if(!file) return;
    const fd = new FormData();
    fd.append('cover_photo', file);
    try {
      const body = await api('{{ route('dormitory-profile.cover-photo') }}', { method: 'POST', body: fd });
      const url = body.cover_photo_url + '?t=' + Date.now();

      let img = $('coverImg');
      if(!img){
        $('coverEmpty')?.remove();
        img = document.createElement('img');
        img.id = 'coverImg';
        $('coverWrap').prepend(img);
      }
      img.src = url;

      let previewImg = $('previewCoverImg');
      previewImg.src = url;
      previewImg.style.display = '';

      toast('Cover photo updated.');
    } catch(e){ toast(e.message, true); }
    this.value = '';
  });

  // ===== Policies file =====
  $('policiesFileBtn').addEventListener('click', () => $('policiesFileInput').click());
  $('policiesFileInput').addEventListener('change', async function(){
    const file = this.files[0];
    if(!file) return;
    const fd = new FormData();
    fd.append('policies_file', file);
    try {
      const body = await api('{{ route('dormitory-profile.policies-file') }}', { method: 'POST', body: fd });
      $('policiesFileStatus').textContent = body.file_name;
      $('policiesFileStatus').classList.add('uploaded');
      $('policiesFileBtn').textContent = 'Replace';
      toast('Policies file uploaded.');
    } catch(e){ toast(e.message, true); }
    this.value = '';
  });

  // ===== Business Permit =====
  function renderBusinessPermitPreview(fileName, imageUrl){
    const box = $('businessPermitPreview');
    if(imageUrl){
      box.innerHTML = `<img src="${imageUrl}?t=${Date.now()}" alt="">`;
    } else if(fileName){
      box.innerHTML = '<div class="legit-doc-file-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg></div>';
    } else {
      box.innerHTML = '<div class="legit-doc-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg><span>No file uploaded</span></div>';
    }
  }
  $('businessPermitBtn').addEventListener('click', () => $('businessPermitInput').click());
  $('businessPermitInput').addEventListener('change', async function(){
    const file = this.files[0];
    if(!file) return;
    const fd = new FormData();
    fd.append('business_permit', file);
    try {
      const body = await api('{{ route('dormitory-profile.business-permit') }}', { method: 'POST', body: fd });
      $('businessPermitFileNameMeta').textContent = body.file_name;
      $('businessPermitFileTypeMeta').textContent = body.file_ext;
      $('businessPermitBtn').textContent = 'Update Document';
      $('businessPermitRemoveBtn').style.display = '';
      renderBusinessPermitPreview(body.file_name, body.image_url);
      toast('Business Permit uploaded.');
    } catch(e){ toast(e.message, true); }
    this.value = '';
  });
  $('businessPermitRemoveBtn').addEventListener('click', async function(){
    if(!confirm('Remove the uploaded Business Permit?')) return;
    try {
      await api('{{ route('dormitory-profile.business-permit') }}', { method: 'DELETE' });
      $('businessPermitFileNameMeta').textContent = '—';
      $('businessPermitFileTypeMeta').textContent = '—';
      $('businessPermitBtn').textContent = 'Upload Document';
      this.style.display = 'none';
      renderBusinessPermitPreview(null, null);
      toast('Business Permit removed.');
    } catch(e){ toast(e.message, true); }
  });

  // ===== BIR Registration =====
  function renderBirRegistrationPreview(fileName, imageUrl){
    const box = $('birRegistrationPreview');
    if(imageUrl){
      box.innerHTML = `<img src="${imageUrl}?t=${Date.now()}" alt="">`;
    } else if(fileName){
      box.innerHTML = '<div class="legit-doc-file-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg></div>';
    } else {
      box.innerHTML = '<div class="legit-doc-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg><span>No file uploaded</span></div>';
    }
  }
  $('birRegistrationBtn').addEventListener('click', () => $('birRegistrationInput').click());
  $('birRegistrationInput').addEventListener('change', async function(){
    const file = this.files[0];
    if(!file) return;
    const fd = new FormData();
    fd.append('bir_registration', file);
    try {
      const body = await api('{{ route('dormitory-profile.bir-registration') }}', { method: 'POST', body: fd });
      $('birRegistrationFileNameMeta').textContent = body.file_name;
      $('birRegistrationFileTypeMeta').textContent = body.file_ext;
      $('birRegistrationBtn').textContent = 'Update Document';
      $('birRegistrationRemoveBtn').style.display = '';
      renderBirRegistrationPreview(body.file_name, body.image_url);
      $('previewBadgeWrap').style.display = '';
      const pill = document.getElementById('previewBadgePill');
      pill.innerHTML = body.image_url
        ? `<img src="${body.image_url}?t=${Date.now()}" alt=""><span>Registered with the Bureau of Internal Revenue</span>`
        : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg><span>Registered with the Bureau of Internal Revenue</span>`;
      toast(body.message);
    } catch(e){ toast(e.message, true); }
    this.value = '';
  });
  $('birRegistrationRemoveBtn').addEventListener('click', async function(){
    if(!confirm('Remove the uploaded BIR Registration? The verification badge will no longer show on your public profile.')) return;
    try {
      await api('{{ route('dormitory-profile.bir-registration') }}', { method: 'DELETE' });
      $('birRegistrationFileNameMeta').textContent = '—';
      $('birRegistrationFileTypeMeta').textContent = '—';
      $('birRegistrationBtn').textContent = 'Upload Document';
      this.style.display = 'none';
      renderBirRegistrationPreview(null, null);
      $('previewBadgeWrap').style.display = 'none';
      toast('BIR Registration removed.');
    } catch(e){ toast(e.message, true); }
  });

  // ===== Amenities =====
  document.querySelectorAll('#amenitiesGrid input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', async function(){
      const id = this.dataset.id;
      const key = this.dataset.key;
      const isEnabled = this.checked;
      this.disabled = true;
      try {
        await api(`/dormitory-profile/amenities/${id}/toggle`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ is_enabled: isEnabled }),
        });

        const previewGrid = $('previewAmenities');
        const existing = previewGrid.querySelector(`[data-key="${key}"]`);
        if(isEnabled && !existing){
          const wrapper = this.closest('.amenity-check');
          const iconHtml = wrapper.querySelector('.amenity-icon').innerHTML;
          const div = document.createElement('div');
          div.className = 'preview-amenity-icon';
          div.dataset.key = key;
          div.title = wrapper.querySelector('.label-text').textContent;
          div.innerHTML = iconHtml;
          previewGrid.appendChild(div);
        } else if(!isEnabled && existing){
          existing.remove();
        }
      } catch(e){
        this.checked = !isEnabled;
        toast(e.message, true);
      }
      this.disabled = false;
    });
  });

  // ===== House Rules =====
  function renumberRules(){
    document.querySelectorAll('#rulesList .rule-row').forEach((row, i) => {
      row.querySelector('.rule-num').textContent = i + 1;
    });
  }

  function addRuleToPreview(text){
    const emptyMsg = document.querySelector('#previewRules .preview-empty');
    if(emptyMsg) emptyMsg.remove();
    const li = document.createElement('li');
    li.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg><span></span>';
    li.querySelector('span').textContent = text;
    $('previewRules').appendChild(li);
    return li;
  }

  function rebuildPreviewRules(){
    const list = $('previewRules');
    list.innerHTML = '';
    const rows = document.querySelectorAll('#rulesList .rule-row');
    if(rows.length === 0){
      list.innerHTML = '<li class="preview-empty">No house rules added yet.</li>';
      return;
    }
    rows.forEach(row => addRuleToPreview(row.querySelector('.rule-text span').textContent));
  }

  $('addRuleBtn').addEventListener('click', async function(){
    const input = $('newRuleInput');
    const text = input.value.trim();
    if(!text) return toast('Enter a rule first.', true);

    this.disabled = true;
    try {
      const body = await api('{{ route('dormitory-profile.house-rules.store') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ rule_text: text }),
      });

      document.getElementById('rulesEmptyMsg')?.remove();

      const li = document.createElement('li');
      li.className = 'rule-row';
      li.dataset.id = body.rule.id;
      li.innerHTML = `
        <span class="rule-num"></span>
        <div class="rule-text">
          <span></span>
          <input type="text" maxlength="500" aria-label="Edit house rule text">
        </div>
        <div class="rule-actions">
          <button type="button" class="icon-btn edit-rule-btn" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg></button>
          <button type="button" class="icon-btn danger delete-rule-btn" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m3 0l-1 14a2 2 0 01-2 2H7a2 2 0 01-2-2L4 6"/></svg></button>
        </div>`;
      li.querySelector('.rule-text span').textContent = text;
      li.querySelector('.rule-text input').value = text;
      $('rulesList').appendChild(li);
      wireRuleRow(li);
      renumberRules();
      addRuleToPreview(text);
      document.querySelector('#previewRules .preview-empty')?.remove();

      input.value = '';
      toast('Rule added.');
    } catch(e){ toast(e.message, true); }
    this.disabled = false;
  });

  function wireRuleRow(row){
    const editBtn = row.querySelector('.edit-rule-btn');
    const deleteBtn = row.querySelector('.delete-rule-btn');
    const span = row.querySelector('.rule-text span');
    const input = row.querySelector('.rule-text input');

    editBtn.addEventListener('click', async function(){
      if(row.classList.contains('editing')){
        // Save
        const newText = input.value.trim();
        if(!newText) return toast('Rule text cannot be empty.', true);
        try {
          await api(`/dormitory-profile/house-rules/${row.dataset.id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ rule_text: newText }),
          });
          span.textContent = newText;
          row.classList.remove('editing');
          rebuildPreviewRules();
          toast('Rule updated.');
        } catch(e){ toast(e.message, true); }
      } else {
        input.value = span.textContent;
        row.classList.add('editing');
        input.focus();
      }
    });

    deleteBtn.addEventListener('click', async function(){
      if(!confirm('Delete this house rule?')) return;
      try {
        await api(`/dormitory-profile/house-rules/${row.dataset.id}`, { method: 'DELETE' });
        row.remove();
        renumberRules();
        rebuildPreviewRules();
        if(document.querySelectorAll('#rulesList .rule-row').length === 0){
          $('rulesList').innerHTML = '<li class="rules-empty" id="rulesEmptyMsg">No house rules added yet.</li>';
        }
        toast('Rule deleted.');
      } catch(e){ toast(e.message, true); }
    });
  }
  document.querySelectorAll('#rulesList .rule-row').forEach(wireRuleRow);

  // ===== Sidebar collapse / search / logout (same pattern as every other admin page) =====
  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
  });

  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
      await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN } });
      window.location.href = '/';
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

  document.querySelectorAll('.sidebar [tabindex="0"], .hamburger, .topbar-icon, .back-arrow').forEach(el => {
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        el.click();
      }
    });
  });
})();
</script>

<script>
(function(){
  const card = document.getElementById('reviewModerationCard');
  if (!card) return;

  const CSRF = document.querySelector('meta[name="csrf-token"]').content;
  const LABELS = { published: 'Public', hidden: 'Hidden', removed: 'Removed' };
  let currentFilter = 'all';

  function notify(msg, isError) {
    const t = document.getElementById('toast');
    if (!t) return alert(msg);
    t.textContent = msg;
    t.classList.toggle('error', !!isError);
    t.classList.add('visible');
    clearTimeout(window._nestToastTimer);
    window._nestToastTimer = setTimeout(() => t.classList.remove('visible'), 3200);
  }

  async function send(url, method, payload) {
    const res = await fetch(url, {
      method,
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: payload ? JSON.stringify(payload) : null,
    });
    let body = {};
    try { body = await res.json(); } catch (e) {}
    if (!res.ok) throw new Error(body.message || 'Something went wrong.');
    return body;
  }

  function rows() { return Array.from(card.querySelectorAll('.rv-row')); }

  function applyFilters() {
    const q = document.getElementById('rvSearch').value.trim().toLowerCase();
    const rating = document.getElementById('rvRating').value;
    let shown = 0;
    rows().forEach(row => {
      const ok = (currentFilter === 'all' || row.dataset.status === currentFilter)
        && (!rating || row.dataset.rating === rating)
        && (!q || row.dataset.search.includes(q));
      row.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });
    const empty = document.getElementById('rvEmpty');
    empty.textContent = rows().length ? 'No reviews match this view.' : 'No reviews yet.';
    empty.style.display = shown ? 'none' : 'block';
  }

  function recount() {
    const counts = { all: 0, published: 0, hidden: 0, removed: 0 };
    rows().forEach(r => { counts.all++; counts[r.dataset.status]++; });
    Object.keys(counts).forEach(k => {
      const el = card.querySelector(`[data-count="${k}"]`);
      if (el) el.textContent = counts[k];
    });
    card.querySelector('.rv-tab[data-filter="hidden"]').classList.toggle('needs-review', counts.hidden > 0);
  }

  // After an action, keep keyboard focus inside the list instead of losing it.
  function refocus(row) {
    const isVisible = (el) => el.offsetParent !== null;
    let target = row;
    while (target && !isVisible(target)) target = target.nextElementSibling;
    if (!target) target = rows().reverse().find(isVisible);
    const btn = target && Array.from(target.querySelectorAll('.rv-actions button')).find(isVisible);
    (btn || document.getElementById('rvEmpty')).focus();
  }

  function updateRow(row, body) {
    row.dataset.status = body.review.status;
    row.querySelector('.rv-status').textContent = LABELS[body.review.status];
    row.querySelector('.rv-meta').textContent = body.review.summary;
    document.getElementById('rvAverage').textContent = Number(body.aggregate.average).toFixed(1);
    document.getElementById('rvPublicCount').textContent = body.aggregate.count;
    document.getElementById('rvPublicNoun').textContent = Number(body.aggregate.count) === 1 ? 'review' : 'reviews';
    recount();
    applyFilters();
    refocus(row);
  }

  card.querySelectorAll('.rv-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      card.querySelectorAll('.rv-tab').forEach(t => {
        t.classList.remove('active');
        t.setAttribute('aria-pressed', 'false');
      });
      tab.classList.add('active');
      tab.setAttribute('aria-pressed', 'true');
      currentFilter = tab.dataset.filter;
      applyFilters();
    });
  });
  document.getElementById('rvSearch').addEventListener('input', applyFilters);
  document.getElementById('rvRating').addEventListener('change', applyFilters);

  card.addEventListener('click', async (e) => {
    const btn = e.target.closest('.rv-actions button');
    if (!btn) return;
    const row = btn.closest('.rv-row');
    let action;
    let payload = null;

    if (btn.classList.contains('act-publish') || btn.classList.contains('act-restore')) {
      action = 'publish';
    } else if (btn.classList.contains('act-hide')) {
      if (!confirm('Hide this review from the public Dorm Info page?')) return;
      action = 'hide';
    } else if (btn.classList.contains('act-remove')) {
      const note = prompt('Remove this review? It will never show publicly, and the tenant cannot submit another one.\n\nReason (optional):');
      if (note === null) return;
      action = 'remove';
      payload = { note: note.trim() };
    } else {
      return;
    }

    btn.disabled = true;
    try {
      const body = await send(`/dormitory-profile/reviews/${row.dataset.id}/${action}`, 'PATCH', payload);
      updateRow(row, body);
      notify(body.message);
    } catch (err) {
      notify(err.message, true);
    }
    btn.disabled = false;
  });

  document.getElementById('rvRescanBtn').addEventListener('click', async function () {
    if (!confirm('Check all reviews again against the word list and spam rules? Reviews you already decided on are skipped.')) return;
    this.disabled = true;
    try {
      const body = await send('{{ route('dormitory-profile.reviews.rescan') }}', 'POST');
      notify(body.message);
      setTimeout(() => location.reload(), 1200);
    } catch (err) {
      notify(err.message, true);
      this.disabled = false;
    }
  });

  applyFilters();
})();
</script>

</body>
</html>
