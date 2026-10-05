{{--
  Testing Tools panel for demos (DemoToolsController). Only shown when
  config('app.demo_tools') is on. Pass:
    $tool    'reservations' | 'applications' | 'tickets' | 'escalation'
    $hint    one sentence on what the tools do
    $actions [['key' => ..., 'label' => ..., 'needsItem' => bool], ...]
    $title   optional toggle label (default "Testing Tools")
--}}
@if (config('app.demo_tools'))
<section class="demo-tools" data-demo-tool="{{ $tool }}" aria-label="Testing tools">
  <button type="button" class="demo-tools-toggle" aria-expanded="false">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
    <span>{{ $title ?? 'Testing Tools' }}</span>
  </button>
  <div class="demo-tools-panel" hidden>
    <p class="demo-tools-hint">{{ $hint }}</p>
    @if (collect($actions)->contains('needsItem', true))
      <select class="demo-tools-select" aria-label="Record to change"><option value="">Loading...</option></select>
    @endif
    <div class="demo-tools-actions">
      @foreach ($actions as $a)
        <button type="button" class="demo-tools-btn{{ $a['needsItem'] ? '' : ' is-secondary' }}" data-action="{{ $a['key'] }}" data-needs-item="{{ $a['needsItem'] ? '1' : '0' }}">{{ $a['label'] }}</button>
      @endforeach
    </div>
    <p class="demo-tools-status" role="status" aria-live="polite"></p>
  </div>
</section>

@once
<style>
  .demo-tools{ margin-top:18px; }
  .demo-tools-toggle{ display:inline-flex; align-items:center; gap:6px; font:600 12.5px/1 inherit; color:#243026; background:none; border:none; cursor:pointer; padding:6px 2px; }
  .demo-tools-toggle svg{ width:13px; height:13px; transition:transform .2s ease; }
  .demo-tools-toggle[aria-expanded="true"] svg{ transform:rotate(90deg); }
  .demo-tools-panel{ margin-top:8px; background:#fff; border:1px dashed #c9d2ca; border-radius:10px; padding:16px; }
  .demo-tools-hint{ font-size:12.5px; color:#5b6b60; margin:0 0 12px; line-height:1.5; max-width:70ch; }
  .demo-tools-select{ width:100%; max-width:480px; padding:8px 10px; border:1px solid #e2e6e2; border-radius:6px; font:13px inherit; color:#243026; background:#fff; margin-bottom:12px; }
  .demo-tools-actions{ display:flex; gap:8px; flex-wrap:wrap; }
  .demo-tools-btn{ padding:8px 14px; border-radius:6px; border:1px solid #197335; background:#197335; color:#fff; font:600 12.5px inherit; cursor:pointer; }
  .demo-tools-btn.is-secondary{ background:#fff; color:#197335; }
  .demo-tools-btn:disabled{ opacity:.55; cursor:wait; }
  .demo-tools-btn:focus-visible, .demo-tools-toggle:focus-visible, .demo-tools-select:focus-visible{ outline:2px solid #197335; outline-offset:2px; }
  .demo-tools-status{ font-size:12.5px; color:#5b6b60; margin:10px 0 0; min-height:1em; white-space:pre-line; }
  .demo-tools-status.is-error{ color:#b3261e; }
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
  document.querySelectorAll('.demo-tools').forEach(root => {
    const tool = root.dataset.demoTool;
    const toggle = root.querySelector('.demo-tools-toggle');
    const panel = root.querySelector('.demo-tools-panel');
    const select = root.querySelector('.demo-tools-select');
    const status = root.querySelector('.demo-tools-status');
    let loaded = false;

    const say = (msg, isError) => { status.textContent = msg; status.classList.toggle('is-error', !!isError); };

    async function load() {
      if (!select) return;
      try {
        const res = await fetch(`/demo-tools/${tool}/items`, { headers: { Accept: 'application/json' } });
        const body = await res.json();
        select.replaceChildren();
        if (!body.items.length) {
          select.add(new Option('Nothing to change right now', ''));
          return;
        }
        body.items.forEach(i => select.add(new Option(i.label, i.id)));
      } catch (e) {
        say('Could not load records. ' + e.message, true);
      }
    }

    toggle.addEventListener('click', () => {
      const open = toggle.getAttribute('aria-expanded') !== 'true';
      toggle.setAttribute('aria-expanded', open);
      panel.hidden = !open;
      if (open && !loaded) { loaded = true; load(); }
    });

    root.querySelectorAll('.demo-tools-btn').forEach(btn => btn.addEventListener('click', async () => {
      const id = select?.value;
      if (btn.dataset.needsItem === '1' && !id) { say('Pick a record first.', true); return; }
      btn.disabled = true;
      say('Working...');
      try {
        const res = await fetch(`/demo-tools/${tool}/${btn.dataset.action}`, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json', 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id ? Number(id) : null }),
        });
        const body = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(body.message || `Server returned status ${res.status}.`);
        say(body.message + '\nReloading the page...');
        setTimeout(() => location.reload(), 1200);
      } catch (e) {
        say(e.message, true);
        btn.disabled = false;
      }
    }));
  });
});
</script>
@endonce
@endif
