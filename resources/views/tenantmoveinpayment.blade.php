<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment | NEST.PH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Agbalumo&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
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
        .topnav .logo {
            display: flex; align-items: center; gap: 6px; color: #fff; font-weight: 700;
            font-size: 19px; letter-spacing: 0.02em; white-space: nowrap; text-decoration: none;
        }
        .topnav .logo .mark { width: 18px; height: 18px; border: 2px solid #fff; border-radius: 4px; flex-shrink: 0; }
        .topnav .buttons { flex: 1; display: flex; justify-content: flex-end; gap: 12px; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 40px; padding: 0 18px; border: 2px solid #fff;
            font-weight: 500; font-size: 13.5px; cursor: pointer;
            white-space: nowrap; text-decoration: none;
        }
        .btn-white { background: #fff; color: #292420; }
        .btn-outline-white { background: transparent; color: #fff; }

        .login-grid {
            display: grid; grid-template-columns: 33% 67%; align-items: stretch;
            min-height: calc(100vh - 70px);
            padding: clamp(16px, 3vw, 20px) clamp(24px, 5vw, 48px) clamp(24px, 5vw, 48px) 0;
        }
        .login-left {
            position: relative; padding: 16px clamp(20px, 4vw, 40px) clamp(24px, 4vw, 40px) clamp(28px, 6vw, 64px);
            display: flex; flex-direction: column;
        }
        .back-button {
            background: none; border: none; color: #fff; font-size: 22px; cursor: pointer;
            line-height: 1; padding: 0; margin-bottom: 32px; align-self: flex-start;
        }
        .login-left-content { flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .login-left h1 { color: #fff; font-weight: 700; font-size: clamp(20px, 2.4vw, 28px); line-height: 1.2; max-width: 300px; }
        .login-left h1 .accent { display: block; color: #44ad65; font-weight: 900; font-size: clamp(24px, 3vw, 32px); margin-top: 4px; }
        .brand-mark {
            margin-top: 30px; width: 120px; height: 120px;
            background: linear-gradient(180deg, #567357 0%, #a2d9a4 100%);
            border-radius: 0 64px 64px 0; display: flex; align-items: center; justify-content: center;
        }
        .brand-mark span { font-family: 'Agbalumo', cursive; font-size: 76px; color: #fff; line-height: 1; }

        .login-right-wrap { position: relative; padding-top: 16px; display: flex; flex-direction: column; }
        .login-right {
            background: #eeeded; border-radius: 28px;
            padding: clamp(28px, 4vw, 40px) clamp(24px, 4vw, 40px);
            flex: 1;
        }

        .payment-title { color: #44ad65; font-weight: 900; font-size: clamp(19px, 2.2vw, 24px); text-transform: uppercase; letter-spacing: 0.02em; margin-bottom: 22px; }

        .top-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 22px; }
        .balance-card {
            background: #fff; border-radius: 10px; padding: 18px 22px; display: flex; align-items: center; gap: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .balance-icon { width: 42px; height: 42px; border-radius: 8px; background: #d9f2dd; color: #197335; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 18px; flex-shrink: 0; }
        .balance-label { font-size: 12px; color: #7a8a7c; }
        .balance-amount { font-size: 24px; font-weight: 900; color: #194e19; }

        .qr-card {
            border-radius: 10px; padding: 18px 22px; color: #fff; display: flex; align-items: center; gap: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .qr-card.gcash { background: linear-gradient(135deg, #0072ec, #00baf2); }
        .qr-card.bdo { background: linear-gradient(135deg, #003da5, #002b73); }
        .qr-card-info { flex: 1; }
        .qr-brand { font-size: 20px; font-weight: 900; letter-spacing: 0.02em; margin-bottom: 4px; }
        .qr-scan-label { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.9; }
        .qr-code-box { background: #fff; border-radius: 8px; padding: 6px; flex-shrink: 0; }
        .qr-code-box canvas { display: block; border-radius: 4px; }
        .qr-merchant { font-size: 10px; color: #292420; text-align: center; margin-top: 4px; font-weight: 700; max-width: 100px; word-break: break-word; }

        .form-columns { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .panel-card { background: #fff; border-radius: 10px; padding: 22px 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .panel-card h3 { font-size: 14px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .panel-card h3 svg { width: 17px; height: 17px; color: #567357; }

        .dropzone {
            border: 2px dashed #c5d1c7; border-radius: 10px; padding: 34px 20px; text-align: center;
            cursor: pointer; position: relative; background: #fbfcfb;
        }
        .dropzone.dragover { border-color: #567357; background: #eef5ef; }
        .dropzone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .dropzone-icon { width: 42px; height: 42px; border-radius: 50%; background: #e2ede3; color: #567357; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
        .dropzone-icon svg { width: 20px; height: 20px; }
        .dropzone-text { font-weight: 700; font-size: 13.5px; margin-bottom: 4px; }
        .dropzone-or { font-size: 12px; color: #9aa5ac; margin: 8px 0; }
        .choose-file-btn { display: inline-block; background: #fff; border: 1px solid #a6b69f; color: #567357; font-weight: 700; font-size: 12.5px; padding: 9px 20px; border-radius: 7px; }
        .dropzone-hint { font-size: 11px; color: #9aa5ac; margin-top: 10px; }

        .uploaded-file-label { font-weight: 700; font-size: 13px; margin: 16px 0 8px; }
        .uploaded-file { display: none; align-items: center; gap: 12px; background: #fbfcfb; border: 1px solid #e2e6e3; border-radius: 8px; padding: 10px 12px; }
        .uploaded-file.visible { display: flex; }
        .uploaded-file-thumb { width: 40px; height: 40px; border-radius: 6px; background: #dfe6e0; object-fit: cover; flex-shrink: 0; }
        .uploaded-file-name { font-size: 12.5px; font-weight: 600; }
        .uploaded-file-size { font-size: 11px; color: #9aa5ac; }
        .remove-file-btn { margin-left: auto; background: none; border: none; color: #d9564f; font-size: 11.5px; font-weight: 700; cursor: pointer; }

        .fld { margin-bottom: 16px; }
        .fld label { display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 7px; }
        .fld label .req { color: #d95117; }
        .fld input, .fld textarea { width: 100%; border: 1px solid #d8dde3; border-radius: 8px; padding: 10px 13px; font-size: 13px; font-family: inherit; }
        .fld textarea { min-height: 60px; resize: vertical; }

        .submit-row { margin-top: 20px; }
        .btn-submit {
            display: inline-flex; align-items: center; gap: 8px; background: #345234; color: #fff; border: none;
            border-radius: 8px; padding: 14px 30px; font-weight: 700; font-size: 13.5px; cursor: pointer;
        }
        .btn-submit:hover:not(:disabled) { background: #26401f; }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }
        .btn-submit svg { width: 15px; height: 15px; }

        .form-error {
            display: none; background: #fdf0f0; border: 1px solid #f3cccc; color: #b3261e;
            border-radius: 8px; padding: 12px 14px; font-size: 13px; margin-bottom: 18px;
        }
        .form-error.visible { display: block; }

        .success-state { display: none; text-align: center; padding: 40px 20px; }
        .success-state.visible { display: block; }
        .success-badge { width: 64px; height: 64px; margin: 0 auto 18px; display: block; }
        .success-state h2 { color: #194e19; margin-bottom: 10px; }
        .success-state p { color: #7a8a7c; font-size: 13.5px; line-height: 1.6; max-width: 420px; margin: 0 auto; }

        @media (max-width: 1024px) {
            .login-grid { grid-template-columns: 1fr; padding: 20px 24px 32px; }
            .login-left { padding: 0 0 24px; }
            .top-row, .form-columns { grid-template-columns: 1fr; }
            .topnav { padding: 14px 24px; flex-wrap: wrap; }
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
    <div class="login-grid">
        <div class="login-left">
            <button class="back-button" type="button" aria-label="Go back" onclick="window.location.href='{{ route('tenant.movein.payment-method') }}'">←</button>
            <div class="login-left-content">
                <h1>Study hard, make friends, and live your<span class="accent">NEST life.</span></h1>
                <div class="brand-mark"><span>N</span></div>
            </div>
        </div>

        <div class="login-right-wrap">
            <div class="login-right">

                <div id="formState">
                    <div class="payment-title">{{ $paymentType === 'partial' ? 'Partial Payment' : 'Full Payment' }}</div>

                    <div class="form-error" id="formError"></div>

                    <div class="top-row">
                        <div class="balance-card">
                            <div class="balance-icon">₱</div>
                            <div>
                                <div class="balance-label">Balance to Pay</div>
                                <div class="balance-amount">₱{{ number_format($billing?->total_amount ?? 0, 0) }}</div>
                            </div>
                        </div>

                        <div class="qr-card {{ $paymentMethod }}">
                            <div class="qr-card-info">
                                <div class="qr-brand">{{ $paymentMethod === 'bdo' ? 'BDO' : 'GCash' }}</div>
                                <div class="qr-scan-label">Scan to Pay Here</div>
                            </div>
                            <div>
                                <div class="qr-code-box"><canvas id="qrCanvas"></canvas></div>
                                <div class="qr-merchant">{{ $dormName }}<br>{{ $paymentMethod === 'bdo' ? ($bdoAccountNumber ?: 'Account not set') : ($gcashNumber ?: 'Number not set') }}</div>
                            </div>
                        </div>
                    </div>

                    <form id="proofForm">
                        <div class="form-columns">
                            <div class="panel-card">
                                <h3>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 19h16"/></svg>
                                    Proof of Payment
                                </h3>

                                <div class="dropzone" id="dropzone">
                                    <input type="file" id="proofFile" accept=".jpg,.jpeg,.png,.pdf" required>
                                    <div class="dropzone-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 14.9A7 7 0 1115.7 8h1.3a4.5 4.5 0 010 9H16"/><path d="M12 12v9M9 15l3-3 3 3"/></svg>
                                    </div>
                                    <div class="dropzone-text">Drag and drop your file here</div>
                                    <div class="dropzone-or">or</div>
                                    <span class="choose-file-btn">Choose File</span>
                                    <div class="dropzone-hint">JPG, PNG, PDF up to 10MB</div>
                                </div>

                                <div class="uploaded-file-label">Uploaded file</div>
                                <div class="uploaded-file" id="uploadedFile">
                                    <img class="uploaded-file-thumb" id="uploadedThumb" src="" alt="">
                                    <div>
                                        <div class="uploaded-file-name" id="uploadedName"></div>
                                        <div class="uploaded-file-size" id="uploadedSize"></div>
                                    </div>
                                    <button type="button" class="remove-file-btn" id="removeFileBtn">Remove file</button>
                                </div>
                            </div>

                            <div class="panel-card">
                                <h3>Proof of Payment</h3>

                                <div class="fld">
                                    <label for="referenceNumber">Reference / Transaction ID <span class="req">*</span></label>
                                    <input type="text" id="referenceNumber" placeholder="1234 5678 9012 3456" required>
                                </div>
                                <div class="fld">
                                    <label for="paymentDate">Date of Payment <span class="req">*</span></label>
                                    <input type="date" id="paymentDate" required>
                                </div>
                                <div class="fld">
                                    <label for="paymentTime">Time of Payment <span class="req">*</span></label>
                                    <input type="time" id="paymentTime" required>
                                </div>
                                <div class="fld">
                                    <label for="amountPaid">Amount Paid <span class="req">*</span></label>
                                    <input type="number" id="amountPaid" step="0.01" min="0.01" value="{{ $billing?->total_amount ?? '' }}" {{ $paymentType === 'partial' ? '' : 'readonly' }} required>
                                </div>
                                <div class="fld">
                                    <label for="notes">Notes (optional)</label>
                                    <textarea id="notes" placeholder="Add any additional information.........."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="submit-row">
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l14-7-7 14-2-6-5-1z"/></svg>
                                <span id="submitBtnText">Submit Proof of Payment</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="success-state" id="successState">
                    <svg class="success-badge" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 2 L37.5 7.5 L45 5 L47.5 12.5 L55 15 L52.5 22.5 L58 28 L52.5 33.5 L55 41 L47.5 43.5 L45 51 L37.5 48.5 L32 54 L26.5 48.5 L19 51 L16.5 43.5 L9 41 L11.5 33.5 L6 28 L11.5 22.5 L9 15 L16.5 12.5 L19 5 L26.5 7.5 Z" fill="#5ea86a"/>
                        <path d="M21 32 L28 39 L43 24" stroke="#fff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                    <h2>Proof of Payment Submitted</h2>
                    <p>An administrator will review your payment shortly. You'll receive an email once it's verified and your account is activated.</p>
                </div>

            </div>
        </div>
    </div>
    </div>

<script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('proofFile');
    const uploadedFile = document.getElementById('uploadedFile');
    const uploadedThumb = document.getElementById('uploadedThumb');
    const uploadedName = document.getElementById('uploadedName');
    const uploadedSize = document.getElementById('uploadedSize');

    function formatSize(bytes) {
        return bytes > 1024 * 1024
            ? (bytes / (1024 * 1024)).toFixed(1) + ' MB'
            : Math.round(bytes / 1024) + ' KB';
    }

    function showFile(file) {
        uploadedName.textContent = file.name;
        uploadedSize.textContent = formatSize(file.size);
        uploadedFile.classList.add('visible');

        if (file.type.startsWith('image/')) {
            uploadedThumb.src = URL.createObjectURL(file);
        } else {
            uploadedThumb.src = '';
        }
    }

    fileInput.addEventListener('change', function () {
        if (this.files[0]) showFile(this.files[0]);
    });

    ['dragenter', 'dragover'].forEach(evt => {
        dropzone.addEventListener(evt, e => { e.preventDefault(); dropzone.classList.add('dragover'); });
    });
    ['dragleave', 'drop'].forEach(evt => {
        dropzone.addEventListener(evt, e => { e.preventDefault(); dropzone.classList.remove('dragover'); });
    });
    dropzone.addEventListener('drop', function (e) {
        if (e.dataTransfer.files[0]) {
            fileInput.files = e.dataTransfer.files;
            showFile(e.dataTransfer.files[0]);
        }
    });

    document.getElementById('removeFileBtn').addEventListener('click', function () {
        fileInput.value = '';
        uploadedFile.classList.remove('visible');
    });

    const submitUrl = '/my/billing/bills/{{ $billing?->id ?? 0 }}/payment-proof';
    const paymentMethodValue = @json($paymentMethod === 'bdo' ? 'bank_transfer' : 'gcash');

    document.getElementById('proofForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const errorBox = document.getElementById('formError');
        errorBox.classList.remove('visible');

        if (!fileInput.files[0]) {
            errorBox.textContent = 'Please attach a screenshot or file showing proof of payment.';
            errorBox.classList.add('visible');
            return;
        }

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        document.getElementById('submitBtnText').textContent = 'Submitting...';

        const paymentDate = document.getElementById('paymentDate').value;
        const paymentTime = document.getElementById('paymentTime').value;
        const referenceNumber = document.getElementById('referenceNumber').value;
        const amountPaid = document.getElementById('amountPaid').value;
        const notesRaw = document.getElementById('notes').value.trim();

        // The backend only stores a plain date for payment_date — the time
        // is folded into notes instead of altering that column's type
        // system-wide, since several other flows (recordCash, receipts,
        // balance calculations) already assume payment_date is date-only.
        const combinedNotes = (paymentTime ? `Time of payment: ${paymentTime}. ` : '') + notesRaw;

        const formData = new FormData();
        formData.append('amount_paid', amountPaid);
        formData.append('payment_method', paymentMethodValue);
        formData.append('reference_number', referenceNumber);
        formData.append('payment_date', paymentDate);
        formData.append('notes', combinedNotes);
        formData.append('proof', fileInput.files[0]);

        try {
            const response = await fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                errorBox.textContent = data.message || 'Something went wrong. Please review your entries and try again.';
                errorBox.classList.add('visible');
                submitBtn.disabled = false;
                document.getElementById('submitBtnText').textContent = 'Submit Proof of Payment';
                return;
            }

            document.getElementById('formState').style.display = 'none';
            document.getElementById('successState').classList.add('visible');
        } catch (err) {
            errorBox.textContent = 'Something went wrong. Please check your connection and try again.';
            errorBox.classList.add('visible');
            submitBtn.disabled = false;
            document.getElementById('submitBtnText').textContent = 'Submit Proof of Payment';
        }
    });

    window.addEventListener('scroll', function () {
        const nav = document.querySelector('.topnav');
        if (window.scrollY > 10) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });

    // QR code generation runs LAST and is wrapped defensively — if the CDN
    // library fails to load (network restriction, ad blocker, offline), this
    // must never be able to break the actual upload/submit functionality
    // above, which is the part that actually matters.
    try {
        const qrPayload = @json($paymentMethod === 'bdo'
            ? 'BDO Account: ' . ($bdoAccountNumber ?: 'Not configured')
            : 'GCash: ' . ($gcashNumber ?: 'Not configured'));

        if (typeof QRCode !== 'undefined') {
            QRCode.toCanvas(document.getElementById('qrCanvas'), qrPayload, { width: 96, margin: 1 });
        } else {
            document.querySelector('.qr-code-box').innerHTML =
                '<div style="width:96px;height:96px;display:flex;align-items:center;justify-content:center;font-size:10px;color:#9aa5ac;text-align:center;padding:6px;">QR code unavailable</div>';
        }
    } catch (qrError) {
        console.warn('QR code generation failed (non-critical):', qrError);
    }
</script>

</body>
</html>