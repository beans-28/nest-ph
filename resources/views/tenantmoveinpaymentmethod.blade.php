<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Method | NEST.PH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Agbalumo&display=swap" rel="stylesheet">
    @include('partials.movein-styles')
    <style>
        .helper-text { font-weight: 500; font-size: 14px; color: var(--muted); margin-bottom: 28px; }

        .choice-group { border: none; width: 100%; max-width: 560px; margin: 0 auto 28px; display: grid; gap: 12px; }
        .choice {
            display: flex; align-items: center; gap: 14px; text-align: left;
            background: #fff; border: 1.5px solid var(--line); border-radius: 12px; padding: 16px 18px;
            cursor: pointer; transition: border-color 0.15s, box-shadow 0.15s;
        }
        .choice:hover { border-color: #a6b69f; }
        .choice input { width: 18px; height: 18px; margin: 0; accent-color: var(--green-darker); flex-shrink: 0; }
        .choice:has(input:checked) { border-color: var(--green-darker); box-shadow: 0 2px 10px rgba(25,115,53,0.12); }
        .choice:has(input:focus-visible) { outline: 2px solid var(--green-darker); outline-offset: 2px; }
        .method-icon {
            width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center;
            justify-content: center; color: #fff; flex-shrink: 0;
        }
        .method-icon svg { width: 20px; height: 20px; }
        .method-icon.gcash { background: #007dfe; }
        .method-icon.bdo { background: #003da5; }
        .method-name { display: block; font-weight: 700; font-size: 15px; color: var(--ink); }
        .method-desc { display: block; font-size: 12.5px; color: var(--muted); margin-top: 2px; line-height: 1.5; }
    </style>
</head>
<body>

    @include('partials.public-nav', ['tenantSession' => true])

    <div class="page-wrap">
    <main class="login-grid">
        <div class="login-left">
            <a class="back-button" href="{{ route('tenant.movein.payment-type') }}" aria-label="Back to payment type">←</a>
            <div class="login-left-content">
                <h1>Study hard, make friends, and live your<span class="accent">NEST life.</span></h1>
                <div class="brand-mark" aria-hidden="true"><span>N</span></div>
            </div>
        </div>

        <div class="login-right-wrap">
            <section class="login-right centered" aria-labelledby="pageHeading">
                @include('partials.movein-steps', ['step' => 3])

                <form class="panel-body" method="POST" action="{{ route('tenant.movein.payment-method.store') }}" id="methodForm">
                    @csrf
                    <h2 id="pageHeading">Select a payment method</h2>
                    <p class="helper-text">Your room will be automatically reserved when payment is received!</p>

                    <fieldset class="choice-group">
                        <legend class="visually-hidden">Payment method</legend>

                        <label class="choice">
                            <input type="radio" name="payment_method" value="gcash" required>
                            <span class="method-icon gcash" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2" width="12" height="20" rx="2.5"/><path d="M11 18h2"/></svg>
                            </span>
                            <span>
                                <span class="method-name">GCash</span>
                                <span class="method-desc">Pay using your GCash account or mobile number</span>
                            </span>
                        </label>

                        <label class="choice">
                            <input type="radio" name="payment_method" value="bdo" required>
                            <span class="method-icon bdo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10l9-6 9 6"/><path d="M5 10v8M9.5 10v8M14.5 10v8M19 10v8"/><path d="M3 21h18"/></svg>
                            </span>
                            <span>
                                <span class="method-name">BDO</span>
                                <span class="method-desc">Pay using your BDO account</span>
                            </span>
                        </label>
                    </fieldset>

                    <button type="submit" class="btn-login" id="proceedBtn" disabled>
                        <span class="spinner"></span>
                        <span>Proceed with Payment</span>
                    </button>
                </form>
            </section>
        </div>
    </main>
    </div>

<script>
    (function () {
        const form = document.getElementById('methodForm');
        const btn = document.getElementById('proceedBtn');
        form.addEventListener('change', function () {
            btn.disabled = !form.querySelector('input[name="payment_method"]:checked');
        });
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.classList.add('loading');
        });
    })();

    window.addEventListener('scroll', function () {
        document.querySelector('.topnav').classList.toggle('scrolled', window.scrollY > 10);
    });
</script>

</body>
</html>
