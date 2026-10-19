<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NEST.PH · Dormitory management made simple</title>
    <link rel="icon" href="{{ asset('images/nestphgreen.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-light: #a2d9a4;
            --green-dark: #567357;
            --green-darker: #197335;
            --ink: #292420;
            --muted: #5b6168;
            --cream-light: #f2f4f8;
            --gray-border: #c1c7cd;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Roboto', system-ui, sans-serif; color: var(--ink); background: #fff; line-height: 1.5; }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }
        a:focus-visible, button:focus-visible, input:focus-visible, textarea:focus-visible {
            outline: 2px solid var(--green-darker); outline-offset: 2px;
        }
        .wrap { width: min(1120px, 100% - 32px); margin-inline: auto; }

        .topnav {
            background: linear-gradient(90deg, var(--green-darker), var(--green-dark));
            color: #fff; position: sticky; top: 0; z-index: 10;
        }
        .topnav .wrap { display: flex; align-items: center; gap: 16px; padding: 14px 0; }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 20px; letter-spacing: .5px; }
        .brand img { height: 34px; width: auto; }
        .topnav nav { margin-left: auto; display: flex; gap: 20px; font-weight: 500; font-size: 14px; }
        .topnav nav a:hover { text-decoration: underline; }
        .topnav a:focus-visible { outline-color: #fff; }

        .hero { background: var(--cream-light); padding: clamp(48px, 9vw, 96px) 0; }
        .eyebrow { color: var(--green-darker); font-weight: 700; font-size: 13px; letter-spacing: 1.5px; text-transform: uppercase; }
        .hero h1 { font-size: clamp(30px, 5vw, 48px); line-height: 1.15; margin: 10px 0 16px; max-width: 18ch; }
        .hero p { color: var(--muted); font-size: 18px; max-width: 60ch; }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 28px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 12px 22px; border-radius: 8px; font-weight: 700; font-size: 15px; border: 2px solid var(--green-darker); cursor: pointer; font-family: inherit; }
        .btn-green { background: var(--green-darker); color: #fff; }
        .btn-green:hover { background: #135a29; }
        .btn-outline { background: #fff; color: var(--green-darker); }
        .btn-outline:hover { background: #eaf6eb; }
        .btn[disabled] { opacity: .6; cursor: wait; }

        section.block { padding: clamp(48px, 8vw, 80px) 0; }
        section.block h2 { font-size: clamp(24px, 3.5vw, 32px); margin: 6px 0 10px; }
        .lead { color: var(--muted); max-width: 65ch; margin-bottom: 28px; }

        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .feature { border: 1px solid var(--gray-border); border-radius: 12px; padding: 20px; }
        .feature h3 { font-size: 16px; margin-bottom: 6px; color: var(--green-darker); }
        .feature p { color: var(--muted); font-size: 14px; }

        .alt { background: var(--cream-light); }
        .dorms { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .dorm-card { background: #fff; border: 1px solid var(--gray-border); border-radius: 14px; padding: 24px; display: flex; flex-direction: column; gap: 12px; }
        .dorm-card .head { display: flex; align-items: center; gap: 14px; }
        .dorm-logo { width: 56px; height: 56px; border-radius: 12px; background: var(--green-light); display: grid; place-items: center; font-weight: 700; color: var(--green-darker); font-size: 22px; overflow: hidden; flex-shrink: 0; }
        .dorm-logo img { width: 100%; height: 100%; object-fit: cover; }
        .dorm-card h3 { font-size: 18px; }
        .dorm-card .addr { color: var(--muted); font-size: 14px; }
        .badge { align-self: flex-start; background: #eaf6eb; color: var(--green-darker); font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 999px; }
        .dorm-card .btn { margin-top: auto; }
        .dorm-card.placeholder { border-style: dashed; background: transparent; justify-content: center; }
        .dorm-card.placeholder p { color: var(--muted); font-size: 14px; }

        .steps { list-style: none; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 36px; counter-reset: step; }
        .steps li { counter-increment: step; padding: 18px; border-radius: 12px; background: var(--cream-light); font-size: 14px; color: var(--muted); }
        .steps li::before { content: counter(step); display: grid; place-items: center; width: 30px; height: 30px; border-radius: 50%; background: var(--green-darker); color: #fff; font-weight: 700; margin-bottom: 10px; }
        .steps strong { display: block; color: var(--ink); font-size: 15px; margin-bottom: 4px; }

        form.request { border: 1px solid var(--gray-border); border-radius: 14px; padding: clamp(20px, 4vw, 32px); display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field.full { grid-column: 1 / -1; }
        label { font-weight: 500; font-size: 14px; }
        label .req { color: #b42318; }
        input, textarea { font: inherit; padding: 10px 12px; border: 1px solid var(--gray-border); border-radius: 8px; width: 100%; }
        textarea { min-height: 110px; resize: vertical; }
        .consent { flex-direction: row; align-items: flex-start; gap: 10px; font-size: 14px; color: var(--muted); }
        .consent input { width: auto; margin-top: 4px; }
        .form-msg { grid-column: 1 / -1; padding: 12px 14px; border-radius: 8px; font-size: 14px; display: none; }
        .form-msg.ok { display: block; background: #eaf6eb; color: var(--green-darker); }
        .form-msg.err { display: block; background: #fdecea; color: #b42318; }
        @media (max-width: 640px) {
            form.request { grid-template-columns: 1fr; }
            .topnav nav { display: none; }
        }

        footer { background: var(--green-darker); color: #fff; padding: 28px 0; font-size: 14px; }
        footer .wrap { display: flex; flex-wrap: wrap; gap: 12px; justify-content: space-between; }
    </style>
</head>
<body>
    <header class="topnav">
        <div class="wrap">
            <a href="{{ route('platform') }}" class="brand"><img src="{{ asset('images/nestph.png') }}" alt=""> NEST.PH</a>
            <nav aria-label="Main">
                <a href="#about">About</a>
                <a href="#dormitories">Dormitories</a>
                <a href="#for-owners">For dorm owners</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="wrap">
                <span class="eyebrow">Dormitory management platform</span>
                <h1>One system for running your dormitory.</h1>
                <p>NEST.PH helps dormitory owners handle rooms, applications, contracts, billing, payments and maintenance in one place, and gives tenants a portal to pay, report issues and stay updated.</p>
                <div class="actions">
                    <a href="#dormitories" class="btn btn-green">Find a dormitory</a>
                    <a href="#for-owners" class="btn btn-outline">I own a dormitory</a>
                </div>
            </div>
        </section>

        <section class="block" id="about">
            <div class="wrap">
                <span class="eyebrow">What NEST.PH does</span>
                <h2>Built for Philippine dormitories</h2>
                <p class="lead">Every dormitory on NEST.PH gets its own public page and its own admin and tenant portals, set up with that dormitory's rules and rates.</p>
                <div class="features">
                    <div class="feature"><h3>Rooms &amp; VR tours</h3><p>Show available rooms and beds, with virtual tours for students who can't visit in person.</p></div>
                    <div class="feature"><h3>Online applications</h3><p>Inquiries and applications come in online, with verification and approval by the admin.</p></div>
                    <div class="feature"><h3>Billing &amp; payments</h3><p>Monthly statements, penalties and payment tracking, following each dorm's own policies.</p></div>
                    <div class="feature"><h3>Tenant portal</h3><p>Tenants view bills, submit maintenance tickets, read announcements and request move-out.</p></div>
                </div>
            </div>
        </section>

        <section class="block alt" id="dormitories">
            <div class="wrap">
                <span class="eyebrow">Choose a dormitory</span>
                <h2>Dormitories on NEST.PH</h2>
                <p class="lead">Pick a dormitory to see its rooms, rates and house rules, or to apply as a tenant.</p>
                <div class="dorms">
                    <article class="dorm-card">
                        <div class="head">
                            <div class="dorm-logo">
                                @if ($dormLogoUrl)
                                    <img src="{{ $dormLogoUrl }}" alt="">
                                @else
                                    {{ mb_substr($dormName, 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <h3>{{ $dormName }}</h3>
                                @if ($dormAddress)<p class="addr">{{ $dormAddress }}</p>@endif
                            </div>
                        </div>
                        <span class="badge">Now accepting tenants</span>
                        <a href="{{ route('home') }}" class="btn btn-green">Visit {{ $dormName }}</a>
                    </article>
                    <article class="dorm-card placeholder">
                        <h3>Your dormitory here</h3>
                        <p>Own a dormitory? Bring it onto NEST.PH and manage it the same way.</p>
                        <a href="#for-owners" class="btn btn-outline">Request NEST.PH</a>
                    </article>
                </div>
            </div>
        </section>

        <section class="block" id="for-owners">
            <div class="wrap">
                <span class="eyebrow">For dormitory owners</span>
                <h2>Request NEST.PH for your dormitory</h2>
                <p class="lead">Tell us about your dormitory and the NEST.PH team will get in touch. Here's how it works:</p>
                <ol class="steps">
                    <li><strong>Send a request</strong>Fill out the form below with your dormitory's details.</li>
                    <li><strong>We contact you</strong>Our team reaches out to discuss your needs and schedule a demo.</li>
                    <li><strong>Setup</strong>We set up your rooms, rates, house rules and admin accounts.</li>
                    <li><strong>Go live</strong>Your dormitory gets its own page here and starts accepting tenants.</li>
                </ol>

                <form class="request" id="partnerForm" novalidate>
                    <div class="field"><label for="owner_name">Your name <span class="req">*</span></label><input id="owner_name" name="owner_name" required maxlength="255" autocomplete="name"></div>
                    <div class="field"><label for="dorm_name">Dormitory name <span class="req">*</span></label><input id="dorm_name" name="dorm_name" required maxlength="255"></div>
                    <div class="field"><label for="email">Email <span class="req">*</span></label><input id="email" name="email" type="email" required maxlength="255" autocomplete="email"></div>
                    <div class="field"><label for="contact_number">Contact number <span class="req">*</span></label><input id="contact_number" name="contact_number" type="tel" required maxlength="30" autocomplete="tel" placeholder="09XX XXX XXXX"></div>
                    <div class="field"><label for="city">City / municipality</label><input id="city" name="city" maxlength="255"></div>
                    <div class="field"><label for="bed_count">Approx. number of beds</label><input id="bed_count" name="bed_count" type="number" min="1" max="10000"></div>
                    <div class="field full"><label for="message">Anything else we should know?</label><textarea id="message" name="message" maxlength="2000" placeholder="Current setup, questions, preferred schedule for a demo..."></textarea></div>
                    <label class="field full consent"><input type="checkbox" name="dpa_consent" id="dpa_consent" required> I agree that NEST.PH may use these details to contact me about this request, in line with the Data Privacy Act of 2012.</label>
                    <div class="field full">@include('partials.turnstile', ['id' => 'partnerTurnstile'])</div>
                    <div class="form-msg" id="partnerMsg" role="status" aria-live="polite"></div>
                    <div class="field full"><button type="submit" class="btn btn-green" id="partnerSubmit">Send request</button></div>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="wrap">
            <span>© {{ date('Y') }} NEST.PH</span>
            <a href="{{ route('login.platform') }}">NEST.PH team login</a>
        </div>
    </footer>

    <script>
        const form = document.getElementById('partnerForm');
        const msg = document.getElementById('partnerMsg');
        const submitBtn = document.getElementById('partnerSubmit');

        function showMsg(text, ok) {
            msg.textContent = text;
            msg.className = 'form-msg ' + (ok ? 'ok' : 'err');
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const data = Object.fromEntries(new FormData(form));
            data.dpa_consent = form.dpa_consent.checked ? 1 : 0;
            data.cf_turnstile_response = turnstileToken('partnerTurnstile');
            turnstileReset('partnerTurnstile');

            submitBtn.disabled = true;
            try {
                const response = await fetch('{{ route('platform.partner-request') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify(data),
                });
                const result = await response.json().catch(() => ({}));
                if (response.ok) {
                    form.reset();
                    showMsg(result.message, true);
                } else if (response.status === 429) {
                    showMsg('Too many requests. Please try again in a few minutes.', false);
                } else {
                    const firstError = result.errors ? Object.values(result.errors)[0][0] : null;
                    showMsg(firstError || result.message || 'Something went wrong. Please try again.', false);
                }
            } catch (err) {
                showMsg('Could not send your request. Please check your connection.', false);
            } finally {
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>
