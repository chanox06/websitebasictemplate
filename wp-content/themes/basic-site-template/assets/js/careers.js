/**
 * Your Company — Careers page enhancements.
 *
 * Two progressive enhancements for the online job application form:
 *
 * 1. Position pre-fill — the "Apply Now" buttons on the Careers page carry a
 *    class such as `lfcareers-apply--registered-nurse`; clicking one selects the
 *    matching option in the "Position Applying For" dropdown.
 *
 * 2. Conditional license fields — the professional license fields are only
 *    shown and required for licensed clinical roles.
 *
 * Both are enhancements only; the form works without JavaScript.
 *
 * @package basic-site-template
 */
(function () {
    'use strict';

    var LICENSED_POSITIONS = [
        'registered-nurse',
        'licensed-vocational-nurse',
        'physical-therapist',
        'occupational-therapist',
        'speech-therapist'
    ];

    // Roles that do not require a professional license. The license block is
    // hidden only for these; it stays visible for every other selection.
    var NON_LICENSED_POSITIONS = [
        'home-health-aide',
        'office-staff',
        'other'
    ];

    /**
     * Read the position slug encoded in a button's modifier class.
     *
     * @param {Element} el Button wrapper element.
     * @return {string} Position slug, or an empty string.
     */
    function positionFromClass(el) {
        var slug = '';

        Array.prototype.forEach.call(el.classList, function (className) {
            if (className.indexOf('lfcareers-apply--') === 0) {
                slug = className.replace('lfcareers-apply--', '');
            }
        });

        return slug;
    }

    /**
     * Pre-select the position dropdown from an "Apply Now" button.
     *
     * @return {void}
     */
    function initApplyButtons() {
        var buttons = document.querySelectorAll('.lfcareers-apply');
        var select = document.getElementById('bst-career-position');

        if (!buttons.length || !select) {
            return;
        }

        Array.prototype.forEach.call(buttons, function (button) {
            button.addEventListener('click', function () {
                var slug = positionFromClass(button);

                if (!slug) {
                    return;
                }

                select.value = slug;
                select.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    }

    /**
     * Show and require the license fields only for licensed clinical roles.
     *
     * @return {void}
     */
    function initLicenseToggle() {
        var select = document.getElementById('bst-career-position');
        var fieldset = document.getElementById('bst-career-license');
        var note = document.getElementById('bst-career-license-note');

        if (!select || !fieldset) {
            return;
        }

        var licenseInputs = fieldset.querySelectorAll('input, select');

        function update() {
            var value = select.value;
            var isLicensed = LICENSED_POSITIONS.indexOf(value) !== -1;
            var isNonLicensed = NON_LICENSED_POSITIONS.indexOf(value) !== -1;

            fieldset.hidden = isNonLicensed;
            if (note) {
                note.hidden = !isLicensed;
            }

            Array.prototype.forEach.call(licenseInputs, function (input) {
                input.required = isLicensed;
            });
        }

        select.addEventListener('change', update);
        update();
    }

    /**
     * Guard against oversized resume files before the browser uploads them.
     *
     * @return {void}
     */
    function initResumeGuard() {
        var input = document.getElementById('bst-career-resume');

        if (!input) {
            return;
        }

        var maxBytes = 5 * 1024 * 1024;

        input.addEventListener('change', function () {
            if (input.files && input.files[0] && input.files[0].size > maxBytes) {
                window.alert('Your resume must be 5 MB or smaller. Please choose a smaller file.');
                input.value = '';
            }
        });
    }

    function init() {
        initApplyButtons();
        initLicenseToggle();
        initResumeGuard();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
