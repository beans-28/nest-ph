{{--
    "How to Secure Your Spot": the real path from applying to moving in.
    Shared by the dorm homepage, the Apply page (above the form) and the
    Apply page's success screen, so the steps never disagree.

    @include('partials.secure-spot-steps', ['current' => 1])
      current: the step the visitor is on (1-5). Earlier steps show as done.
               0 = nobody is on a step yet (homepage).

    Amounts come from the Dormitory Profile's Rental Policy. The bed-hold
    rule matches PaymentController::claimBedForMoveIn(): a bed is only held
    once the first move-in payment is verified, not when applying or when
    approved. Keep this text in sync if that rule changes.
--}}
@php
    $spotProfile = \App\Models\DormitoryProfile::current();
    $spotAdvance = (int) $spotProfile->advance_rent_months;
    $spotDeposit = (int) $spotProfile->security_deposit_months;
    $spotMonths = fn (int $n) => $n . ' ' . \Illuminate\Support\Str::plural('month', $n);
    $spotFee = collect([
        $spotAdvance > 0 ? $spotMonths($spotAdvance) . ' advance rent' : null,
        $spotDeposit > 0 ? $spotMonths($spotDeposit) . ' security deposit' : null,
    ])->filter()->implode(' + ');
    $current = (int) ($current ?? 0);
    $compact = (bool) ($compact ?? false);

    // Compact (Apply page, beside the form): a glanceable map, a few words per step.
    $spotShort = [
        'Fill out the form and sign online',
        'We email you the result',
        'Your login comes in the approval email',
        $spotFee ?: 'Pay to hold your bed',
        'On your start date',
    ];

    $spotSteps = [
        ['Apply and sign', 'Fill out the application, choose your bed, and sign the dormitory documents. Your emergency contact signs too.'],
        ['Wait for review', 'You get a confirmation email right away. The admin reviews your application and emails you the result. Check your Spam folder too.'],
        ['Log in to your tenant account', 'If you are approved, the email includes your login and a temporary password.'],
        ['Pay your move-in fee', 'The move-in fee is ' . $spotFee . '. Pay it in your tenant account, or in cash at the dorm office.'],
        ['Move in', 'Once your full move-in fee is verified, your account becomes active and you can move in on your start date.'],
    ];
@endphp
<style>
    .spot-steps { list-style: none; margin: 0; padding: 0; display: grid; gap: 0; text-align: left; }
    .spot-steps li { position: relative; display: grid; grid-template-columns: 32px 1fr; gap: 14px; padding-bottom: 18px; }
    .spot-steps li:last-child { padding-bottom: 0; }
    /* The line joining one step to the next */
    .spot-steps li:not(:last-child)::after {
        content: ''; position: absolute; left: 15px; top: 34px; bottom: 2px; width: 2px;
        background: color-mix(in srgb, var(--green-dark, #567357) 22%, #e5e0dc);
    }
    .spot-num {
        width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 13.5px; font-weight: 700; font-variant-numeric: tabular-nums;
        color: var(--green-darker, #197335); background: #fff;
        border: 2px solid color-mix(in srgb, var(--green-dark, #567357) 35%, #e5e0dc);
    }
    .spot-num svg { width: 16px; height: 16px; }
    .spot-steps li.is-done .spot-num { background: var(--green-dark, #567357); border-color: var(--green-dark, #567357); color: #fff; }
    .spot-steps li.is-current .spot-num {
        background: var(--green-dark, #567357); border-color: var(--green-dark, #567357); color: #fff;
        box-shadow: 0 0 0 5px color-mix(in srgb, var(--green-dark, #567357) 18%, transparent);
    }
    .spot-body { padding-top: 5px; }
    .spot-title { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; font-size: 15px; font-weight: 700; color: var(--ink, #292420); line-height: 1.3; }
    .spot-tag {
        font-size: 11.5px; font-weight: 600; padding: 2px 9px; border-radius: 999px;
        color: var(--green-darker, #197335); background: color-mix(in srgb, var(--green-dark, #567357) 12%, #fff);
    }
    .spot-text { margin: 4px 0 0; font-size: 14px; line-height: 1.55; color: #5b524c; }
    .spot-steps li.is-done .spot-title { color: #6b625c; }
    /* Money rule: stays visible, not behind an info-tip. */
    .spot-note {
        margin-top: 8px; padding: 9px 12px; border-radius: 10px; font-size: 13.5px; line-height: 1.5;
        color: #6a4a12; background: #fff6e6; border: 1px solid #f1dcb0;
    }
    .spot-note strong { color: #5a3d0c; }
    .spot-steps .visually-hidden { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; }
</style>
@if ($compact)
<style>
    /* Compact journey map for a dark background (Apply page left column) */
    .spot-mini { list-style: none; margin: 0; padding: 0; display: grid; gap: 4px; }
    .spot-mini li { display: grid; grid-template-columns: 28px 1fr; gap: 12px; align-items: center; padding: 8px 10px; border-radius: 12px; color: rgba(255,255,255,0.72); }
    .spot-mini li.is-current { background: rgba(255,255,255,0.12); color: #fff; }
    .spot-mini-num {
        width: 28px; height: 28px; border-radius: 50%; display: grid; place-items: center;
        font-size: 13px; font-weight: 700; font-variant-numeric: tabular-nums;
        border: 1.5px solid rgba(255,255,255,0.35); color: rgba(255,255,255,0.85);
    }
    .spot-mini li.is-current .spot-mini-num { background: #fff; border-color: #fff; color: #292420; }
    .spot-mini-title { display: block; font-size: 14.5px; font-weight: 700; line-height: 1.3; }
    .spot-mini-text { display: block; font-size: 13px; line-height: 1.4; opacity: 0.8; }
    .spot-mini-note { margin: 12px 0 0; padding: 10px 12px; border-radius: 12px; font-size: 13px; line-height: 1.45; color: #fff; background: rgba(0,0,0,0.22); }
    .spot-mini-note strong { font-weight: 700; }
    .spot-mini-more { display: inline-flex; align-items: center; gap: 6px; margin-top: 12px; font-size: 13.5px; font-weight: 600; color: #fff; text-decoration: underline; text-underline-offset: 4px; text-decoration-color: rgba(255,255,255,0.45); }
    .spot-mini-more:hover { text-decoration-color: #fff; }
    .spot-mini-more svg { width: 14px; height: 14px; }
    .spot-mini .visually-hidden, .spot-mini-more .visually-hidden { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; }
</style>
<ol class="spot-mini">
    @foreach ($spotSteps as $i => [$title])
        @php $n = $i + 1; @endphp
        <li class="{{ $n === $current ? 'is-current' : '' }}" @if($n === $current) aria-current="step" @endif>
            <span class="spot-mini-num" aria-hidden="true">{{ $n }}</span>
            <span>
                <span class="spot-mini-title">{{ $title }}@if($n === $current)<span class="visually-hidden"> (you are here)</span>@endif</span>
                <span class="spot-mini-text">{{ $spotShort[$i] }}</span>
            </span>
        </li>
    @endforeach
</ol>
<p class="spot-mini-note"><strong>Your bed is held only after your first move-in payment is verified.</strong> Until then, someone who pays first can take it.</p>
<a class="spot-mini-more" href="{{ route('home') }}#how-to-apply" target="_blank" rel="noopener">Read the full guide <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8"/></svg><span class="visually-hidden"> (opens in a new tab)</span></a>
@else
<ol class="spot-steps">
    @foreach ($spotSteps as $i => [$title, $text])
        @php $n = $i + 1; $state = $n < $current ? 'is-done' : ($n === $current ? 'is-current' : ''); @endphp
        <li class="{{ $state }}" @if($n === $current) aria-current="step" @endif>
            <span class="spot-num" aria-hidden="true">
                @if ($n < $current)
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                @else
                    {{ $n }}
                @endif
            </span>
            <div class="spot-body">
                <div class="spot-title">
                    {{ $title }}
                    @if ($n === $current)<span class="spot-tag">{{ $current === 1 ? 'You are here' : 'Next' }}</span>@endif
                    @if ($n < $current)<span class="visually-hidden">(done)</span>@endif
                </div>
                <p class="spot-text">{{ $text }}</p>
                @if ($n === 4)
                    <p class="spot-note"><strong>Your bed is not held yet when you apply or get approved.</strong> It is held once your first move-in payment is verified. Until then, another applicant who pays first can take it, so pay as soon as you can.</p>
                @endif
            </div>
        </li>
    @endforeach
</ol>
@endif
