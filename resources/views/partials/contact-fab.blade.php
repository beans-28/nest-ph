{{--
    Floating "Contact us" button (bottom-right) on the dorm's public pages.
    Opens a small menu: inquiry form, plus Facebook / email / call when the
    dorm has filled those in on the Dormitory Profile. Included on Home,
    Rooms and About the Dorm (not VR Tour, Apply or Inquiry).
--}}
@php
    $fabProfile = \App\Models\DormitoryProfile::current();
    $fabPhone = preg_replace('/[^0-9+]/', '', (string) $fabProfile->contact_number);
@endphp
<style>
    .contact-fab { position: fixed; right: clamp(14px, 3vw, 28px); bottom: clamp(14px, 3vw, 28px); z-index: 1500; display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }
    .contact-fab-btn {
        display: inline-flex; align-items: center; gap: 8px; height: 50px; padding: 0 20px 0 16px; border: 0; border-radius: 999px;
        background: #292420; color: #fff; font: inherit; font-size: 15px; font-weight: 600; cursor: pointer;
        /* White ring keeps the dark button visible on dark pages too (About the Dorm) */
        box-shadow: 0 0 0 2px rgba(255,255,255,0.9), 0 2px 4px rgba(0,0,0,0.12), 0 12px 28px -8px rgba(0,0,0,0.45);
        transition: transform .16s cubic-bezier(.23,1,.32,1), background-color .2s ease;
    }
    .contact-fab-btn:hover { background: #3a332e; }
    .contact-fab-btn:active { transform: scale(0.97); }
    .contact-fab-btn:focus-visible { outline: 3px solid #fff; outline-offset: 2px; box-shadow: 0 0 0 6px #292420; }
    .contact-fab-btn svg { width: 20px; height: 20px; }
    .contact-fab-btn .icon-close { display: none; }
    .contact-fab.is-open .contact-fab-btn .icon-chat { display: none; }
    .contact-fab.is-open .contact-fab-btn .icon-close { display: block; }

    .contact-fab-menu {
        width: min(300px, calc(100vw - 28px)); padding: 8px; border-radius: 18px; background: #fff;
        border: 1px solid #ebe6e1; box-shadow: 0 2px 4px rgba(0,0,0,0.06), 0 20px 44px -16px rgba(0,0,0,0.4);
        transform-origin: bottom right;
        transition: opacity .18s cubic-bezier(.23,1,.32,1), transform .18s cubic-bezier(.23,1,.32,1), visibility 0s linear .18s;
        opacity: 0; transform: scale(0.96) translateY(6px); visibility: hidden;
    }
    .contact-fab.is-open .contact-fab-menu { opacity: 1; transform: none; visibility: visible; transition-delay: 0s; }
    .contact-fab-head { padding: 10px 12px 8px; font-size: 13.5px; color: #6b625c; }
    .contact-fab-head strong { display: block; font-size: 15.5px; color: #292420; margin-bottom: 2px; }
    .contact-fab-menu a {
        display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 12px;
        color: #292420; text-decoration: none; font-size: 14.5px; font-weight: 600; line-height: 1.3;
        transition: background-color .15s ease;
    }
    .contact-fab-menu a:hover, .contact-fab-menu a:focus-visible { background: #f4f1ee; outline: none; }
    .contact-fab-menu a small { display: block; font-size: 12.5px; font-weight: 500; color: #7a716b; }
    .contact-fab-ico { flex-shrink: 0; width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; background: #f4f1ee; color: #292420; }
    .contact-fab-ico svg { width: 18px; height: 18px; }
    .contact-fab-ico.fb { background: #e7f0fd; color: #1866d8; }
    @media (prefers-reduced-motion: reduce) { .contact-fab-menu, .contact-fab-btn { transition: none; } }
    @media print { .contact-fab { display: none; } }
</style>

<div class="contact-fab" id="contactFab">
    <div class="contact-fab-menu" id="contactFabMenu" role="menu" aria-labelledby="contactFabBtn">
        <div class="contact-fab-head"><strong>Questions?</strong>Reach {{ $fabProfile->dorm_name ?: 'us' }} any way you like.</div>
        <a href="{{ route('public.inquiry') }}" role="menuitem">
            <span class="contact-fab-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16v11H8l-4 4V5z"/><path d="M8 9h8M8 12h5"/></svg></span>
            <span>Send an inquiry<small>Ask about rooms, rates or a visit</small></span>
        </a>
        @if ($fabProfile->facebook_url)
        <a href="{{ $fabProfile->facebook_url }}" target="_blank" rel="noopener" role="menuitem">
            <span class="contact-fab-ico fb"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 8h3V4h-3c-2.8 0-4.5 1.8-4.5 4.6V11H7v4h2.5v7h4v-7H17l.5-4h-4V8.8c0-.5.3-.8.5-.8z"/></svg></span>
            <span>Message on Facebook<small>{{ $fabProfile->facebook_page_name ?: 'Opens Facebook' }}</small></span>
        </a>
        @endif
        @if ($fabProfile->contact_email)
        <a href="mailto:{{ $fabProfile->contact_email }}" role="menuitem">
            <span class="contact-fab-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg></span>
            <span>Email us<small>{{ $fabProfile->contact_email }}</small></span>
        </a>
        @endif
        @if ($fabPhone)
        <a href="tel:{{ $fabPhone }}" role="menuitem">
            <span class="contact-fab-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg></span>
            <span>Call or text<small>{{ $fabProfile->contact_number }}</small></span>
        </a>
        @endif
    </div>
    <button type="button" class="contact-fab-btn" id="contactFabBtn" aria-haspopup="true" aria-expanded="false" aria-controls="contactFabMenu">
        <svg class="icon-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16v11H8l-4 4V5z"/></svg>
        <svg class="icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
        Contact us
    </button>
</div>
<script>
(function () {
    var fab = document.getElementById('contactFab');
    var btn = document.getElementById('contactFabBtn');
    if (!fab || !btn) return;
    function setOpen(open) {
        fab.classList.toggle('is-open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) { var first = fab.querySelector('.contact-fab-menu a'); if (first) first.focus(); }
    }
    btn.addEventListener('click', function () { setOpen(!fab.classList.contains('is-open')); });
    // A link ending in #contact opens the menu straight away.
    if (location.hash === '#contact') setOpen(true);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && fab.classList.contains('is-open')) { setOpen(false); btn.focus(); }
    });
    document.addEventListener('click', function (e) {
        if (fab.classList.contains('is-open') && !fab.contains(e.target)) setOpen(false);
    });
})();
</script>
