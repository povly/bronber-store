/**
 * Scrollable Plugin
 *
 * Alpine.js directive `x-scrollable` that replaces the native scrollbar with
 * a custom-styled, cross-browser-identical scrollbar overlay.
 *
 * Why: CSS-only scrollbar styling is inconsistent across browsers
 * (Firefox can't do border-radius on thumb, limited widths, no per-state
 * colors). This directive provides a single, pixel-perfect look everywhere.
 *
 * Usage:
 *   <ul x-scrollable>...</ul>
 *   <div x-scrollable="{ thumbColor: '#7212bc', thumbWidth: 6 }">...</div>
 *
 * Element MUST have `overflow-y: auto` and a `max-height` set via CSS
 * (the directive never imposes dimensions — only the visual scrollbar).
 *
 * Config (via expression object, all optional):
 *   - thumbColor   (string, default '#bfbfbf')
 *   - thumbWidth   (number, default 4)
 *   - thumbRadius  (number, default 4)
 *   - trackOffset  (number, default 2) — gap from the right edge
 *   - minThumbSize (number, default 24) — keeps thumb grabbable on long content
 *   - autoHide     (bool,   default false) — fade thumb when idle
 *   - fadeDelay    (number, default 800) — ms before autoHide fades
 *
 * DOM transformation:
 *   <ul x-scrollable>...</ul>
 *   becomes:
 *   <div class="x-scrollable__wrapper">
 *     <ul class="x-scrollable">...</ul>
 *     <div class="x-scrollable__track"><div class="x-scrollable__thumb"></div></div>
 *   </div>
 *
 * Cleanup unwraps the element and restores original state.
 */

const DEFAULTS = {
    thumbColor: '#bfbfbf',
    thumbWidth: 4,
    thumbRadius: 4,
    trackOffset: 2,
    minThumbSize: 24,
    autoHide: false,
    fadeDelay: 800,
};

export default function (Alpine) {
    Alpine.directive('scrollable', (el, { expression }, { cleanup, evaluate, effect }) => {
        let opts = { ...DEFAULTS };

        const applyConfig = (userOpts) => {
            if (!userOpts || typeof userOpts !== 'object') return;
            opts = { ...opts, ...userOpts };
            wrapper.style.setProperty('--x-scroll-thumb-width', `${opts.thumbWidth}px`);
            wrapper.style.setProperty('--x-scroll-thumb-color', opts.thumbColor);
            wrapper.style.setProperty('--x-scroll-thumb-radius', `${opts.thumbRadius}px`);
            wrapper.style.setProperty('--x-scroll-track-offset', `${opts.trackOffset}px`);
        };

        // --- Ensure scrollable & positioned context for track ---
        const computedStyle = getComputedStyle(el);
        if (computedStyle.overflowY !== 'auto' && computedStyle.overflowY !== 'scroll') {
            el.style.overflowY = 'auto';
        }
        el.classList.add('x-scrollable');

        // --- Wrap element so track can sit as an absolute sibling ---
        const wrapper = document.createElement('div');
        wrapper.className = 'x-scrollable__wrapper';
        el.parentNode.insertBefore(wrapper, el);
        wrapper.appendChild(el);

        const track = document.createElement('div');
        track.className = 'x-scrollable__track';
        const thumb = document.createElement('div');
        thumb.className = 'x-scrollable__thumb';
        track.appendChild(thumb);
        wrapper.appendChild(track);

        // --- Apply initial config (and watch expression for reactive updates) ---
        if (expression) {
            try {
                applyConfig(evaluate(expression));
            } catch (e) {
                // ignore — element may not have Alpine scope yet
            }
            effect(() => {
                try {
                    applyConfig(evaluate(expression));
                    update();
                } catch (e) {
                    // ignore reactive evaluation errors
                }
            });
        } else {
            applyConfig({});
        }

        // --- State ---
        let isDragging = false;
        let dragStartY = 0;
        let dragStartScrollTop = 0;
        let fadeTimer = null;

        const computeMetrics = () => {
            const { clientHeight, scrollHeight, scrollTop } = el;
            const contentHeight = Math.max(scrollHeight, 1);
            const visibleRatio = Math.min(1, clientHeight / contentHeight);
            const thumbHeight = Math.max(opts.minThumbSize, clientHeight * visibleRatio);
            const maxThumbTop = Math.max(0, clientHeight - thumbHeight);
            const maxScrollTop = Math.max(0, scrollHeight - clientHeight);
            return { clientHeight, scrollHeight, scrollTop, thumbHeight, maxThumbTop, maxScrollTop };
        };

        const update = () => {
            const m = computeMetrics();
            const needsScroll = m.maxScrollTop > 1;
            track.classList.toggle('is-hidden', !needsScroll);
            if (!needsScroll) return;
            const scrollRatio = m.maxScrollTop > 0 ? m.scrollTop / m.maxScrollTop : 0;
            const thumbTop = m.maxThumbTop * Math.min(1, Math.max(0, scrollRatio));
            thumb.style.height = `${m.thumbHeight}px`;
            thumb.style.transform = `translateY(${thumbTop}px)`;
        };

        const show = () => {
            if (!opts.autoHide) return;
            track.classList.remove('is-faded');
            clearTimeout(fadeTimer);
            fadeTimer = setTimeout(() => {
                if (!isDragging) track.classList.add('is-faded');
            }, opts.fadeDelay);
        };

        // --- Scroll sync ---
        const onScroll = () => {
            update();
            show();
        };
        el.addEventListener('scroll', onScroll, { passive: true });

        // --- Resize observers: element + direct children ---
        const elementRO = new ResizeObserver(() => update());
        elementRO.observe(el);

        const childRO = new ResizeObserver(() => update());
        const observedChildren = new Set();
        const observeChildren = () => {
            for (const child of el.children) {
                if (observedChildren.has(child)) continue;
                observedChildren.add(child);
                childRO.observe(child);
            }
        };
        observeChildren();

        // --- Mutation observer: added/removed children, class/style/hidden changes ---
        const mo = new MutationObserver((mutations) => {
            for (const mut of mutations) {
                mut.addedNodes.forEach((n) => {
                    if (n.nodeType !== 1 || n === track || n === wrapper) return;
                    observedChildren.add(n);
                    childRO.observe(n);
                });
                mut.removedNodes.forEach((n) => {
                    if (n.nodeType !== 1 || !observedChildren.has(n)) return;
                    observedChildren.delete(n);
                    childRO.unobserve(n);
                });
            }
            update();
        });
        mo.observe(el, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style', 'class', 'hidden'],
        });

        // --- Thumb drag (pointer events: covers mouse + touch + pen) ---
        const onPointerMove = (e) => {
            if (!isDragging) return;
            const m = computeMetrics();
            if (m.maxThumbTop === 0) return;
            const dy = e.clientY - dragStartY;
            const scrollDelta = (dy / m.maxThumbTop) * m.maxScrollTop;
            el.scrollTop = dragStartScrollTop + scrollDelta;
        };

        const onPointerUp = () => {
            if (!isDragging) return;
            isDragging = false;
            thumb.classList.remove('is-dragging');
            document.body.style.userSelect = '';
            document.removeEventListener('pointermove', onPointerMove);
            document.removeEventListener('pointerup', onPointerUp);
            show();
        };

        const onThumbPointerDown = (e) => {
            if (e.button && e.button !== 0) return;
            e.preventDefault();
            e.stopPropagation();
            isDragging = true;
            dragStartY = e.clientY;
            dragStartScrollTop = el.scrollTop;
            thumb.classList.add('is-dragging');
            document.body.style.userSelect = 'none';
            document.addEventListener('pointermove', onPointerMove);
            document.addEventListener('pointerup', onPointerUp);
            show();
        };
        thumb.addEventListener('pointerdown', onThumbPointerDown);

        // --- Click on track (jump, like native) ---
        const onTrackPointerDown = (e) => {
            if (e.target === thumb) return;
            e.preventDefault();
            const rect = track.getBoundingClientRect();
            const clickY = e.clientY - rect.top;
            const m = computeMetrics();
            const targetThumbTop = clickY - m.thumbHeight / 2;
            const clamped = Math.max(0, Math.min(targetThumbTop, m.maxThumbTop));
            const scrollRatio = m.maxThumbTop > 0 ? clamped / m.maxThumbTop : 0;
            el.scrollTop = scrollRatio * m.maxScrollTop;
        };
        track.addEventListener('pointerdown', onTrackPointerDown);

        // --- Hover state (visual cue + cancels autoHide fade) ---
        const onEnter = () => { track.classList.add('is-hovered'); show(); };
        const onLeave = () => { track.classList.remove('is-hovered'); if (!isDragging) show(); };
        wrapper.addEventListener('mouseenter', onEnter);
        wrapper.addEventListener('mouseleave', onLeave);

        // --- Initial paint (after layout settles) ---
        const rafId = requestAnimationFrame(() => {
            update();
            show();
        });

        // --- Cleanup ---
        cleanup(() => {
            cancelAnimationFrame(rafId);
            clearTimeout(fadeTimer);
            elementRO.disconnect();
            childRO.disconnect();
            mo.disconnect();
            el.removeEventListener('scroll', onScroll);
            thumb.removeEventListener('pointerdown', onThumbPointerDown);
            track.removeEventListener('pointerdown', onTrackPointerDown);
            wrapper.removeEventListener('mouseenter', onEnter);
            wrapper.removeEventListener('mouseleave', onLeave);
            document.removeEventListener('pointermove', onPointerMove);
            document.removeEventListener('pointerup', onPointerUp);

            track.remove();
            observedChildren.clear();

            if (wrapper.parentNode) {
                wrapper.parentNode.insertBefore(el, wrapper);
                wrapper.remove();
            }
            el.classList.remove('x-scrollable');
            el.style.removeProperty('overflow-y');
        });
    });
}
