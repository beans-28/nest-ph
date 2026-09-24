{{--
    Move-in progress indicator. Pass the current step (1–4), or 5 once
    everything is submitted:
        @include('partials.movein-steps', ['step' => 2])
--}}
@php
    $moveInSteps = ['Approved', 'Payment type', 'Method', 'Proof of payment'];
@endphp
<ol class="movein-steps" aria-label="Move-in progress">
    @foreach($moveInSteps as $i => $label)
        <li class="{{ $i + 1 < $step ? 'done' : '' }}" @if($i + 1 === $step) aria-current="step" @endif>
            {{ $label }}@if($i + 1 < $step)<span class="visually-hidden"> (completed)</span>@endif
        </li>
    @endforeach
</ol>
