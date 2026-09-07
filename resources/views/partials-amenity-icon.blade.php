{{--
    Renders a small line-icon SVG for a given amenity key. Shared by
    admindormitoryprofile.blade.php (the checklist + live preview) and
    publicdorminfo.blade.php (the public listing), so the icon always looks
    the same in both places. Falls back to a generic dot icon for any key
    not in the switch, so a future amenity added directly in the database
    never renders a blank/broken icon.
--}}
@switch($key)
    @case('wifi')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 8.5c5.5-5 14.5-5 20 0"/><path d="M5.5 12c3.5-3 9.5-3 13 0"/><path d="M9 15.5c1.7-1.3 4.3-1.3 6 0"/><circle cx="12" cy="19" r="1" fill="currentColor" stroke="none"/></svg>
        @break
    @case('kitchen')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10h16v2a8 8 0 01-8 8 8 8 0 01-8-8v-2z"/><path d="M4 10V8a2 2 0 012-2h12a2 2 0 012 2v2"/><path d="M9 6V4M15 6V4"/></svg>
        @break
    @case('study_area')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6c-2-1.5-5-2-8-1v13c3-1 6-.5 8 1 2-1.5 5-2 8-1V5c-3-1-6-.5-8 1z"/><path d="M12 6v13"/></svg>
        @break
    @case('parking_area')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 16V8h4a3 3 0 010 6H9"/></svg>
        @break
    @case('cctv')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="13" height="10" rx="2"/><path d="M16 10.5l5-3v9l-5-3z"/><circle cx="8" cy="12" r="2.2"/></svg>
        @break
    @case('hot_cold_shower')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12a8 8 0 0116 0"/><path d="M4 12h16"/><path d="M7 16v2M11 16v3M15 16v2"/></svg>
        @break
    @case('laundry_area')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><circle cx="12" cy="13" r="5"/><circle cx="7" cy="6" r="1" fill="currentColor" stroke="none"/><circle cx="10" cy="6" r="1" fill="currentColor" stroke="none"/></svg>
        @break
    @case('24_7_security')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 5-3.5 8-7 9-3.5-1-7-4-7-9V6l7-3z"/><path d="M9 12l2 2 4-4"/></svg>
        @break
    @default
        <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="4"/></svg>
@endswitch
