{{-- Shared ticket colours (admin Tickets, tenant Tickets, admin dashboard).
     One rule keeps them readable at a glance:
       - TIME (overdue / due soon) is plain text with an icon and a left
         bar, never a pill, so it can't be mistaken for a priority.
       - PRIORITY is colour-coded pills: Critical red, High orange,
         Medium blue, Low grey.
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

  .prio{ display:inline-flex; align-items:center; font-size:11.5px; font-weight:600; line-height:1.3; padding:3px 10px; border-radius:20px; white-space:nowrap; }
  .prio.critical{ background:#fde7e7; color:#b42318; }
  .prio.high{ background:#fdeedd; color:#9a4a0a; }
  .prio.medium{ background:#e3ecf7; color:#2a5a99; }
  .prio.low{ background:#eceeec; color:#4a524e; }

  .due{ font-size:12.5px; color:#5d6661; }
  .due.late, .due.soon{ display:inline-flex; align-items:center; gap:5px; font-weight:700; padding-left:8px; border-left:3px solid currentColor; }
  .due.late{ color:var(--tk-late); }
  .due.late::before{ content:'\26A0'; font-size:13px; }
  .due.soon{ color:var(--tk-soon); }
  .due.soon::before{ content:'\23F1'; font-size:13px; }
</style>
