{{--
  Full-screen viewer for a payment QR code, shared by the tenant billing
  and move-in payment screens. Any element with data-qr-src opens it
  (data-qr-name labels it and names the downloaded file).

  Uploaded QR images are often whole screenshots with the code itself a
  small part of them, so the image fills the screen and can be zoomed:
  buttons, mouse wheel, pinch, double-click/tap, and drag to pan.
--}}
<style>
  .qr-lightbox{ position:fixed; inset:0; z-index:1000; background:rgba(12,20,15,.92); display:none; flex-direction:column; }
  .qr-lightbox.open{ display:flex; }
  .qr-lightbox-bar{ display:flex; align-items:center; gap:10px; padding:12px 16px; color:#fff; flex-shrink:0; }
  .qr-lightbox-title{ font-size:15px; font-weight:700; margin:0; flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#fff; }
  .qr-lightbox-btn{ display:inline-flex; align-items:center; justify-content:center; gap:7px; min-width:40px; height:40px; padding:0 12px; border-radius:9px; border:1px solid rgba(255,255,255,.25); background:rgba(255,255,255,.1); color:#fff; font:inherit; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; flex-shrink:0; }
  .qr-lightbox-btn:hover{ background:rgba(255,255,255,.2); }
  .qr-lightbox-btn:focus-visible{ outline:2px solid #fff; outline-offset:2px; }
  .qr-lightbox-btn.primary{ background:#2f6b3a; border-color:#2f6b3a; }
  .qr-lightbox-btn.primary:hover{ background:#255a2f; }
  .qr-lightbox-btn svg{ width:18px; height:18px; }
  .qr-lightbox-zoom-level{ font-size:12.5px; font-variant-numeric:tabular-nums; min-width:46px; text-align:center; opacity:.85; }
  .qr-lightbox-stage{ flex:1; position:relative; overflow:hidden; touch-action:none; cursor:grab; }
  .qr-lightbox-stage.dragging{ cursor:grabbing; }
  /* Always fills the viewer, even for a small upload */
  .qr-lightbox-img{ position:absolute; top:50%; left:50%; width:calc(100% - 32px); height:calc(100% - 32px); object-fit:contain; transform-origin:center center; user-select:none; -webkit-user-drag:none; will-change:transform; }
  .qr-lightbox-hint{ text-align:center; color:rgba(255,255,255,.7); font-size:12px; padding:8px 16px 14px; flex-shrink:0; }
  @media (max-width:640px){
    .qr-lightbox-btn .label{ display:none; }
    .qr-lightbox-zoom-level{ display:none; }
  }
  [data-qr-src]{ cursor:zoom-in; }
  .qr-tap-hint{ display:block; font-size:10.5px; margin-top:6px; opacity:.85; }
</style>

<div class="qr-lightbox" id="qrLightbox" role="dialog" aria-modal="true" aria-labelledby="qrLightboxTitle">
  <div class="qr-lightbox-bar">
    <h2 class="qr-lightbox-title" id="qrLightboxTitle">QR Code</h2>
    <button type="button" class="qr-lightbox-btn" id="qrZoomOut" aria-label="Zoom out"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3M8 11h6"/></svg></button>
    <span class="qr-lightbox-zoom-level" id="qrZoomLevel" aria-live="polite">100%</span>
    <button type="button" class="qr-lightbox-btn" id="qrZoomIn" aria-label="Zoom in"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3M8 11h6M11 8v6"/></svg></button>
    <button type="button" class="qr-lightbox-btn" id="qrZoomReset" aria-label="Fit to screen"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg></button>
    <a class="qr-lightbox-btn primary" id="qrLightboxDownload" href="#" download>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
      <span class="label">Download</span>
    </a>
    <button type="button" class="qr-lightbox-btn" id="qrLightboxClose" aria-label="Close"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
  </div>
  <div class="qr-lightbox-stage" id="qrLightboxStage">
    <img class="qr-lightbox-img" id="qrLightboxImg" alt="" draggable="false">
  </div>
  <div class="qr-lightbox-hint">Pinch, scroll, or double-tap to zoom. Drag to move around.</div>
</div>

<script>
(function(){
  const box = document.getElementById('qrLightbox');
  const stage = document.getElementById('qrLightboxStage');
  const img = document.getElementById('qrLightboxImg');
  const MIN = 1, MAX = 8;
  let scale = 1, x = 0, y = 0, lastFocus = null;
  const pointers = new Map();
  let pinchStart = null, dragStart = null, lastTap = 0;

  function apply(){
    img.style.transform = `translate(calc(-50% + ${x}px), calc(-50% + ${y}px)) scale(${scale})`;
    document.getElementById('qrZoomLevel').textContent = Math.round(scale * 100) + '%';
  }

  // Keep the image from being panned entirely off screen.
  function clampPan(){
    const r = stage.getBoundingClientRect();
    const maxX = Math.max(0, (img.offsetWidth * scale - r.width) / 2 + 40);
    const maxY = Math.max(0, (img.offsetHeight * scale - r.height) / 2 + 40);
    x = Math.min(maxX, Math.max(-maxX, x));
    y = Math.min(maxY, Math.max(-maxY, y));
  }

  // Zoom toward a point on screen (cx, cy), so what's under the cursor or
  // fingers stays put.
  function zoomTo(next, cx, cy){
    next = Math.min(MAX, Math.max(MIN, next));
    const r = stage.getBoundingClientRect();
    const px = (cx ?? r.left + r.width / 2) - (r.left + r.width / 2);
    const py = (cy ?? r.top + r.height / 2) - (r.top + r.height / 2);
    x = px - (px - x) * (next / scale);
    y = py - (py - y) * (next / scale);
    scale = next;
    if(scale === 1){ x = 0; y = 0; }
    clampPan();
    apply();
  }

  function reset(){ scale = 1; x = 0; y = 0; apply(); }

  function open(src, name){
    const label = name ? `${name} QR Code` : 'QR Code';
    document.getElementById('qrLightboxTitle').textContent = label;
    img.src = src;
    img.alt = label;
    const ext = (src.split('?')[0].match(/\.(png|jpe?g|webp)$/i) || [, 'png'])[1];
    const link = document.getElementById('qrLightboxDownload');
    link.href = src;
    link.download = `${(name || 'payment').replace(/[^A-Za-z0-9]+/g, '-').toLowerCase()}-qr.${ext}`;
    reset();
    lastFocus = document.activeElement;
    box.classList.add('open');
    document.body.style.overflow = 'hidden';
    document.getElementById('qrLightboxClose').focus();
  }
  function close(){
    box.classList.remove('open');
    document.body.style.overflow = '';
    pointers.clear();
    if(lastFocus) lastFocus.focus();
  }

  document.getElementById('qrZoomIn').addEventListener('click', () => zoomTo(scale * 1.5));
  document.getElementById('qrZoomOut').addEventListener('click', () => zoomTo(scale / 1.5));
  document.getElementById('qrZoomReset').addEventListener('click', reset);
  document.getElementById('qrLightboxClose').addEventListener('click', close);

  stage.addEventListener('wheel', (e) => {
    e.preventDefault();
    zoomTo(scale * (e.deltaY < 0 ? 1.2 : 1 / 1.2), e.clientX, e.clientY);
  }, { passive: false });

  stage.addEventListener('dblclick', (e) => zoomTo(scale > 1 ? 1 : 3, e.clientX, e.clientY));

  stage.addEventListener('pointerdown', (e) => {
    stage.setPointerCapture(e.pointerId);
    pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
    if(pointers.size === 2){
      const [a, b] = [...pointers.values()];
      pinchStart = { dist: Math.hypot(a.x - b.x, a.y - b.y), scale };
      dragStart = null;
    } else if(pointers.size === 1){
      dragStart = { px: e.clientX, py: e.clientY, x, y };
      // Double-tap to zoom on touch screens (dblclick is unreliable there).
      if(e.pointerType === 'touch'){
        const now = Date.now();
        if(now - lastTap < 300){ zoomTo(scale > 1 ? 1 : 3, e.clientX, e.clientY); dragStart = null; }
        lastTap = now;
      }
    }
  });
  stage.addEventListener('pointermove', (e) => {
    if(!pointers.has(e.pointerId)) return;
    pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
    if(pointers.size === 2 && pinchStart){
      const [a, b] = [...pointers.values()];
      const dist = Math.hypot(a.x - b.x, a.y - b.y);
      zoomTo(pinchStart.scale * (dist / pinchStart.dist), (a.x + b.x) / 2, (a.y + b.y) / 2);
    } else if(dragStart && scale > 1){
      stage.classList.add('dragging');
      x = dragStart.x + (e.clientX - dragStart.px);
      y = dragStart.y + (e.clientY - dragStart.py);
      clampPan();
      apply();
    }
  });
  function endPointer(e){
    pointers.delete(e.pointerId);
    if(pointers.size < 2) pinchStart = null;
    if(pointers.size === 1){
      const [p] = [...pointers.values()];
      dragStart = { px: p.x, py: p.y, x, y };
    } else if(pointers.size === 0){
      dragStart = null;
      stage.classList.remove('dragging');
    }
  }
  stage.addEventListener('pointerup', endPointer);
  stage.addEventListener('pointercancel', endPointer);

  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-qr-src]');
    if(trigger && trigger.dataset.qrSrc) open(trigger.dataset.qrSrc, trigger.dataset.qrName);
  });
  document.addEventListener('keydown', (e) => {
    if(box.classList.contains('open')){
      if(e.key === 'Escape') close();
      else if(e.key === '+' || e.key === '=') zoomTo(scale * 1.5);
      else if(e.key === '-') zoomTo(scale / 1.5);
      else if(e.key === '0') reset();
      return;
    }
    const trigger = e.target.closest && e.target.closest('[data-qr-src]');
    if(trigger && (e.key === 'Enter' || e.key === ' ') && trigger.dataset.qrSrc){ e.preventDefault(); open(trigger.dataset.qrSrc, trigger.dataset.qrName); }
  });
})();
</script>
