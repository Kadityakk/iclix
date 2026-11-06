/**
 * Parallax engine behind the `x-parallax` directive.
 *
 * Usage: place layers inside an element with `data-parallax-root`, then configure each layer:
 *   <div x-parallax="{ scroll: 0.2, pointer: 14, origin: 'top', fade: true }">
 *
 * - scroll:  how far the layer lags behind the page (0 = moves with the page, negative = moves ahead).
 * - pointer: maximum pixels the layer follows the cursor; negative values move against the cursor.
 * - origin:  'top' keeps the layer at rest while its root sits at the top of the viewport (for heroes),
 *            'center' keeps it at rest while its root is centered (for sections further down the page).
 * - fade:    fades the layer out as its root scrolls away.
 *
 * Only `transform` and `opacity` are touched, so nothing triggers layout. Offsets are measured on the
 * root, which never moves itself, and the effect is switched off for prefers-reduced-motion.
 */
const POINTER_EASING = 0.08;
const POINTER_REST_THRESHOLD = 0.001;

const layers = new Set();
const visibleRoots = new WeakSet();
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const hoverCapable = window.matchMedia('(hover: hover) and (pointer: fine)');

const pointerTarget = { x: 0, y: 0 };
const pointerCurrent = { x: 0, y: 0 };
let frameRequested = false;

const rootObserver = new IntersectionObserver(
    (entries) => {
        for (const entry of entries) {
            if (entry.isIntersecting) {
                visibleRoots.add(entry.target);
            } else {
                visibleRoots.delete(entry.target);
            }
        }

        requestFrame();
    },
    { rootMargin: '20% 0px' },
);

function requestFrame() {
    if (!frameRequested) {
        frameRequested = true;
        requestAnimationFrame(renderFrame);
    }
}

function renderLayer(layer, viewportHeight) {
    const rootRect = layer.root.getBoundingClientRect();
    const scrollOffset =
        layer.origin === 'top' ? -rootRect.top : viewportHeight / 2 - (rootRect.top + rootRect.height / 2);

    const x = pointerCurrent.x * layer.pointer;
    const y = scrollOffset * layer.scroll + pointerCurrent.y * layer.pointer;

    layer.element.style.transform = `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, 0)`;

    if (layer.fade) {
        const fadedOut = Math.min(Math.max(-rootRect.top / (rootRect.height * 0.75), 0), 1);
        layer.element.style.opacity = (1 - fadedOut).toFixed(3);
    }
}

function renderFrame() {
    frameRequested = false;

    if (reducedMotion.matches) {
        return;
    }

    pointerCurrent.x += (pointerTarget.x - pointerCurrent.x) * POINTER_EASING;
    pointerCurrent.y += (pointerTarget.y - pointerCurrent.y) * POINTER_EASING;

    const viewportHeight = window.innerHeight;

    for (const layer of layers) {
        if (visibleRoots.has(layer.root)) {
            renderLayer(layer, viewportHeight);
        }
    }

    const pointerStillEasing =
        Math.abs(pointerTarget.x - pointerCurrent.x) > POINTER_REST_THRESHOLD ||
        Math.abs(pointerTarget.y - pointerCurrent.y) > POINTER_REST_THRESHOLD;

    if (pointerStillEasing) {
        requestFrame();
    }
}

function clearLayerStyles() {
    for (const layer of layers) {
        layer.element.style.transform = '';
        layer.element.style.opacity = '';
    }
}

function handlePointerMove(event) {
    if (!hoverCapable.matches) {
        return;
    }

    pointerTarget.x = (event.clientX / window.innerWidth - 0.5) * 2;
    pointerTarget.y = (event.clientY / window.innerHeight - 0.5) * 2;
    requestFrame();
}

window.addEventListener('scroll', requestFrame, { passive: true });
window.addEventListener('resize', requestFrame, { passive: true });
window.addEventListener('pointermove', handlePointerMove, { passive: true });
reducedMotion.addEventListener('change', () => (reducedMotion.matches ? clearLayerStyles() : requestFrame()));

/**
 * @param {import('alpinejs').Alpine} Alpine
 */
export function registerParallax(Alpine) {
    Alpine.directive('parallax', (element, { expression }, { evaluate, cleanup }) => {
        const options = expression ? evaluate(expression) : {};
        const root = element.closest('[data-parallax-root]') ?? element.parentElement;

        const layer = {
            element,
            root,
            scroll: options.scroll ?? 0.2,
            pointer: options.pointer ?? 0,
            origin: options.origin ?? 'center',
            fade: options.fade ?? false,
        };

        element.style.willChange = layer.fade ? 'transform, opacity' : 'transform';
        layers.add(layer);
        rootObserver.observe(root);
        requestFrame();

        cleanup(() => layers.delete(layer));
    });
}

/**
 * Registers `x-reveal`, which eases an element in the first time it scrolls into view.
 *
 * @param {import('alpinejs').Alpine} Alpine
 */
export function registerReveal(Alpine) {
    const revealObserver = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    revealObserver.unobserve(entry.target);
                }
            }
        },
        { rootMargin: '0px 0px -8% 0px' },
    );

    Alpine.directive('reveal', (element, _directive, { cleanup }) => {
        element.classList.add('reveal');
        revealObserver.observe(element);

        cleanup(() => revealObserver.unobserve(element));
    });
}
