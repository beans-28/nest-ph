{{--
    Shared public top navigation. Included via:
        @include('partials.public-nav')

    Used by welcome, publicrooms, publicdorminfo, publicvr, publicinquiry,
    publicapply, loginadmin, logintenant and passwords. Each page keeps its
    own .topnav desktop styles in its <style> block; this partial only adds
    the compact phone layout below, which loads later and so wins.

    Phones (860px and below): one slim row with the logo, an Apply button
    and a Menu button. Menu opens the page links and Log In / Admin
    underneath, inside the same sticky bar.
--}}
<style>
    .topnav .nav-toggle, .topnav .nav-apply { display: none; }
    /* The logo is a link now; some pages' own styles don't remove the link underline */
    .topnav .logo, .topnav .logo:hover { text-decoration: none; color: #fff; }
    .topnav .menu a[aria-current="page"] { text-decoration: underline; text-underline-offset: 6px; text-decoration-thickness: 2px; }
    /* Current page on the pill link: filled white instead of underlined (desktop; the phone menu has no pill shape) */
    @media (min-width: 861px) {
        .topnav .menu a.pill[aria-current="page"] {
            text-decoration: none; background: #fff; border-color: #fff; color: var(--green-darker, #197335);
        }
    }

    @media (max-width: 860px) {
        .topnav {
            display: grid; grid-template-columns: 1fr auto auto; align-items: center;
            column-gap: 8px; row-gap: 0; padding: 10px 16px;
        }
        .topnav .logo { grid-column: 1; grid-row: 1; order: 0; justify-content: flex-start; font-size: 17px; }
        .topnav .logo .logo-img { height: 26px; }
        .topnav .nav-apply {
            display: inline-flex; grid-column: 2; grid-row: 1;
            height: 38px; padding: 0 16px; font-size: 13px;
        }
        .topnav .nav-toggle {
            display: inline-flex; grid-column: 3; grid-row: 1; align-items: center; gap: 6px;
            height: 38px; padding: 0 12px; border: 1px solid rgba(255,255,255,0.55); border-radius: 8px;
            background: transparent; color: #fff; font: inherit; font-size: 13px; font-weight: 500; cursor: pointer;
        }
        .topnav .nav-toggle svg { width: 18px; height: 18px; }
        .topnav .nav-toggle .icon-close { display: none; }
        .topnav.menu-open .nav-toggle .icon-open { display: none; }
        .topnav.menu-open .nav-toggle .icon-close { display: block; }
        .topnav .nav-toggle:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }

        /* Collapsed by default; the Menu button reveals both groups */
        .topnav .menu, .topnav .buttons { display: none; grid-column: 1 / -1; }
        .topnav.menu-open .menu {
            display: flex; flex-direction: column; align-items: stretch; gap: 2px;
            flex-wrap: nowrap; margin-top: 10px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.18);
        }
        .topnav.menu-open .menu a, .topnav.menu-open .menu a.pill {
            padding: 12px 4px; font-size: 15px; border: none; border-radius: 0; justify-content: flex-start;
        }
        .topnav.menu-open .buttons {
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 8px 0 6px;
        }
        .topnav.menu-open .buttons .btn { width: 100%; }
        .topnav .buttons .nav-apply-full { display: none; }
    }
    @media (min-width: 861px) {
        .topnav .buttons .nav-apply-full { display: inline-flex; }
    }
</style>

<nav class="topnav textured" aria-label="Main">
    <img src="{{ asset('images/leaf-texture-2.png') }}" class="bg-texture" alt="">
    <div class="menu" id="publicNavMenu">
        <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>HOME</a>
        <a href="{{ route('public.rooms') }}" @if(request()->routeIs('public.rooms')) aria-current="page" @endif>ROOMS</a>
        <a href="{{ route('public.vr') }}" @if(request()->routeIs('public.vr')) aria-current="page" @endif>VR TOUR</a>
        <a href="{{ route('public.dorminfo') }}" class="pill" @if(request()->routeIs('public.dorminfo')) aria-current="page" @endif>About the Dorm</a>
    </div>
    <a href="{{ route('home') }}" class="logo"><img src="{{ asset('images/nestph.png') }}" alt="" class="logo-img"> NEST.PH</a>
    <a href="{{ route('public.apply') }}" class="btn btn-white nav-apply">Apply</a>
    <button type="button" class="nav-toggle" id="publicNavToggle" aria-expanded="false" aria-controls="publicNavMenu">
        <svg class="icon-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        <svg class="icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
        Menu
    </button>
    <div class="buttons">
        <a href="{{ route('public.apply') }}" class="btn btn-white nav-apply-full">Apply</a>
        <a href="{{ route('login.tenant') }}" class="btn btn-white">Log In</a>
        <a href="{{ route('login.admin') }}" class="btn btn-outline-white">Admin</a>
    </div>
</nav>

<script>
(function () {
    var nav = document.querySelector('.topnav');
    var toggle = document.getElementById('publicNavToggle');
    if (!nav || !toggle) return;

    function setOpen(open) {
        nav.classList.toggle('menu-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    toggle.addEventListener('click', function () { setOpen(!nav.classList.contains('menu-open')); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && nav.classList.contains('menu-open')) { setOpen(false); toggle.focus(); }
    });
    document.addEventListener('click', function (e) {
        if (nav.classList.contains('menu-open') && !nav.contains(e.target)) setOpen(false);
    });
    window.matchMedia('(min-width: 861px)').addEventListener('change', function (e) { if (e.matches) setOpen(false); });
})();
</script>
