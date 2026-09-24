<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pay Move-In Fee | NEST.PH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Agbalumo&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    @include('partials.movein-styles')
    <style>
        /* This step carries a two-column form, so the panel gets more room */
        @media (min-width: 1025px) {
            .login-grid { grid-template-columns: 30% 70%; }
            .login-left h1 { font-size: clamp(22px, 2.4vw, 30px); max-width: 300px; }
            .login-left h1 .accent { font-size: clamp(26px, 3vw, 34px); }
            .brand-mark { width: 120px; height: 120px; border-radius: 0 64px 64px 0; }
            .brand-mark span { font-size: 76px; }
        }
        .movein-steps { margin-left: 0; }
        .login-right h2 { font-size: clamp(22px, 2.4vw, 28px); margin-bottom: 20px; }

        .top-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        .balance-card {
            background: #fff; border-radius: 12px; padding: 20px 22px;
            display: flex; flex-direction: column; justify-content: center;
        }
        .balance-label { font-size: 13px; font-weight: 500; color: var(--muted); }
        .balance-amount { font-size: clamp(26px, 3vw, 32px); font-weight: 900; color: var(--green-deep); font-variant-numeric: tabular-nums; margin-top: 2px; }
        .balance-type { font-size: 12.5px; color: var(--muted); margin-top: 6px; }

        .qr-card { border-radius: 12px; padding: 18px 20px; color: #fff; display: flex; align-items: center; gap: 16px; }
        .qr-card.gcash { background: linear-gradient(135deg, #0065d1, #0093d6); }
        .qr-card.bdo { background: linear-gradient(135deg, #003da5, #002b73); }
        .qr-card-info { flex: 1; min-width: 0; }
        .qr-brand { font-size: 20px; font-weight: 900; letter-spacing: 0.02em; margin-bottom: 4px; }
        .qr-scan-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .qr-account { font-size: 13px; margin-top: 8px; font-variant-numeric: tabular-nums; word-break: break-word; }
        .qr-code-box { background: #fff; border-radius: 8px; padding: 6px; flex-shrink: 0; }
        .qr-code-box canvas { display: block; border-radius: 4px; }
        .qr-fallback { width: 96px; height: 96px; display: flex; align-items: center; justify-content: center; font-size: 11px; color: var(--muted); text-align: center; padding: 6px; }

        .form-columns { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .panel-card { background: #fff; border-radius: 12px; padding: 22px 24px; min-width: 0; }
        .panel-card h3 { font-size: 15px; font-weight: 700; margin-bottom: 16px; color: var(--ink); }

        .dropzone {
            border: 2px dashed #c5d1c7; border-radius: 10px; padding: 30px 20px; text-align: center;
            cursor: pointer; position: relative; background: #fbfcfb; transition: border-color 0.15s, background 0.15s;
        }
        .dropzone:hover, .dropzone.dragover { border-color: var(--green-dark); background: #eef5ef; }
        .dropzone:has(input:focus-visible) { outline: 2px solid var(--green-darker); outline-offset: 2px; }
        .dropzone input[type=file] { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
        .dropzone-icon { width: 44px; height: 44px; border-radius: 50%; background: #e2ede3; color: var(--green-dark); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
        .dropzone-icon svg { width: 20px; height: 20px; }
        .dropzone-text { font-weight: 700; font-size: 14px; margin-bottom: 4px; }
        .dropzone-or { font-size: 12px; color: var(--muted); margin: 8px 0; }
        .choose-file-btn { display: inline-block; background: #fff; border: 1px solid #a6b69f; color: var(--green-dark); font-weight: 700; font-size: 13px; padding: 9px 20px; border-radius: 7px; }
        .dropzone-hint { font-size: 12px; color: var(--muted); margin-top: 10px; }
        .touch-only { display: none; }
        @media (hover: none) { .touch-only { display: inline; } .pointer-only { display: none; } }

        .uploaded-file { display: none; align-items: center; gap: 12px; background: #fbfcfb; border: 1px solid #e2e6e3; border-radius: 8px; padding: 10px 12px; margin-top: 14px; }
        .uploaded-file.visible { display: flex; }
        .uploaded-file-thumb { width: 40px; height: 40px; border-radius: 6px; background: #dfe6e0; object-fit: cover; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: var(--green-dark); font-size: 10px; font-weight: 700; }
        .uploaded-file-meta { min-width: 0; }
        .uploaded-file-name { font-size: 13px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .uploaded-file-size { font-size: 12px; color: var(--muted); }
        .remove-file-btn { margin-left: auto; background: none; border: none; color: #b3261e; font-family: inherit; font-size: 12.5px; font-weight: 700; cursor: pointer; padding: 8px 4px; flex-shrink: 0; }

        .fld { margin-bottom: 16px; }
        .fld:last-child { margin-bottom: 0; }
        .fld-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .fld label { display: block; font-size: 13px; font-weight: 500; color: var(--green-dark); margin-bottom: 7px; }
        .fld label .req { color: #d95117; }
        .fld input, .fld textarea {
            width: 100%; border: 1px solid #cfd6d0; border-radius: 8px; padding: 10px 12px;
            font-size: 14px; font-family: inherit; color: var(--ink); background: #fff;
        }
        .fld input:focus, .fld textarea:focus { border-color: var(--green-dark); outline: none; box-shadow: 0 0 0 3px rgba(86,115,87,0.15); }
        .fld input[readonly] { background: #f3f5f3; color: var(--muted); }
        .fld textarea { min-height: 72px; resize: vertical; }
        .fld-hint { font-size: 12px; color: var(--muted); margin-top: 6px; }

        .submit-row { margin-top: 22px; display: flex; justify-content: flex-end; }

        .success-state { display: none; flex: 1; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 20px 0; }
        .success-state.visible { display: flex; }

        @media (max-width: 1024px) {
            .top-row, .form-columns { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .panel-card { padding: 18px 16px; }
            .qr-card { padding: 16px; }
            .submit-row .btn-login { width: 100%; }
            .fld-row { grid-template-columns: 1fr; gap: 0; }
            .fld-row .fld { margin-bottom: 16px; }
        }
    </style>
</head>
<body>

    @include('partials.public-nav', ['tenantSession' => true])

    <div class="page-wrap">
    <main class="login-grid">
        <div class="login-left">
            <a class="back-button" href="{{ route('tenant.movein.payment-method') }}" aria-label="Back to payment method">←</a>
            <div class="login-left-content">
                <h1>Study hard, make friends, and live your<span class="accent">NEST life.</span></h1>
                <div class="brand-mark" aria-hidden="true"><span>N</span></div>
            </div>
        </div>

        <div class="login-right-wrap">
            <section class="login-right" aria-labelledby="pageHeading">

                <div id="formState">
                    @include('partials.movein-steps', ['step' => 4])

                    <h2 id="pageHeading">Pay and upload your proof</h2>

                    <div class="form-error" id="formError" role="alert"></div>

                    <div class="top-row">
                        <div class="balance-card">
                            <div class="balance-label">Balance to pay</div>
                            <div class="balance-amount">₱{{ number_format($billing?->total_amount ?? 0, 2) }}</div>
                            <div class="balance-type">{{ $paymentType === 'partial' ? 'Partial payment — enter the amount you sent below.' : 'Full payment' }}</div>
                        </div>

                        <div class="qr-card {{ $paymentMethod }}">
                            <div class="qr-card-info">
                                <div class="qr-brand">{{ $paymentMethod === 'bdo' ? 'BDO' : 'GCash' }}</div>
                                <div class="qr-scan-label">Scan to pay here</div>
                                <div class="qr-account">{{ $dormName }}<br>{{ $paymentMethod === 'bdo' ? ($bdoAccountNumber ?: 'Account not set') : ($gcashNumber ?: 'Number not set') }}</div>
                            </div>
                            <div class="qr-code-box"><canvas id="qrCanvas" aria-label="Payment QR code" role="img"></canvas></div>
                        </div>
                    </div>

                    <form id="proofForm" novalidate>
                        <div class="form-columns">
                            <div class="panel-card">
                                <h3 id="proofHeading">Proof of payment</h3>

                                <div class="dropzone" id="dropzone">
                                    <input type="file" id="proofFile" accept=".jpg,.jpeg,.png,.pdf" aria-labelledby="proofHeading" aria-describedby="proofHint" required>
                                    <div class="dropzone-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14.9A7 7 0 1115.7 8h1.3a4.5 4.5 0 010 9H16"/><path d="M12 12v9M9 15l3-3 3 3"/></svg>
                                    </div>
                                    <div class="dropzone-text"><span class="pointer-only">Drag and drop your file here</span><span class="touch-only">Add a screenshot or file</span></div>
                                    <div class="dropzone-or pointer-only">or</div>
                                    <span class="choose-file-btn" aria-hidden="true">Choose File</span>
                                    <div class="dropzone-hint" id="proofHint">JPG, PNG, PDF up to 10MB</div>
                                </div>

                                <div class="uploaded-file" id="uploadedFile">
                                    <img class="uploaded-file-thumb" id="uploadedThumb" src="" alt="">
                                    <div class="uploaded-file-meta">
                                        <div class="uploaded-file-name" id="uploadedName"></div>
                                        <div class="uploaded-file-size" id="uploadedSize"></div>
                                    </div>
                                    <button type="button" class="remove-file-btn" id="removeFileBtn">Remove</button>
                                </div>
                            </div>

                            <div class="panel-card">
                                <h3>Payment details</h3>

                                <div class="fld">
                                    <label for="referenceNumber">Reference / Transaction ID <span class="req" aria-hidden="true">*</span></label>
                                    <input type="text" id="referenceNumber" placeholder="1234 5678 9012 3456" autocomplete="off" required>
                                </div>
                                <div class="fld-row">
                                    <div class="fld">
                                        <label for="paymentDate">Date of payment <span class="req" aria-hidden="true">*</span></label>
                                        <input type="date" id="paymentDate" required>
                                    </div>
                                    <div class="fld">
                                        <label for="paymentTime">Time of payment <span class="req" aria-hidden="true">*</span></label>
                                        <input type="time" id="paymentTime" required>
                                    </div>
                                </div>
                                <div class="fld">
                                    <label for="amountPaid">Amount paid <span class="req" aria-hidden="true">*</span></label>
                                    <input type="number" id="amountPaid" inputmode="decimal" step="0.01" min="0.01" value="{{ $billing?->total_amount ?? '' }}" {{ $paymentType === 'partial' ? '' : 'readonly' }} required>
                                    @if($paymentType !== 'partial')
                                        <div class="fld-hint">Set to the full move-in fee.</div>
                                    @endif
                                </div>
                                <div class="fld">
                                    <label for="notes">Notes (optional)</label>
                                    <textarea id="notes" placeholder="Anything the admin should know about this payment"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="submit-row">
                            <button type="submit" class="btn-login" id="submitBtn">
                                <span class="spinner"></span>
                                <span id="submitBtnText">Submit Proof of Payment</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="success-state" id="successState" role="status">
                    <svg class="state-badge" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M32 2 L37.5 7.5 L45 5 L47.5 12.5 L55 15 L52.5 22.5 L58 28 L52.5 33.5 L55 41 L47.5 43.5 L45 51 L37.5 48.5 L32 54 L26.5 48.5 L19 51 L16.5 43.5 L9 41 L11.5 33.5 L6 28 L11.5 22.5 L9 15 L16.5 12.5 L19 5 L26.5 7.5 Z" fill="#5ea86a"/>
                        <path d="M21 32 L28 39 L43 24" stroke="#fff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                    <h2 id="successHeading" tabindex="-1">Proof of Payment Submitted</h2>
                    <p class="lead">An administrator will review your payment shortly. You'll receive an email once it's verified and your account is activated.</p>
                    <a href="{{ route('tenant.movein.pending') }}" class="btn-login">View Payment Status</a>
                </div>

            </section>
        </div>
    </main>
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
            uploadedThumb.style.visibility = 'visible';
        } else {
            uploadedThumb.removeAttribute('src');
            uploadedThumb.style.visibility = 'hidden';
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
        fileInput.focus();
    });

    const submitUrl = '/my/billing/bills/{{ $billing?->id ?? 0 }}/payment-proof';
    const paymentMethodValue = @json($paymentMethod === 'bdo' ? 'bank_transfer' : 'gcash');

    document.getElementById('proofForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const errorBox = document.getElementById('formError');
        errorBox.classList.remove('visible');

        function showError(message) {
            errorBox.textContent = message;
            errorBox.classList.add('visible');
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        if (!fileInput.files[0]) {
            showError('Please attach a screenshot or file showing proof of payment.');
            return;
        }
        const missing = ['referenceNumber', 'paymentDate', 'paymentTime', 'amountPaid']
            .map(id => document.getElementById(id))
            .find(input => !input.value.trim());
        if (missing) {
            const label = document.querySelector(`label[for="${missing.id}"]`).firstChild.textContent.trim();
            showError(`Please fill in ${label.toLowerCase()}.`);
            missing.focus();
            return;
        }

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        document.getElementById('submitBtnText').textContent = 'Submitting...';

        function resetButton() {
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            document.getElementById('submitBtnText').textContent = 'Submit Proof of Payment';
        }

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
                const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
                showError(firstError || data.message || 'Something went wrong. Please review your entries and try again.');
                resetButton();
                return;
            }

            document.getElementById('formState').style.display = 'none';
            document.getElementById('successState').classList.add('visible');
            document.getElementById('successHeading').focus();
        } catch (err) {
            showError('Something went wrong. Please check your connection and try again.');
            resetButton();
        }
    });

    window.addEventListener('scroll', function () {
        document.querySelector('.topnav').classList.toggle('scrolled', window.scrollY > 10);
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
            document.querySelector('.qr-code-box').innerHTML = '<div class="qr-fallback">QR code unavailable</div>';
        }
    } catch (qrError) {
        console.warn('QR code generation failed (non-critical):', qrError);
    }
</script>

</body>
</html>
