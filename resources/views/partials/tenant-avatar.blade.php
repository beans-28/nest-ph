{{--
    Top-bar profile button for tenant pages. Shows the tenant's 2x2 ID
    picture (uploaded on their application) as their profile picture, or
    the usual person icon when they don't have one. A real link, so it
    works with the keyboard and screen readers.
--}}
@php($avatarTenant = auth()->user()?->tenant)
<a class="topbar-icon" href="{{ route('tenant.account') }}" aria-label="My profile" style="text-decoration:none;color:inherit;@if($avatarTenant?->id_photo_url)padding:0;overflow:hidden;@endif">
  @if($avatarTenant?->id_photo_url)
    <img src="{{ $avatarTenant->id_photo_url }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;display:block;">
  @else
    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
  @endif
</a>
