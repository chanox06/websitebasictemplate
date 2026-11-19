/**
 * Your Company - FAQ accordion.
 *
 * Progressive enhancement for the FAQ page's native <details> accordion.
 * Browsers that support the `name` attribute on <details> already keep one
 * answer open per category. This adds the same behavior for older browsers
 * so only a single answer is expanded at a time within each category.
 */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    ready(function () {
        var items = document.querySelectorAll('.lffaq-item');

        if (!items.length) {
            return;
        }

        Array.prototype.forEach.call(items, function (item) {
            item.addEventListener('toggle', function () {
                if (!item.open) {
                    return;
                }

                var group = item.getAttribute('name');

                if (!group) {
                    return;
                }

                Array.prototype.forEach.call(items, function (other) {
                    if (other !== item && other.getAttribute('name') === group && other.open) {
                        other.open = false;
                    }
                });
            });
        });
    });
})();
