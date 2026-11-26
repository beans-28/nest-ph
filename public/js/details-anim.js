/*
 * Smooth open/close for every <details> dropdown on the page.
 * Browsers normally snap <details> open/shut instantly; this animates the
 * height (and a little fade) instead. Skipped for people who turned on
 * "reduce motion" in their device settings.
 */
(function () {
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)');
    var EASE = 'cubic-bezier(.32,.72,0,1)';

    function setup(details) {
        if (details.dataset.animReady) return;
        details.dataset.animReady = '1';
        var summary = details.querySelector(':scope > summary');
        if (!summary) return;
        var anim = null;

        summary.addEventListener('click', function (e) {
            if (reduce.matches || !details.animate) return; // let the browser do its normal snap
            e.preventDefault();

            var closing = details.open && !details.dataset.closing;
            var startH = details.offsetHeight;
            if (anim) anim.cancel();

            var endH;
            if (closing) {
                details.dataset.closing = '1';
                endH = summary.offsetHeight + parseFloat(getComputedStyle(details).borderTopWidth) + parseFloat(getComputedStyle(details).borderBottomWidth);
            } else {
                delete details.dataset.closing;
                details.open = true;           // open first so we can measure full height
                endH = details.offsetHeight;
            }

            details.style.overflow = 'hidden';
            anim = details.animate(
                { height: [startH + 'px', endH + 'px'] },
                { duration: closing ? 260 : 340, easing: EASE }
            );

            // fade/slide the content in on open
            var body = Array.prototype.filter.call(details.children, function (c) { return c !== summary; });
            if (!closing) {
                body.forEach(function (el) {
                    el.animate(
                        { opacity: [0, 1], transform: ['translateY(-6px)', 'translateY(0)'] },
                        { duration: 300, easing: EASE }
                    );
                });
            }

            anim.onfinish = function () {
                if (closing) details.open = false;
                delete details.dataset.closing;
                details.style.overflow = '';
                anim = null;
            };
        });
    }

    function init() { document.querySelectorAll('details').forEach(setup); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
