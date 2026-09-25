<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Approved | NEST.PH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Agbalumo&display=swap" rel="stylesheet">
    @include('partials.movein-styles')
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
                @include('partials.movein-steps', ['step' => 1])

                <div class="panel-body">
                    @if($rejectedProof)
                        {{-- A rejected proof comes first: the tenant's next step is to resend it, not to celebrate. --}}
                        <div class="rejection-notice">
                            <strong>Your last proof of payment was not accepted.</strong>
                            <span>Reason: {{ $rejectedProof->review_notes }}</span>
                            <span>Please submit a new proof of payment.</span>
                        </div>
                    @else
                        <svg class="state-badge" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M32 2 L37.5 7.5 L45 5 L47.5 12.5 L55 15 L52.5 22.5 L58 28 L52.5 33.5 L55 41 L47.5 43.5 L45 51 L37.5 48.5 L32 54 L26.5 48.5 L19 51 L16.5 43.5 L9 41 L11.5 33.5 L6 28 L11.5 22.5 L9 15 L16.5 12.5 L19 5 L26.5 7.5 Z" fill="#5ea86a"/>
                            <path d="M21 32 L28 39 L43 24" stroke="#fff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        </svg>
                    @endif

                    <h2 id="pageHeading">Welcome to Pureza Station Dormitory!</h2>
                    <p class="lead">We are pleased to inform you that your application has been approved by the dormitory administration. To proceed with your move-in process, please settle your required move-in fees to receive your official Move-In Permit.</p>

                    <a href="{{ route('tenant.movein.payment-type') }}" class="btn-login">{{ $rejectedProof ? 'Submit a New Proof of Payment' : 'Proceed with Payment' }}</a>
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
