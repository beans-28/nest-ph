<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Type · {{ $brandDormName }}</title>
    <link rel="icon" href="{{ $brandFaviconUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Agbalumo&display=swap" rel="stylesheet">
    @include('partials.movein-styles')
    <style>
        .fee-amount { font-size: 15px; color: var(--muted); margin-bottom: 28px; font-variant-numeric: tabular-nums; }
        .fee-amount strong { display: block; color: var(--green-deep); font-size: clamp(28px, 3.4vw, 36px); font-weight: 900; margin-top: 4px; }

        .choice-group { border: none; width: 100%; max-width: 560px; margin: 0 auto 22px; }
        .choice-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .choice {
            position: relative; display: flex; flex-direction: column; gap: 4px; text-align: left;
            background: #fff; border: 1.5px solid var(--line); border-radius: 12px; padding: 18px 20px 18px 50px;
            cursor: pointer; transition: border-color 0.15s, box-shadow 0.15s;
        }
        .choice:hover { border-color: #a6b69f; }
        .choice input {
            position: absolute; left: 18px; top: 20px; width: 18px; height: 18px; margin: 0;
            accent-color: var(--green-darker);
        }
        .choice-grid.is-single { grid-template-columns: 1fr; max-width: 380px; margin: 0 auto; }
        .choice-title { font-weight: 700; font-size: 15px; color: var(--ink); }
        .choice-desc { font-size: 12.5px; line-height: 1.5; color: var(--muted); }
        .choice:has(input:checked) { border-color: var(--green-darker); box-shadow: 0 2px 10px rgba(25,115,53,0.12); }
        .choice:has(input:focus-visible) { outline: 2px solid var(--green-darker); outline-offset: 2px; }

        .helper-text { font-weight: 700; font-size: 14px; color: var(--ink); margin-bottom: 6px; }
        .sub-text { font-size: 13px; color: var(--muted); line-height: 1.6; max-width: 48ch; margin: 0 auto 26px; }

        @media (max-width: 640px) {
            .choice-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    @include('partials.public-nav', ['tenantSession' => true])

    <div class="page-wrap">
    <main class="login-grid">
        <div class="login-left">
            <a class="back-button" href="{{ route('tenant.movein.welcome') }}" aria-label="Back">←</a>
            <div class="login-left-content">
                <img src="{{ asset('images/nestph.png') }}" alt="NEST.PH" class="brand-mark" width="72" height="68">
                <h1>Malayo sa bahay,<span class="accent">pero at home.</span></h1>
            </div>
        </div>

        <div class="login-right-wrap">
            <section class="login-right centered" aria-labelledby="pageHeading">
                @include('partials.movein-steps', ['step' => 2])

                <form class="panel-body" method="POST" action="{{ route('tenant.movein.payment-type.store') }}" id="typeForm">
                    @csrf
                    @php
                        $remaining = $billing?->remainingBalance() ?? 0;
                        $halfPaid = $billing && $remaining < (float) $billing->total_amount;
                        $canHalf = $billing && $billing->canPayMoveInHalf();
                        $half = $billing?->moveInHalfAmount() ?? 0;
                    @endphp
                    <h2 id="pageHeading">{{ $halfPaid ? 'Pay your remaining balance' : 'Choose your payment type' }}</h2>

                    @if($halfPaid)
                        @include('partials.movein-ledger', ['billing' => $billing])
                    @elseif($billing)
                        <p class="fee-amount">Total move-in fee due <strong>₱{{ number_format($billing->total_amount, 2) }}</strong></p>
                    @endif
                    @error('payment_type')<p class="form-error is-static" role="alert">{{ $message }}</p>@enderror

                    <fieldset class="choice-group">
                        <legend class="visually-hidden">Payment type</legend>
                        <div class="choice-grid{{ $canHalf ? '' : ' is-single' }}">
                            <label class="choice">
                                <input type="radio" name="payment_type" value="full" required @checked(! $canHalf)>
                                <span class="choice-title">{{ $halfPaid ? 'Remaining Balance' : 'Full Payment' }}</span>
                                <span class="choice-desc">{{ $halfPaid ? 'Pay ₱' . number_format($remaining, 2) . '. Your Move-In Permit is sent once it is verified.' : 'Pay the whole ₱' . number_format($remaining, 2) . ' at once.' }}</span>
                            </label>
                            @if($canHalf)
                            <label class="choice">
                                <input type="radio" name="payment_type" value="partial" required>
                                <span class="choice-title">Partial Payment</span>
                                <span class="choice-desc">Pay half (₱{{ number_format($half, 2) }}) now. The other half is due within one month of your payment, or your bedspace is released.</span>
                            </label>
                            @endif
                        </div>
                    </fieldset>

                    @unless($halfPaid)
                        <p class="helper-text">Your bedspace is already reserved for you. Your Move-In Permit is sent once your full payment is verified.</p>
                    @endunless
                    <p class="sub-text">Please wait for the administrator's review and approval. Kindly check your inbox regularly for updates regarding your application status.</p>

                    <button type="submit" class="btn-login" id="continueBtn" @disabled($canHalf)>
                        <span class="spinner"></span>
                        <span>Continue</span>
                    </button>
                </form>
            </section>
        </div>
    </main>
    </div>

<script>
    (function () {
        const form = document.getElementById('typeForm');
        const btn = document.getElementById('continueBtn');
        form.addEventListener('change', function () {
            btn.disabled = !form.querySelector('input[name="payment_type"]:checked');
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
