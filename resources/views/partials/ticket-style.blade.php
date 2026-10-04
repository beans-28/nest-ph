{{-- Shared ticket colours (admin Tickets, tenant Tickets, admin dashboard).
     One rule keeps them readable at a glance:
       - TIME (overdue / due soon) uses colour: red and amber.
       - PRIORITY uses shape and weight, not those colours, so "important"
         and "late" can never be confused.
     Markup: <span class="prio critical">Critical</span>
             <span class="due late">Response overdue</span>
     All text colours checked at >= 4.5:1 on their backgrounds. --}}
<style>
  :root{
    --tk-ink:#292420;
    --tk-late:#b3261e;   --tk-late-bg:#fdf1f0;
    --tk-soon:#7a4508;   --tk-soon-bg:#fcf0dc;
    --tk-closed:#5d6661; --tk-closed-bg:#eceeec;
    --tk-pending:#33629e; --tk-pending-bg:#e3ecf7;
  }

  .prio{ display:inline-flex; align-items:center; gap:6px; font-size:11.5px; font-weight:600; line-height:1.3; padding:3px 9px; border-radius:20px; white-space:nowrap; border:1px solid transparent; }
  .prio.critical{ background:var(--tk-ink); color:#fff; font-weight:700; }
  .prio.critical::before{ content:''; width:7px; height:7px; border-radius:50%; background:#ff7a6e; }
  .prio.high{ border-color:var(--tk-ink); color:var(--tk-ink); font-weight:700; }
  .prio.medium{ border-color:#c1c7cd; color:#4a524e; }
  .prio.low{ color:#5d6661; background:#f2f4f3; }

  .due{ font-size:12.5px; color:#5d6661; }
  .due.late{ color:var(--tk-late); font-weight:700; }
  .due.soon{ color:var(--tk-soon); font-weight:700; }
</style>
