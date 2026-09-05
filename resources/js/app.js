/**
 * Site chrome behaviour: the header megamenu disclosures and the mobile drawer.
 *
 * Panels are toggled with the `hidden` property rather than inline display, so CSS
 * keeps full control of layout and the accessibility tree stays correct.
 */

const HOVER_INTENT_MS = 180;

function closeDisclosure(item) {
    const trigger = item.querySelector('[data-disclosure-trigger]');
    const panel = item.querySelector('[data-disclosure-panel]');

    if (!trigger || !panel) {
        return;
    }

    trigger.setAttribute('aria-expanded', 'false');
    panel.hidden = true;
}

function openDisclosure(item, all) {
    const trigger = item.querySelector('[data-disclosure-trigger]');
    const panel = item.querySelector('[data-disclosure-panel]');

    if (!trigger || !panel) {
        return;
    }

    all.forEach((other) => {
        if (other !== item) {
            closeDisclosure(other);
        }
    });

    trigger.setAttribute('aria-expanded', 'true');
    panel.hidden = false;
}

function initDisclosures() {
    const items = Array.from(document.querySelectorAll('[data-disclosure]'));

    if (items.length === 0) {
        return;
    }

    const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    items.forEach((item) => {
        const trigger = item.querySelector('[data-disclosure-trigger]');
        const panel = item.querySelector('[data-disclosure-panel]');

        if (!trigger || !panel) {
            return;
        }

        let openTimer = null;
        let closeTimer = null;

        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            const isOpen = trigger.getAttribute('aria-expanded') === 'true';

            if (isOpen) {
                closeDisclosure(item);
            } else {
                openDisclosure(item, items);
            }
        });

        if (canHover) {
            const scheduleOpen = () => {
                window.clearTimeout(closeTimer);
                openTimer = window.setTimeout(() => openDisclosure(item, items), HOVER_INTENT_MS);
            };

            const scheduleClose = () => {
                window.clearTimeout(openTimer);
                closeTimer = window.setTimeout(() => closeDisclosure(item), HOVER_INTENT_MS);
            };

            item.addEventListener('mouseenter', scheduleOpen);
            item.addEventListener('mouseleave', scheduleClose);
        }

        // Leaving the panel by keyboard closes it, so tab order continues sensibly.
        item.addEventListener('focusout', (event) => {
            if (!item.contains(event.relatedTarget)) {
                closeDisclosure(item);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        items.forEach((item) => {
            const trigger = item.querySelector('[data-disclosure-trigger]');

            if (trigger && trigger.getAttribute('aria-expanded') === 'true') {
                closeDisclosure(item);
                trigger.focus();
            }
        });
    });

    document.addEventListener('click', (event) => {
        items.forEach((item) => {
            if (!item.contains(event.target)) {
                closeDisclosure(item);
            }
        });
    });
}

function initDrawer() {
    const trigger = document.querySelector('[data-drawer-trigger]');
    const drawer = document.querySelector('[data-drawer]');

    if (!trigger || !drawer) {
        return;
    }

    trigger.addEventListener('click', () => {
        const isOpen = trigger.getAttribute('aria-expanded') === 'true';

        trigger.setAttribute('aria-expanded', String(!isOpen));
        drawer.hidden = isOpen;
        trigger.textContent = isOpen ? 'Menu' : 'Close';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initDisclosures();
    initDrawer();
});
