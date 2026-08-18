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
 *   - orientation  (string, default 'vertical') — 'vertical' | 'horizontal'
 *   - thumbColor   (string, default '#bfbfbf')
 *   - thumbWidth   (number, default 4) — track thickness on the scroll axis
 *   - thumbRadius  (number, default 4)
 *   - trackOffset  (number, default 2) — gap from the right/bottom edge
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
    orientation: 'vertical',
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

        // --- Peek expression once up front: orientation must be known before
        //     forcing overflow axis and tagging the wrapper (do not remove).
        if (expression) {
            try {
                const userOpts = evaluate(expression);
                if (userOpts && typeof userOpts === 'object') {
                    opts = { ...opts, ...userOpts };
                }
            } catch (e) {
                // ignore — element may not have Alpine scope yet
            }
        }

        // --- Ensure scrollable & positioned context for track ---
        const computedStyle = getComputedStyle(el);
        const axis = opts.orientation === 'horizontal' ? 'overflowX' : 'overflowY';
        if (computedStyle[axis] !== 'auto' && computedStyle[axis] !== 'scroll') {
            el.style[axis] = 'auto';
        }
        el.classList.add('x-scrollable');

        // --- Wrap element so track can sit as an absolute sibling ---
        const wrapper = document.createElement('div');
        wrapper.className = 'x-scrollable__wrapper';
        if (opts.orientation === 'horizontal') {
            wrapper.classList.add('is-horizontal');
        }
        el.parentNode.insertBefore(wrapper, el);
        wrapper.appendChild(el);

        const track = document.createElement('div');
        // is-pending держит трек невидимым до первого update():
        // иначе между созданием и первым расчётом метрик виден флеш тумба
        track.className = 'x-scrollable__track is-pending';
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
        let dragStartPointer = 0;
        let dragStartScroll = 0;
        let fadeTimer = null;

        const computeMetrics = () => {
            if (opts.orientation === 'horizontal') {
                const { clientWidth, scrollWidth, scrollLeft } = el;
                const contentSize = Math.max(scrollWidth, 1);
                const visibleRatio = Math.min(1, clientWidth / contentSize);
                const thumbSize = Math.max(opts.minThumbSize, clientWidth * visibleRatio);
                const maxThumbPos = Math.max(0, clientWidth - thumbSize);
                const maxScroll = Math.max(0, scrollWidth - clientWidth);
                return { clientSize: clientWidth, scrollSize: scrollWidth, scrollPos: scrollLeft, thumbSize, maxThumbPos, maxScroll };
            }
            const { clientHeight, scrollHeight, scrollTop } = el;
            const contentHeight = Math.max(scrollHeight, 1);
            const visibleRatio = Math.min(1, clientHeight / contentHeight);
            const thumbHeight = Math.max(opts.minThumbSize, clientHeight * visibleRatio);
            const maxThumbTop = Math.max(0, clientHeight - thumbHeight);
            const maxScrollTop = Math.max(0, scrollHeight - clientHeight);
            return { clientSize: clientHeight, scrollSize: scrollHeight, scrollPos: scrollTop, thumbSize: thumbHeight, maxThumbPos: maxThumbTop, maxScroll: maxScrollTop };
        };

        const update = () => {
            track.classList.remove('is-pending');
            const m = computeMetrics();
            const needsScroll = m.maxScroll > 1;
            track.classList.toggle('is-hidden', !needsScroll);
            if (!needsScroll) return;
            const scrollRatio = m.maxScroll > 0 ? m.scrollPos / m.maxScroll : 0;
            const thumbPos = m.maxThumbPos * Math.min(1, Math.max(0, scrollRatio));
            if (opts.orientation === 'horizontal') {
                thumb.style.width = `${m.thumbSize}px`;
                thumb.style.transform = `translateX(${thumbPos}px)`;
            } else {
                thumb.style.height = `${m.thumbSize}px`;
                thumb.style.transform = `translateY(${thumbPos}px)`;
            }
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
            if (m.maxThumbPos === 0) return;
            const client = opts.orientation === 'horizontal' ? e.clientX : e.clientY;
            const d = client - dragStartPointer;
            const scrollDelta = (d / m.maxThumbPos) * m.maxScroll;
            if (opts.orientation === 'horizontal') {
                el.scrollLeft = dragStartScroll + scrollDelta;
            } else {
                el.scrollTop = dragStartScroll + scrollDelta;
            }
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
            dragStartPointer = opts.orientation === 'horizontal' ? e.clientX : e.clientY;
            dragStartScroll = opts.orientation === 'horizontal' ? el.scrollLeft : el.scrollTop;
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
            const m = computeMetrics();
            const clickPos = opts.orientation === 'horizontal'
                ? e.clientX - rect.left
                : e.clientY - rect.top;
            const targetThumbPos = clickPos - m.thumbSize / 2;
            const clamped = Math.max(0, Math.min(targetThumbPos, m.maxThumbPos));
            const scrollRatio = m.maxThumbPos > 0 ? clamped / m.maxThumbPos : 0;
            if (opts.orientation === 'horizontal') {
                el.scrollLeft = scrollRatio * m.maxScroll;
            } else {
                el.scrollTop = scrollRatio * m.maxScroll;
            }
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
            el.style.removeProperty(opts.orientation === 'horizontal' ? 'overflow-x' : 'overflow-y');
        });
    });
}
