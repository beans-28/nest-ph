<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Under Review | NEST.PH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Agbalumo&display=swap" rel="stylesheet">
    @include('partials.movein-styles')
    <style>
        .resubmit-note { color: var(--muted); font-size: 13px; margin: 0 0 12px; }
    </style>
</head>
<body>

    @include('partials.public-nav', ['tenantSession' => true])

    <div class="page-wrap">
    <main class="login-grid">
        <div class="login-left">
            <a class="back-button" href="{{ route('home') }}" aria-label="Back to home">←</a>
            <div class="login-left-content">
                <h1>Study hard, make friends, and live your<span class="accent">NEST life.</span></h1>
                <div class="brand-mark" aria-hidden="true"><span>N</span></div>
            </div>
        </div>

        <div class="login-right-wrap">
            <section class="login-right centered" aria-labelledby="pageHeading">
                @include('partials.movein-steps', ['step' => 5])

                <div class="panel-body">
                    <svg class="state-badge" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="32" cy="32" r="30" fill="#e8b13c"/>
                        <path d="M32 16v16l11 6" stroke="#fff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>

                    <h2 id="pageHeading">Your payment is being verified</h2>
                    <p class="lead">We've received your proof of payment and it's currently being reviewed by our admin team. You'll receive your official Move-In Permit by email once it's verified — this usually takes 1&ndash;2 business days.</p>

                    <p class="resubmit-note">Made a mistake, or need to submit a different proof?</p>
                    <a href="{{ route('tenant.movein.payment-type') }}" class="btn-secondary">Submit Again</a>
                </div>
            </section>
        </div>
    </main>
    </div>

<script>
    window.addEventListener('scroll', function () {
        document.querySelector('.topnav').classList.toggle('scrolled', window.scrollY > 10);
    });
</script>

</body>
</html>
