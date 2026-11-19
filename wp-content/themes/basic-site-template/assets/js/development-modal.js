/**
 * Development notice modal.
 *
 * The homepage is the only finished page for now. Any CTA button,
 * navigation tab or tab control opens a branded "still in development"
 * notice instead of navigating away.
 */
(function () {
    'use strict';

    var MODAL_ID = 'samson-dev-modal';
    var HOVER_OPEN_DELAY = 650;

    var LEARN_MORE_PATTERN = /^learn\s*more\s*(?:\u2192|->)?$/i;

    var TRIGGER_SELECTOR = [
        '.wp-block-button__link',
        '.wp-element-button',
        '#header-navigation .wp-block-navigation-item__content',
        '[role="tab"]',
        '.tabs button',
        '.tabs [data-tab]',
        '.nav-tabs .nav-link',
        '.wp-block-tabs button',
        '[data-samson-dev-modal]'
    ].join(', ');

    var FOCUSABLE_SELECTOR = [
        'a[href]',
        'button:not([disabled])',
        'input:not([disabled]):not([type="hidden"])',
        'select:not([disabled])',
        'textarea:not([disabled])',
        '[tabindex]:not([tabindex="-1"])'
    ].join(', ');

    var modal = null;
    var dialog = null;
    var lastTrigger = null;
    var closeTimer = null;
    var hoverOpenTimer = null;
    var hoverOpenTrigger = null;
    var hoverSuppressUntil = 0;
    var reduceMotion = window.matchMedia
        ? window.matchMedia('(prefers-reduced-motion: reduce)')
        : null;

    function getTriggerText(element) {
        return element
            ? element.textContent.replace(/\s+/g, ' ').trim()
            : '';
    }

    function isLearnMore(element) {
        return !!element && LEARN_MORE_PATTERN.test(getTriggerText(element));
    }

    function getLearnMoreLabel(trigger) {
        var column = trigger.closest('.wp-block-column');
        var heading = column
            ? column.querySelector('.wp-block-heading')
            : null;

        return heading ? getTriggerText(heading) : '';
    }

    /*
     * Service cards use a plain paragraph for "LEARN MORE →". Turn that
     * paragraph into a real, focusable modal trigger and split the arrow
     * into its own span so it can animate on hover.
     */
    function prepareLearnMoreTriggers() {
        document
            .querySelectorAll('.samson-services p.wp-block-paragraph')
            .forEach(function (trigger) {
                if (trigger.classList.contains('samson-learn-more') || !isLearnMore(trigger)) {
                    return;
                }

                trigger.classList.add('samson-learn-more');
                trigger.setAttribute('data-samson-dev-modal', '');
                trigger.setAttribute('data-samson-dev-hover', '');
                trigger.setAttribute('aria-haspopup', 'dialog');
                trigger.setAttribute('tabindex', '0');
                trigger.setAttribute('role', 'button');

                var label = getLearnMoreLabel(trigger);

                if (label) {
                    trigger.setAttribute('aria-label', 'Learn more about ' + label);
                }

                var match = getTriggerText(trigger).match(
                    /^(.*?)(?:\s*)(?:\u2192|->)$/
                );

                if (!match) {
                    return;
                }

                var labelText = match[1].trim();

                if (!labelText) {
                    return;
                }

                var labelSpan = document.createElement('span');
                labelSpan.className = 'samson-learn-more__label';
                labelSpan.textContent = labelText;

                var arrowSpan = document.createElement('span');
                arrowSpan.className = 'samson-learn-more__arrow';
                arrowSpan.setAttribute('aria-hidden', 'true');
                arrowSpan.textContent = '\u2192';

                trigger.textContent = '';
                trigger.appendChild(labelSpan);
                trigger.appendChild(document.createTextNode(' '));
                trigger.appendChild(arrowSpan);
            });
    }

    function clearHoverOpen() {
        if (hoverOpenTimer) {
            window.clearTimeout(hoverOpenTimer);
        }

        hoverOpenTimer = null;
        hoverOpenTrigger = null;
    }

    function scheduleHoverOpen(trigger) {
        if (
            !trigger ||
            Date.now() < hoverSuppressUntil ||
            (modal && !modal.hidden) ||
            (hoverOpenTimer && hoverOpenTrigger === trigger)
        ) {
            return;
        }

        clearHoverOpen();

        hoverOpenTrigger = trigger;

        hoverOpenTimer = window.setTimeout(function () {
            var target = hoverOpenTrigger;

            hoverOpenTimer = null;
            hoverOpenTrigger = null;

            if (!target || !document.contains(target) || (modal && !modal.hidden)) {
                return;
            }

            openModal(target);
        }, HOVER_OPEN_DELAY);
    }

    function buildModal() {
        if (modal) {
            return;
        }

        modal = document.createElement('div');
        modal.className = 'samson-dev-modal';
        modal.id = MODAL_ID;
        modal.hidden = true;

        modal.innerHTML = [
            '<div class="samson-dev-modal__backdrop" data-samson-dev-dismiss></div>',
            '<div class="samson-dev-modal__dialog" role="dialog" aria-modal="true"',
            ' aria-labelledby="samson-dev-modal-title"',
            ' aria-describedby="samson-dev-modal-message" tabindex="-1">',
            '    <button class="samson-dev-modal__close" type="button"',
            ' data-samson-dev-close aria-label="Close development notice">',
            '        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">',
            '            <path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor"',
            ' stroke-width="2" stroke-linecap="round"></path>',
            '        </svg>',
            '    </button>',
            '    <div class="samson-dev-modal__icon" aria-hidden="true">',
            '        <svg viewBox="0 0 48 48" fill="none" focusable="false">',
            '            <path d="M7 18h34v22H7V18Z" stroke="currentColor"',
            ' stroke-width="2.4" stroke-linejoin="round"></path>',
            '            <path d="M7 18 11 9h30l-4 9H7Z" stroke="currentColor"',
            ' stroke-width="2.4" stroke-linejoin="round"></path>',
            '            <path d="m16 9-4 9M26 9l-4 9M36 9l-4 9" stroke="currentColor"',
            ' stroke-width="2.4" stroke-linecap="round"></path>',
            '            <path d="M17 27h14M17 33h9" stroke="currentColor"',
            ' stroke-width="2.4" stroke-linecap="round"></path>',
            '        </svg>',
            '    </div>',
            '    <p class="samson-dev-modal__eyebrow">Coming soon</p>',
            '    <h2 class="samson-dev-modal__title" id="samson-dev-modal-title">',
            '        Still in development',
            '    </h2>',
            '    <p class="samson-dev-modal__message" id="samson-dev-modal-message">',
            '        Our website is still under development. Please come back another time.',
            '    </p>',
            '    <button class="samson-dev-modal__button" type="button"',
            ' data-samson-dev-close>',
            '        <span>Got it</span>',
            '        <span class="samson-dev-modal__arrow" aria-hidden="true">→</span>',
            '    </button>',
            '    <p class="samson-dev-modal__note">',
            '        <span aria-hidden="true"></span>',
            '        Samson Styles · Storytelling that makes a difference',
            '    </p>',
            '</div>'
        ].join('\n');

        document.body.appendChild(modal);

        dialog = modal.querySelector('.samson-dev-modal__dialog');

        modal.addEventListener('click', function (event) {
            if (event.target === modal || event.target.closest('[data-samson-dev-close], [data-samson-dev-dismiss]')) {
                event.preventDefault();
                closeModal();
            }
        });
    }

    function closeOpenMenu() {
        var closeButton = document.querySelector(
            '#header-navigation .wp-block-navigation__responsive-container.is-menu-open ' +
            '.wp-block-navigation__responsive-container-close'
        );

        if (closeButton) {
            closeButton.click();
        }
    }

    function openModal(trigger) {
        buildModal();
        closeOpenMenu();
        clearHoverOpen();

        if (closeTimer) {
            window.clearTimeout(closeTimer);
            closeTimer = null;
        }

        lastTrigger = trigger || null;

        var scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;

        if (scrollbarWidth > 0) {
            document.body.style.setProperty('--samson-dev-scrollbar', scrollbarWidth + 'px');
        } else {
            document.body.style.removeProperty('--samson-dev-scrollbar');
        }

        document.documentElement.classList.add('samson-dev-modal-open');
        document.body.classList.add('samson-dev-modal-open');

        modal.hidden = false;

        /* Force the initial hidden state to paint before animating in. */
        void modal.offsetWidth;

        window.requestAnimationFrame(function () {
            modal.classList.add('is-open');
            dialog.focus({ preventScroll: true });
        });
    }

    function closeModal() {
        if (!modal || modal.hidden) {
            return;
        }

        var restoreTarget = lastTrigger;
        var delay = reduceMotion && reduceMotion.matches ? 0 : 280;

        /*
         * If the modal was opened by hovering, do not immediately reopen
         * while the pointer is still resting on the same Learn More link.
         * The short delay lets the pointer leave before hover can reopen it.
         */
        if (restoreTarget && restoreTarget.hasAttribute('data-samson-dev-hover')) {
            hoverSuppressUntil = Date.now() + 700;
        }

        lastTrigger = null;

        modal.classList.remove('is-open');

        document.documentElement.classList.remove('samson-dev-modal-open');
        document.body.classList.remove('samson-dev-modal-open');
        document.body.style.removeProperty('--samson-dev-scrollbar');

        closeTimer = window.setTimeout(function () {
            if (!modal.classList.contains('is-open')) {
                modal.hidden = true;
            }

            closeTimer = null;

            /*
             * Wait one frame after hiding the dialog. Hiding a focused
             * element moves focus to <body>, so restoring it immediately
             * would be undone by the browser.
             */
            window.requestAnimationFrame(function () {
                var canRestore = restoreTarget &&
                    document.contains(restoreTarget) &&
                    restoreTarget.getClientRects().length > 0 &&
                    window.getComputedStyle(restoreTarget).visibility !== 'hidden';

                if (canRestore) {
                    restoreTarget.focus({ preventScroll: true });
                    return;
                }

                /* Mobile nav links are hidden once the overlay closes. */
                var menuToggle = document.querySelector(
                    '#header-navigation .wp-block-navigation__responsive-container-open'
                );

                if (menuToggle && menuToggle.getClientRects().length > 0) {
                    menuToggle.focus({ preventScroll: true });
                }
            });
        }, delay);
    }

    function trapFocus(event) {
        if (!modal || modal.hidden || !dialog) {
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            closeModal();
            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        var focusables = Array.prototype.slice
            .call(dialog.querySelectorAll(FOCUSABLE_SELECTOR))
            .filter(function (element) {
                return element.offsetParent !== null;
            });

        if (!focusables.length) {
            event.preventDefault();
            dialog.focus();
            return;
        }

        var first = focusables[0];
        var last = focusables[focusables.length - 1];
        var active = document.activeElement;

        if (event.shiftKey && (active === first || active === dialog)) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && (active === last || active === dialog)) {
            event.preventDefault();
            first.focus();
        }
    }

    function isTrigger(element) {
        if (!element || element.closest('#' + MODAL_ID)) {
            return false;
        }

        if (element.hasAttribute('data-samson-dev-ignore')) {
            return false;
        }

        /* Keep real form submission and the responsive menu controls working. */
        if (element.matches(
            'input[type="submit"], input[type="button"], button[type="submit"], ' +
            '.wp-block-navigation__responsive-container-open, ' +
            '.wp-block-navigation__responsive-container-close'
        )) {
            return false;
        }

        return true;
    }

    function onDocumentKeydown(event) {
        trapFocus(event);

        if (event.defaultPrevented) {
            return;
        }

        if (event.key !== 'Enter' && event.key !== ' ') {
            return;
        }

        var target = event.target instanceof Element
            ? event.target.closest(TRIGGER_SELECTOR)
            : null;

        if (!isTrigger(target)) {
            return;
        }

        if (target.matches('a[href], button, input, select, textarea')) {
            return;
        }

        event.preventDefault();
        openModal(target);
    }

    function onDocumentClick(event) {
        if (event.defaultPrevented || event.button !== 0) {
            return;
        }

        /* Let modified clicks keep their normal browser behaviour. */
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        var target = event.target instanceof Element
            ? event.target.closest(TRIGGER_SELECTOR)
            : null;

        if (!isTrigger(target)) {
            return;
        }

        event.preventDefault();
        openModal(target);
    }

    function onDocumentPointerOver(event) {
        if (event.pointerType && event.pointerType !== 'mouse') {
            return;
        }

        var trigger = event.target instanceof Element
            ? event.target.closest('[data-samson-dev-hover]')
            : null;

        if (!isTrigger(trigger)) {
            return;
        }

        if (event.relatedTarget instanceof Node && trigger.contains(event.relatedTarget)) {
            return;
        }

        scheduleHoverOpen(trigger);
    }

    function onDocumentPointerOut(event) {
        var trigger = event.target instanceof Element
            ? event.target.closest('[data-samson-dev-hover]')
            : null;

        if (!trigger) {
            return;
        }

        if (event.relatedTarget instanceof Node && trigger.contains(event.relatedTarget)) {
            return;
        }

        if (hoverOpenTrigger === trigger) {
            clearHoverOpen();
        }
    }

    function decorateTriggers() {
        document.querySelectorAll(TRIGGER_SELECTOR).forEach(function (trigger) {
            if (!isTrigger(trigger)) {
                return;
            }

            if (!trigger.hasAttribute('aria-haspopup')) {
                trigger.setAttribute('aria-haspopup', 'dialog');
            }

            /*
             * The featured film CTA is a regular Gutenberg button, but its
             * visible label is also "LEARN MORE →", so give it the same
             * hover-to-open behaviour as the service card links.
             */
            if (
                isLearnMore(trigger) &&
                trigger.matches('.wp-block-button__link, .wp-element-button')
            ) {
                trigger.setAttribute('data-samson-dev-hover', '');
            }

            /*
             * Some Gutenberg button links are rendered without an href.
             * Make them keyboard-focusable and announce them as buttons.
             */
            if (!trigger.matches('a[href], button, input, select, textarea, [tabindex]')) {
                trigger.setAttribute('tabindex', '0');

                if (!trigger.hasAttribute('role')) {
                    trigger.setAttribute('role', 'button');
                }
            }
        });
    }

    function init() {
        buildModal();

        prepareLearnMoreTriggers();

        document.addEventListener('click', onDocumentClick);
        document.addEventListener('keydown', onDocumentKeydown);
        document.addEventListener('pointerover', onDocumentPointerOver);
        document.addEventListener('pointerout', onDocumentPointerOut);

        decorateTriggers();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

