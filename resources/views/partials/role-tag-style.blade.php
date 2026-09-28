{{-- Owner / Admin tag shown next to staff names (User::roleTag()).
     Included once per page that shows one. JS pages build the same markup:
     <span class="role-tag role-tag-owner">Owner</span> --}}
<style>
  .role-tag{ display:inline-flex; align-items:center; vertical-align:middle; margin-left:6px; padding:2px 7px; border-radius:20px; font-size:11px; font-weight:700; line-height:1.3; letter-spacing:0.3px; text-transform:uppercase; white-space:nowrap; font-style:normal; }
  .role-tag-owner{ background:#2f6f3c; color:#fff; }
  .role-tag-admin{ background:#d9f2dd; color:#2f6f3c; }
</style>
