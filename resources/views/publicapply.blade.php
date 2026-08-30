<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Apply for Occupancy | NEST.PH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Agbalumo&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        .page-wrap { overflow-x: hidden; }

        body {
            font-family: 'Roboto', system-ui, -apple-system, sans-serif;
            color: #292420;
            background: linear-gradient(180deg, #567357 0%, #59473f 100%);
            min-height: 100vh;
        }

        .textured { position: relative; overflow: hidden; }
        .textured .bg-texture {
            position: absolute; inset: 0; width: 100%; height: 100%;
            object-fit: cover; pointer-events: none; z-index: 0;
            mix-blend-mode: multiply; opacity: 0.5;
        }
        .textured > *:not(.bg-texture) { position: relative; z-index: 1; }

        .topnav {
            background: linear-gradient(90deg, #567357, #a2d9a4);
            padding: 14px clamp(20px, 5vw, 64px);
            display: flex; align-items: center; gap: clamp(16px, 3vw, 40px);
            position: sticky; top: 0; z-index: 1000;
            transition: box-shadow 0.25s ease;
        }
        .topnav.scrolled { box-shadow: 0 4px 14px rgba(0,0,0,0.2); }
        .topnav .menu { flex: 1; display: flex; align-items: center; gap: 10px; }
        .topnav .menu a, .topnav .menu span {
            color: #fff; font-weight: 500; font-size: 14px;
            padding: 10px 6px; display: inline-flex; align-items: center; gap: 4px;
            text-decoration: none;
        }
        .topnav .menu a.pill { border: 1px solid rgba(255,255,255,0.5); border-radius: 999px; padding: 7px 16px; }
        .topnav .menu a.pill:hover { background: rgba(255,255,255,0.12); }
        .topnav .logo {
            display: flex; align-items: center; gap: 6px; color: #fff; font-weight: 700;
            font-size: 19px; letter-spacing: 0.02em; white-space: nowrap; text-decoration: none;
        }
        .topnav .logo .mark { width: 18px; height: 18px; border: 2px solid #fff; border-radius: 4px; flex-shrink: 0; }
        .topnav .buttons { flex: 1; display: flex; justify-content: flex-end; gap: 12px; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 40px; padding: 0 18px; border: 2px solid #fff;
            font-weight: 500; font-size: 13.5px; letter-spacing: 0.02em; cursor: pointer;
            white-space: nowrap; text-decoration: none;
        }
        .btn-white { background: #fff; color: #292420; }
        .btn-outline-white { background: transparent; color: #fff; }

        /* ===== APPLY WIZARD — same skeleton as the login pages ===== */
        .apply-grid {
            display: grid;
            grid-template-columns: 33% 67%;
            align-items: stretch;
            min-height: calc(100vh - 70px);
            padding: clamp(16px, 3vw, 20px) clamp(24px, 5vw, 48px) clamp(24px, 5vw, 48px) 0;
        }

        .apply-left {
            position: relative;
            padding: 16px clamp(20px, 4vw, 40px) clamp(24px, 4vw, 40px) clamp(28px, 6vw, 64px);
            display: flex; flex-direction: column;
        }
        .back-button {
            background: none; border: none; color: #fff; font-size: 22px; cursor: pointer;
            line-height: 1; padding: 0; margin-bottom: 32px; align-self: flex-start;
        }
        .apply-left-content { flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .apply-left h1 {
            color: #fff; font-weight: 700; font-size: clamp(22px, 2.6vw, 30px);
            line-height: 1.2; max-width: 320px;
        }
        .apply-left h1 .accent {
            display: block; color: #44ad65; font-weight: 900;
            font-size: clamp(26px, 3.2vw, 36px); margin-top: 4px;
        }
        .brand-mark {
            margin-top: 32px; width: 130px; height: 130px;
            background: linear-gradient(180deg, #567357 0%, #a2d9a4 100%);
            border-radius: 0 70px 70px 0; display: flex; align-items: center; justify-content: center;
        }
        .brand-mark span { font-family: 'Agbalumo', cursive; font-size: 84px; color: #fff; line-height: 1; }

        .apply-right-wrap { position: relative; padding-top: 16px; display: flex; flex-direction: column; }
        .apply-right {
            background: #eeeded; border-radius: 28px;
            padding: clamp(28px, 4vw, 44px) clamp(24px, 4vw, 48px) clamp(32px, 4vw, 44px);
            flex: 1;
        }

        .apply-title {
            color: #44ad65; font-weight: 900; font-size: clamp(17px, 1.8vw, 21px);
            letter-spacing: 0.02em; text-transform: uppercase; margin-bottom: 20px;
        }

        /* Step indicator */
        .step-track {
            display: flex; align-items: flex-start; justify-content: space-between;
            max-width: 460px; margin: 0 auto 22px; position: relative;
        }
        .step-track::before {
            content: ''; position: absolute; top: 8px; left: 30px; right: 30px;
            height: 2px; background: #44ad65; z-index: 0;
        }
        .step-dot-wrap { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; gap: 7px; flex: 1; }
        .step-dot { width: 17px; height: 17px; border-radius: 50%; background: #44ad65; border: 2px solid #44ad65; }
        .step-dot.current { background: #fff; }
        .step-label { font-size: 9.5px; font-weight: 700; color: #194e19; text-transform: uppercase; letter-spacing: 0.02em; text-align: center; }
        .step-divider { height: 1px; background: #d8dde3; margin: 0 0 22px; }

        /* Steps */
        .step-card { display: none; }
        .step-card.active { display: block; }
        .step-card h3 {
            color: #194e19; font-weight: 700; font-size: 16px;
            text-transform: uppercase; letter-spacing: 0.02em; margin-bottom: 18px;
        }
        .step-card h3.spaced { margin-top: 28px; }

        .field-row { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 18px; }
        .field { flex: 1; min-width: 180px; display: flex; flex-direction: column; gap: 8px; }
        .field.full { flex-basis: 100%; }
        .field label {
            font-size: 12px; font-weight: 500; color: #262e36; letter-spacing: -0.01em;
            display: flex; align-items: center; gap: 3px;
        }
        .field label .req { color: #d95117; }
        .field input[type="text"],
        .field input[type="email"],
        .field input[type="tel"],
        .field input[type="date"],
        .field select,
        .field textarea {
            background: #fff; border: 1px solid #d8dde3; border-radius: 8px;
            padding: 12px 14px; font-size: 13.5px; color: #4c5c6b;
            font-family: inherit; width: 100%; box-shadow: 0 0 2px rgba(23,25,28,0.05);
        }
        .field textarea { resize: vertical; min-height: 70px; }
        .field input:focus, .field select:focus, .field textarea:focus {
            outline: none; border-color: #567357;
        }

        .radio-group {
            background: #fff; border: 1px solid #d8dde3; border-radius: 8px;
            padding: 16px; display: flex; flex-direction: column; gap: 12px;
        }
        .radio-option { display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #4c5c6b; }
        .radio-option input { accent-color: #567357; width: 16px; height: 16px; }

        .file-drop {
            background: #fff; border: 1px solid #d8dde3; border-radius: 8px;
            padding: 16px; min-height: 90px; display: flex; flex-direction: column;
            justify-content: center; gap: 8px; cursor: pointer; position: relative;
        }
        .file-drop input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .file-drop .placeholder { font-size: 13px; color: #9aa5ac; }
        .file-drop .filename { font-size: 13px; color: #194e19; font-weight: 500; word-break: break-all; }
        .file-drop-icons { display: flex; gap: 10px; color: #9aa5ac; }
        .file-drop-icons svg { width: 16px; height: 16px; }

        .contract-review-card {
            background: #fff; border: 1px solid #d8dde3; border-radius: 10px; padding: 18px 20px;
        }
        .contract-review-head { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 14px; }
        .contract-review-head svg { width: 26px; height: 26px; color: #567357; flex-shrink: 0; margin-top: 2px; }
        .crc-title { font-size: 14px; font-weight: 700; color: #194e19; }
        .crc-sub { font-size: 12px; color: #7a8a7c; margin-top: 3px; line-height: 1.5; }
        .contract-review-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .crc-btn {
            display: inline-flex; align-items: center; background: #567357; color: #fff; font-weight: 700;
            font-size: 12.5px; padding: 10px 20px; border-radius: 7px; text-decoration: none;
        }
        .crc-btn.secondary { background: #fff; color: #567357; border: 1px solid #a6b69f; }
        .crc-unavailable {
            background: #fdf0f0; border: 1px solid #f3cccc; color: #b3261e; border-radius: 8px;
            padding: 11px 14px; font-size: 12.5px; line-height: 1.6; margin-bottom: 16px;
        }
        .crc-checkbox {
            display: flex; gap: 9px; align-items: flex-start; font-size: 12.5px; color: #4b5f4c;
            line-height: 1.6; padding-top: 14px; border-top: 1px solid #eef1ee;
        }
        .crc-checkbox input { margin-top: 2px; accent-color: #567357; width: 15px; height: 15px; flex-shrink: 0; }

        .step-actions { display: flex; justify-content: space-between; gap: 16px; margin-top: 28px; }
        .btn-nav {
            display: inline-flex; align-items: center; justify-content: center;
            border: none; border-radius: 6px; padding: 14px 32px; font-weight: 700;
            font-size: 14px; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;
            text-decoration: none;
        }
        .btn-nav.primary { background: #345234; color: #fff; }
        .btn-nav.primary:hover { background: #26401f; }
        .btn-nav.secondary { background: transparent; color: #567357; border: 1px solid #a6b69f; }
        .btn-nav.secondary:hover { background: #e4e9e3; }
        .btn-nav:disabled { opacity: 0.6; cursor: not-allowed; }

        .spinner {
            display: none; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid #fff; border-radius: 50%; animation: spin 0.8s linear infinite; margin-right: 8px;
        }
        .btn-nav.loading .spinner { display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .form-error {
            display: none; background: #fdf0f0; border: 1px solid #f3cccc; color: #b3261e;
            border-radius: 8px; padding: 12px 14px; font-size: 13px; margin-bottom: 18px;
        }
        .form-error.visible { display: block; }

        /* Step 4 — verify summary (redesigned: structured cards, not a text block) */
        .verify-card {
            background: #fff; border: 1px solid #e2e6e3; border-radius: 12px;
            padding: 24px 28px; margin-bottom: 18px;
        }
        .summary-section { margin-bottom: 26px; }
        .summary-section:last-child { margin-bottom: 0; }
        .summary-section h4 {
            color: #194e19; font-weight: 700; font-size: 12.5px; text-transform: uppercase;
            letter-spacing: 0.04em; margin-bottom: 14px; padding-bottom: 8px;
            border-bottom: 2px solid #e2ede3;
        }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px 24px; }
        .summary-item { display: flex; flex-direction: column; gap: 4px; }
        .summary-item .label {
            font-size: 10px; color: #9aa5ac; text-transform: uppercase;
            letter-spacing: 0.04em; font-weight: 700;
        }
        .summary-item .value { font-size: 13.5px; color: #292420; font-weight: 500; word-break: break-word; }
        .summary-item .value.empty { color: #c2c9c5; font-style: italic; font-weight: 400; }
        .consent-row {
            display: flex; gap: 10px; align-items: flex-start; font-size: 12.5px;
            color: #4b5f4c; line-height: 1.6; margin-bottom: 20px;
        }
        .consent-row input { margin-top: 3px; accent-color: #567357; width: 16px; height: 16px; flex-shrink: 0; }

        /* Step 5 — success */
        #step-success { text-align: center; padding: 40px 0; }
        .success-badge { width: 72px; height: 72px; margin: 0 auto 20px; display: block; }
        #step-success h2 { color: #567357; font-weight: 900; font-size: clamp(22px, 2.6vw, 30px); margin-bottom: 14px; }
        #step-success p { color: #8a9690; font-size: 14px; line-height: 1.6; max-width: 480px; margin: 0 auto 26px; }

        @media (max-width: 1024px) {
            .apply-grid { grid-template-columns: 1fr; padding: 20px 24px 32px; }
            .apply-left { padding: 0 0 24px; }
            .apply-right { padding: 28px 24px 32px; }
            .topnav { padding: 14px 24px; flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .apply-left h1 { font-size: 22px; }
            .apply-left h1 .accent { font-size: 26px; }
            .brand-mark { width: 100px; height: 100px; }
            .brand-mark span { font-size: 64px; }
            .field-row { flex-direction: column; }
            .step-label { display: none; }
            .step-actions { flex-direction: column-reverse; }
        }
    </style>
</head>
<body>

    <nav class="topnav textured">
        <img src="{{ asset('images/leaf-texture-2.png') }}" class="bg-texture" alt="">
        <div class="menu">
            <a href="{{ route('public.vr') }}">VR TOUR</a>
            <a href="{{ route('public.rooms') }}">ROOMS</a>
            <a href="{{ route('home') }}">HOME</a>
            <a href="{{ route('public.dorminfo') }}" class="pill">Dorm Info</a>
        </div>
        <div class="logo"><span class="mark"></span> NEST.PH</div>
        <div class="buttons">
            <a href="{{ route('login.admin') }}" class="btn btn-white">Admin</a>
            <a href="{{ route('public.apply') }}" class="btn btn-outline-white">Apply</a>
            <a href="{{ route('login.tenant') }}" class="btn btn-white">Log In</a>
        </div>
    </nav>

    <div class="page-wrap">
    <div class="apply-grid">
        <div class="apply-left">
            <button class="back-button" type="button" aria-label="Go back" onclick="window.location.href='{{ route('home') }}'">←</button>
            <div class="apply-left-content">
                <h1>Study hard, make friends, and live your<span class="accent">NEST life.</span></h1>
                <div class="brand-mark"><span>N</span></div>
            </div>
        </div>

        <div class="apply-right-wrap">
            <div class="apply-right">
                <div class="apply-title">Apply for Occupancy</div>

                <div class="step-track" id="stepTrack">
                    <div class="step-dot-wrap"><div class="step-dot current" data-dot="1"></div><span class="step-label">Personal<br>Information</span></div>
                    <div class="step-dot-wrap"><div class="step-dot" data-dot="2"></div><span class="step-label">Contact<br>Information</span></div>
                    <div class="step-dot-wrap"><div class="step-dot" data-dot="3"></div><span class="step-label">Room<br>Information</span></div>
                </div>
                <div class="step-divider"></div>

                <div class="form-error" id="formError"></div>

                {{-- STEP 1 — Personal Information --}}
                <div class="step-card active" id="step-1" data-step="1">
                    <h3>Personal Information</h3>
                    <div class="field-row">
                        <div class="field full">
                            <label>Full Name <span class="req">*</span></label>
                            <input type="text" id="full_name" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Birthdate <span class="req">*</span></label>
                            <input type="date" id="birthdate" required>
                        </div>
                        <div class="field">
                            <label>Gender</label>
                            <select id="gender">
                                <option value="">Select</option>
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                                <option value="prefer_not_to_say">Prefer not to say</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Nationality</label>
                            <input type="text" id="nationality" placeholder="Filipino">
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field full">
                            <label>Medical Condition <span class="req">*</span></label>
                            <input type="text" id="medical_condition" placeholder="None, if not applicable" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field full">
                            <label>Occupation <span class="req">*</span></label>
                            <input type="text" id="occupation" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field full">
                            <label>School/Company <span class="req">*</span></label>
                            <input type="text" id="school_company" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field full">
                            <label>School/Company Address <span class="req">*</span></label>
                            <input type="text" id="school_company_address" required>
                        </div>
                    </div>

                    <div class="step-actions">
                        <span></span>
                        <button type="button" class="btn-nav primary" onclick="goNext(1)">NEXT</button>
                    </div>
                </div>

                {{-- STEP 2 — Contact + Emergency Contact Information --}}
                <div class="step-card" id="step-2" data-step="2">
                    <h3>Contact Information</h3>
                    <div class="field-row">
                        <div class="field">
                            <label>Cellphone No. <span class="req">*</span></label>
                            <input type="tel" id="contact_number" placeholder="09-" required>
                        </div>
                        <div class="field">
                            <label>Email</label>
                            <input type="email" id="email">
                        </div>
                        <div class="field">
                            <label>Landline</label>
                            <input type="text" id="landline">
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field full">
                            <label>Home Address <span class="req">*</span></label>
                            <input type="text" id="home_address" required>
                        </div>
                    </div>

                    <h3 class="spaced">Emergency Contact Information</h3>
                    <div class="field-row">
                        <div class="field full">
                            <label>Fullname <span class="req">*</span></label>
                            <input type="text" id="emergency_contact_name" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Cellphone No. <span class="req">*</span></label>
                            <input type="tel" id="emergency_contact_number" placeholder="09-" required>
                        </div>
                        <div class="field">
                            <label>Email</label>
                            <input type="email" id="emergency_contact_email">
                        </div>
                        <div class="field">
                            <label>Landline</label>
                            <input type="text" id="emergency_contact_landline">
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Father's Name <span class="req">*</span></label>
                            <input type="text" id="father_name" required>
                        </div>
                        <div class="field">
                            <label>Mother's Name <span class="req">*</span></label>
                            <input type="text" id="mother_name" required>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn-nav secondary" onclick="goBack(2)">BACK</button>
                        <button type="button" class="btn-nav primary" onclick="goNext(2)">NEXT</button>
                    </div>
                </div>

                {{-- STEP 3 — Room Information --}}
                <div class="step-card" id="step-3" data-step="3">
                    <h3>Room Information</h3>
                    <div class="field-row">
                        <div class="field">
                            <label>Preferred Start Date <span class="req">*</span></label>
                            <input type="date" id="preferred_start_date" required>
                        </div>
                        <div class="field">
                            <label>Room No <span class="req">*</span></label>
                            <select id="room_select" required>
                                <option value="">Loading rooms…</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Tenant End Date <span class="req">*</span></label>
                            <input type="date" id="tenant_end_date" required>
                        </div>
                        <div class="field">
                            <label>Bed No <span class="req">*</span></label>
                            <select id="bed_select" required>
                                <option value="">Select a room first</option>
                            </select>
                        </div>
                    </div>

                    <div class="field-row">
                        <div class="field full">
                            <label>Type of Tenant <span class="req">*</span></label>
                            <div class="radio-group">
                                <label class="radio-option"><input type="radio" name="type_of_tenant" value="student" required> Student</label>
                                <label class="radio-option"><input type="radio" name="type_of_tenant" value="working_student" required> Working Student</label>
                                <label class="radio-option"><input type="radio" name="type_of_tenant" value="full_time_employee" required> Full-time Employee</label>
                                <label class="radio-option"><input type="radio" name="type_of_tenant" value="part_time_employee" required> Part-time Employee</label>
                            </div>
                        </div>
                    </div>

                    <div class="field-row">
                        <div class="field full">
                            <div class="contract-review-card">
                                <div class="contract-review-head">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                                    <div>
                                        <div class="crc-title">Review the Dormitory Contract</div>
                                        <div class="crc-sub">Read the full terms before signing. Download a copy to print, sign, and scan.</div>
                                    </div>
                                </div>

                                @if($hasContractTemplate)
                                    <div class="contract-review-actions">
                                        <a href="{{ route('public.apply.contract') }}" target="_blank" rel="noopener" class="crc-btn">View Contract</a>
                                        <a href="{{ route('public.apply.contract.download') }}" class="crc-btn secondary">Download Contract</a>
                                    </div>
                                @else
                                    <div class="crc-unavailable">The contract document hasn't been uploaded yet. Please check back soon, or contact the dormitory directly before applying.</div>
                                @endif

                                <label class="crc-checkbox">
                                    <input type="checkbox" id="contract_acceptance" required>
                                    <span>I have reviewed and downloaded a copy of the dormitory contract.</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label>ID <span class="req">*</span></label>
                            <div class="file-drop" id="idDrop">
                                <input type="file" id="id_document" accept=".jpg,.jpeg,.png,.pdf" required>
                                <span class="placeholder" id="idPlaceholder">Add file (JPG, PNG, or PDF)</span>
                            </div>
                        </div>
                        <div class="field">
                            <label>Signed Contract <span class="req">*</span></label>
                            <div class="file-drop" id="contractDrop">
                                <input type="file" id="signed_contract" accept=".jpg,.jpeg,.png,.pdf" required>
                                <span class="placeholder" id="contractPlaceholder">Upload the contract you just signed</span>
                            </div>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn-nav secondary" onclick="goBack(3)">BACK</button>
                        <button type="button" class="btn-nav primary" onclick="goNext(3)">NEXT</button>
                    </div>
                </div>

                {{-- STEP 4 — Verify Your Information --}}
                <div class="step-card" id="step-4" data-step="4">
                    <h3>Verify Your Information</h3>

                    <div class="verify-card">
                        <div class="summary-section">
                            <h4>Personal Information</h4>
                            <div class="summary-grid">
                                <div class="summary-item"><span class="label">Full Name</span><span class="value" id="sum_full_name"></span></div>
                                <div class="summary-item"><span class="label">Birthdate</span><span class="value" id="sum_birthdate"></span></div>
                                <div class="summary-item"><span class="label">Gender</span><span class="value" id="sum_gender"></span></div>
                                <div class="summary-item"><span class="label">Nationality</span><span class="value" id="sum_nationality"></span></div>
                                <div class="summary-item"><span class="label">Medical Condition</span><span class="value" id="sum_medical_condition"></span></div>
                                <div class="summary-item"><span class="label">Occupation</span><span class="value" id="sum_occupation"></span></div>
                                <div class="summary-item"><span class="label">School/Company</span><span class="value" id="sum_school_company"></span></div>
                                <div class="summary-item"><span class="label">School/Company Address</span><span class="value" id="sum_school_company_address"></span></div>
                            </div>
                        </div>

                        <div class="summary-section">
                            <h4>Contact Information</h4>
                            <div class="summary-grid">
                                <div class="summary-item"><span class="label">Cellphone No.</span><span class="value" id="sum_contact_number"></span></div>
                                <div class="summary-item"><span class="label">Email</span><span class="value" id="sum_email"></span></div>
                                <div class="summary-item"><span class="label">Landline</span><span class="value" id="sum_landline"></span></div>
                                <div class="summary-item"><span class="label">Home Address</span><span class="value" id="sum_home_address"></span></div>
                            </div>
                        </div>

                        <div class="summary-section">
                            <h4>Emergency Contact Information</h4>
                            <div class="summary-grid">
                                <div class="summary-item"><span class="label">Full Name</span><span class="value" id="sum_emergency_contact_name"></span></div>
                                <div class="summary-item"><span class="label">Cellphone No.</span><span class="value" id="sum_emergency_contact_number"></span></div>
                                <div class="summary-item"><span class="label">Email</span><span class="value" id="sum_emergency_contact_email"></span></div>
                                <div class="summary-item"><span class="label">Landline</span><span class="value" id="sum_emergency_contact_landline"></span></div>
                                <div class="summary-item"><span class="label">Father's Name</span><span class="value" id="sum_father_name"></span></div>
                                <div class="summary-item"><span class="label">Mother's Name</span><span class="value" id="sum_mother_name"></span></div>
                            </div>
                        </div>

                        <div class="summary-section">
                            <h4>Room Information</h4>
                            <div class="summary-grid">
                                <div class="summary-item"><span class="label">Preferred Start Date</span><span class="value" id="sum_start_date"></span></div>
                                <div class="summary-item"><span class="label">Tenant End Date</span><span class="value" id="sum_end_date"></span></div>
                                <div class="summary-item"><span class="label">Room</span><span class="value" id="sum_room"></span></div>
                                <div class="summary-item"><span class="label">Bed</span><span class="value" id="sum_bed"></span></div>
                                <div class="summary-item"><span class="label">Type of Tenant</span><span class="value" id="sum_tenant_type"></span></div>
                                <div class="summary-item"><span class="label">ID File</span><span class="value" id="sum_id_file"></span></div>
                                <div class="summary-item"><span class="label">Signed Contract File</span><span class="value" id="sum_contract_file"></span></div>
                            </div>
                        </div>
                    </div>

                    <label class="consent-row">
                        <input type="checkbox" id="dpa_consent" required>
                        <span>By approving, I verify the accuracy of the provided information. I am aware of the rental rates, payment schedule, and associated penalties for late payment. I also understand and agree to abide by all dormitory rules and regulations.</span>
                    </label>

                    <div class="step-actions">
                        <button type="button" class="btn-nav secondary" onclick="goBack(4)">BACK</button>
                        <button type="button" class="btn-nav primary" id="registerBtn" onclick="submitApplication()">
                            <span class="spinner"></span>
                            APPROVE &amp; REGISTER
                        </button>
                    </div>
                </div>

                {{-- STEP 5 — Success --}}
                <div class="step-card" id="step-5" data-step="5">
                    <svg class="success-badge" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 2 L37.5 7.5 L45 5 L47.5 12.5 L55 15 L52.5 22.5 L58 28 L52.5 33.5 L55 41 L47.5 43.5 L45 51 L37.5 48.5 L32 54 L26.5 48.5 L19 51 L16.5 43.5 L9 41 L11.5 33.5 L6 28 L11.5 22.5 L9 15 L16.5 12.5 L19 5 L26.5 7.5 Z" fill="#5ea86a"/>
                        <path d="M21 32 L28 39 L43 24" stroke="#fff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                    <h2>Application Successful</h2>
                    <p id="successAppNumber" style="display:none; font-weight:700; color:#194e19; margin-bottom:6px;"></p>
                    <p>Please wait for the administrator's review and approval. Kindly check your email inbox regularly for updates regarding your application status.</p>
                    <a href="{{ route('home') }}" class="btn-nav primary" style="display:inline-flex;">BACK TO HOME</a>
                </div>

            </div>
        </div>
    </div>
    </div>

<script>
    const STEP_LABELS = {
        1: 'PERSONAL INFORMATION',
        2: 'CONTACT INFORMATION',
        3: 'ROOM INFORMATION',
    };

    function showFormError(message) {
        const box = document.getElementById('formError');
        box.textContent = message;
        box.classList.add('visible');
    }

    function clearFormError() {
        document.getElementById('formError').classList.remove('visible');
    }

    function validateStep(stepEl) {
        const fields = stepEl.querySelectorAll('[required]');
        for (const field of fields) {
            if (field.type === 'radio') {
                const group = stepEl.querySelectorAll(`[name="${field.name}"]`);
                const checked = Array.from(group).some(r => r.checked);
                if (!checked) {
                    showFormError('Please select a type of tenant before continuing.');
                    return false;
                }
                continue;
            }
            if (!field.reportValidity()) {
                return false;
            }
        }
        return true;
    }

    function setActiveStep(step) {
        document.querySelectorAll('.step-card').forEach(c => c.classList.remove('active'));
        document.getElementById('step-' + step).classList.add('active');

        // Update the 3-dot tracker for steps 1–3; steps 4/5 leave it on the last state.
        const trackStep = Math.min(step, 3);
        document.querySelectorAll('.step-dot').forEach(dot => {
            const n = parseInt(dot.dataset.dot, 10);
            dot.classList.toggle('current', n === trackStep && step <= 3);
        });

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function goNext(currentStep) {
        clearFormError();
        const stepEl = document.getElementById('step-' + currentStep);
        if (!validateStep(stepEl)) return;

        if (currentStep === 3) {
            buildVerifySummary();
        }

        setActiveStep(currentStep + 1);
    }

    function goBack(currentStep) {
        clearFormError();
        setActiveStep(currentStep - 1);
    }

    function val(id) {
        const el = document.getElementById(id);
        return el ? el.value : '';
    }

    function checkedRadioValue(name) {
        const el = document.querySelector(`input[name="${name}"]:checked`);
        return el ? el.value : '';
    }

    function formatDate(value) {
        if (!value) return '';
        const d = new Date(value + 'T00:00:00');
        if (isNaN(d.getTime())) return value;
        return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function capitalize(value) {
        if (!value) return '';
        return value.charAt(0).toUpperCase() + value.slice(1).replace(/_/g, ' ');
    }

    function setSummary(id, value) {
        const el = document.getElementById(id);
        if (!el) return;
        if (value && String(value).trim()) {
            el.textContent = value;
            el.classList.remove('empty');
        } else {
            el.textContent = '—';
            el.classList.add('empty');
        }
    }

    function buildVerifySummary() {
        const roomText = document.getElementById('room_select').selectedOptions[0]?.textContent.replace(/\s*\(.*\)/, '') || '';
        const bedText = document.getElementById('bed_select').selectedOptions[0]?.textContent || '';
        const tenantTypeLabel = {
            student: 'Student',
            working_student: 'Working Student',
            full_time_employee: 'Full-time Employee',
            part_time_employee: 'Part-time Employee',
        }[checkedRadioValue('type_of_tenant')] || '';

        setSummary('sum_full_name', val('full_name'));
        setSummary('sum_birthdate', formatDate(val('birthdate')));
        setSummary('sum_gender', capitalize(val('gender')));
        setSummary('sum_nationality', val('nationality'));
        setSummary('sum_medical_condition', val('medical_condition'));
        setSummary('sum_occupation', val('occupation'));
        setSummary('sum_school_company', val('school_company'));
        setSummary('sum_school_company_address', val('school_company_address'));

        setSummary('sum_contact_number', val('contact_number'));
        setSummary('sum_email', val('email'));
        setSummary('sum_landline', val('landline'));
        setSummary('sum_home_address', val('home_address'));

        setSummary('sum_emergency_contact_name', val('emergency_contact_name'));
        setSummary('sum_emergency_contact_number', val('emergency_contact_number'));
        setSummary('sum_emergency_contact_email', val('emergency_contact_email'));
        setSummary('sum_emergency_contact_landline', val('emergency_contact_landline'));
        setSummary('sum_father_name', val('father_name'));
        setSummary('sum_mother_name', val('mother_name'));

        setSummary('sum_start_date', formatDate(val('preferred_start_date')));
        setSummary('sum_end_date', formatDate(val('tenant_end_date')));
        setSummary('sum_room', roomText);
        setSummary('sum_bed', bedText);
        setSummary('sum_tenant_type', tenantTypeLabel);
        setSummary('sum_id_file', document.getElementById('id_document').files[0]?.name);
        setSummary('sum_contract_file', document.getElementById('signed_contract').files[0]?.name);
    }

    // ===== File drop labels =====
    document.getElementById('id_document').addEventListener('change', function () {
        document.getElementById('idPlaceholder').textContent = this.files[0]?.name || 'Add file (JPG, PNG, or PDF)';
    });
    document.getElementById('signed_contract').addEventListener('change', function () {
        document.getElementById('contractPlaceholder').textContent = this.files[0]?.name || 'Add file (JPG, PNG, or PDF)';
    });

    // ===== Room -> Bed cascading dropdowns =====
    const roomSelect = document.getElementById('room_select');
    const bedSelect = document.getElementById('bed_select');

    fetch('/public-api/rooms')
        .then(r => r.json())
        .then(rooms => {
            const vacant = rooms.filter(r => r.available_beds > 0);
            roomSelect.innerHTML = '<option value="">Select a room</option>';
            vacant.forEach(room => {
                const opt = document.createElement('option');
                opt.value = room.id;
                opt.textContent = `${room.room_no} (${room.available_beds} vacant)`;
                roomSelect.appendChild(opt);
            });
            if (vacant.length === 0) {
                roomSelect.innerHTML = '<option value="">No vacant rooms right now</option>';
            }
        })
        .catch(() => {
            roomSelect.innerHTML = '<option value="">Could not load rooms</option>';
        });

    roomSelect.addEventListener('change', function () {
        bedSelect.innerHTML = '<option value="">Loading beds…</option>';
        if (!this.value) {
            bedSelect.innerHTML = '<option value="">Select a room first</option>';
            return;
        }
        fetch(`/public-api/rooms/${this.value}/beds`)
            .then(r => r.json())
            .then(beds => {
                bedSelect.innerHTML = '<option value="">Select a bed</option>';
                beds.forEach(bed => {
                    const opt = document.createElement('option');
                    opt.value = bed.id;
                    opt.textContent = bed.bed_label;
                    bedSelect.appendChild(opt);
                });
                if (beds.length === 0) {
                    bedSelect.innerHTML = '<option value="">No vacant beds in this room</option>';
                }
            })
            .catch(() => {
                bedSelect.innerHTML = '<option value="">Could not load beds</option>';
            });
    });

    // ===== Final submission =====
    function submitApplication() {
        clearFormError();
        const step4 = document.getElementById('step-4');
        if (!validateStep(step4)) return;

        const button = document.getElementById('registerBtn');
        button.disabled = true;
        button.classList.add('loading');

        const formData = new FormData();
        formData.append('full_name', val('full_name'));
        formData.append('birthdate', val('birthdate'));
        formData.append('gender', val('gender'));
        formData.append('nationality', val('nationality'));
        formData.append('medical_condition', val('medical_condition'));
        formData.append('occupation', val('occupation'));
        formData.append('school_company', val('school_company'));
        formData.append('school_company_address', val('school_company_address'));

        formData.append('contact_number', val('contact_number'));
        formData.append('email', val('email'));
        formData.append('landline', val('landline'));
        formData.append('home_address', val('home_address'));

        formData.append('emergency_contact_name', val('emergency_contact_name'));
        formData.append('emergency_contact_number', val('emergency_contact_number'));
        formData.append('emergency_contact_email', val('emergency_contact_email'));
        formData.append('emergency_contact_landline', val('emergency_contact_landline'));
        formData.append('father_name', val('father_name'));
        formData.append('mother_name', val('mother_name'));

        formData.append('bed_id', bedSelect.value);
        formData.append('preferred_start_date', val('preferred_start_date'));
        formData.append('tenant_end_date', val('tenant_end_date'));
        formData.append('type_of_tenant', checkedRadioValue('type_of_tenant'));

        const idFile = document.getElementById('id_document').files[0];
        const contractFile = document.getElementById('signed_contract').files[0];
        if (idFile) formData.append('id_document', idFile);
        if (contractFile) formData.append('signed_contract', contractFile);

        formData.append('dpa_consent', document.getElementById('dpa_consent').checked ? '1' : '0');
        formData.append('contract_acceptance', document.getElementById('contract_acceptance').checked ? '1' : '0');

        fetch('/api/applications', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
            .then(async response => {
                const data = await response.json();
                button.disabled = false;
                button.classList.remove('loading');

                if (!response.ok) {
                    const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
                    showFormError(firstError || data.message || 'Something went wrong. Please review your answers and try again.');
                    return;
                }

                if (data.application && data.application.id) {
                    const numEl = document.getElementById('successAppNumber');
                    numEl.textContent = 'Application #' + data.application.id;
                    numEl.style.display = 'block';
                }

                setActiveStep(5);
            })
            .catch(() => {
                button.disabled = false;
                button.classList.remove('loading');
                showFormError('Something went wrong. Please check your connection and try again.');
            });
    }

    // ===== Sticky nav shadow =====
    window.addEventListener('scroll', function () {
        const nav = document.querySelector('.topnav');
        if (window.scrollY > 10) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });
</script>

</body>
</html>