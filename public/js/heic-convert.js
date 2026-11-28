/*
 * Lets people upload iPhone/Mac photos (.heic / .heif).
 *
 * Our server only accepts JPG/PNG/WEBP/PDF, and most browsers can't show
 * HEIC. So when someone picks a HEIC photo, we turn it into a JPG right here
 * in the browser, swap it into the file box, and only then let the page's
 * own code see it. The rest of the site doesn't need to know HEIC exists.
 *
 * - Safari (Mac/iPhone) can read HEIC itself, so it converts instantly.
 * - Other browsers download a small converter (heic2any) only when a HEIC
 *   file is actually picked, so normal page loads stay fast.
 */
(function () {
  const HEIC_TYPES = ',.heic,.heif,image/heic,image/heif';
  const CONVERTER = 'https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js';
  const done = new WeakSet();

  const isHeic = f => /image\/hei[cf]/i.test(f.type) || /\.hei[cf]$/i.test(f.name);
  const acceptsImages = input => /jpe?g|image\/\*/i.test(input.getAttribute('accept') || '');

  // Let HEIC files be picked in image upload boxes (they were greyed out).
  function widen(root) {
    const inputs = root.matches && root.matches('input[type="file"]') ? [root] : root.querySelectorAll('input[type="file"]');
    inputs.forEach(input => {
      const acc = input.getAttribute('accept') || '';
      if (acceptsImages(input) && !/heic/i.test(acc)) input.setAttribute('accept', acc + HEIC_TYPES);
    });
  }

  let converterPromise;
  function loadConverter() {
    if (window.heic2any) return Promise.resolve(window.heic2any);
    converterPromise = converterPromise || new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = CONVERTER;
      s.onload = () => resolve(window.heic2any);
      s.onerror = () => { converterPromise = null; reject(new Error('converter failed to load')); };
      document.head.appendChild(s);
    });
    return converterPromise;
  }

  async function toJpeg(file) {
    let blob;
    try {
      // Safari can decode HEIC itself.
      const bmp = await createImageBitmap(file);
      const c = document.createElement('canvas');
      c.width = bmp.width; c.height = bmp.height;
      c.getContext('2d').drawImage(bmp, 0, 0);
      blob = await new Promise(r => c.toBlob(r, 'image/jpeg', 0.9));
    } catch (e) { /* not supported here; use the converter */ }
    if (!blob) {
      const heic2any = await loadConverter();
      const out = await heic2any({ blob: file, toType: 'image/jpeg', quality: 0.9 });
      blob = Array.isArray(out) ? out[0] : out;
    }
    const name = file.name.replace(/\.hei[cf]$/i, '') + '.jpg';
    return new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() });
  }

  // Runs before the page's own "file chosen" code (capture phase), holds the
  // event back while converting, then fires it again with the JPG in place.
  document.addEventListener('change', async e => {
    const input = e.target;
    if (!(input instanceof HTMLInputElement) || input.type !== 'file' || done.has(e)) return;
    const files = Array.from(input.files || []);
    if (!files.some(isHeic)) return;

    e.stopImmediatePropagation();
    input.disabled = true;
    input.dataset.converting = '1';
    try {
      const converted = await Promise.all(files.map(f => isHeic(f) ? toJpeg(f) : f));
      const dt = new DataTransfer();
      converted.forEach(f => dt.items.add(f));
      input.files = dt.files;
    } catch (err) {
      input.value = '';
      alert('Sorry, we couldn’t read that iPhone photo (HEIC). Please choose a JPG or PNG instead, or take a screenshot of it and upload that.');
    } finally {
      input.disabled = false;
      delete input.dataset.converting;
    }
    const again = new Event('change', { bubbles: true });
    done.add(again);
    input.dispatchEvent(again);
    input.dispatchEvent(new Event('input', { bubbles: true }));
  }, true);

  // For drag-and-drop areas: hand a dropped HEIC to the file box so it gets
  // converted the same way. Returns true if it took the file.
  window.NestHeic = {
    isHeic,
    routeDrop(file, input) {
      if (!file || !isHeic(file) || !input) return false;
      const dt = new DataTransfer();
      dt.items.add(file);
      input.files = dt.files;
      input.dispatchEvent(new Event('change', { bubbles: true }));
      return true;
    },
  };

  // Also catch inputs added later (modals, dynamic forms).
  function start() {
    widen(document);
    new MutationObserver(muts => muts.forEach(m => m.addedNodes.forEach(n => { if (n.nodeType === 1) widen(n); })))
      .observe(document.body, { childList: true, subtree: true });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
  else start();
})();
