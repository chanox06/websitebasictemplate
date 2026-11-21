/**
 * Banner Slider - Skitter initialisation.
 *
 * Uses WordPress' bundled jQuery (declared as a script dependency) and keeps
 * all work scoped to elements rendered by the [banner_slider] shortcode.
 */
(function ($) {
	'use strict';

	/**
	 * Read a boolean from a data attribute.
	 *
	 * @param {string} value    Attribute value.
	 * @param {boolean} fallback Fallback value.
	 * @return {boolean} Parsed value.
	 */
	function toBool(value, fallback) {
		if (typeof value === 'boolean') {
			return value;
		}

		if (typeof value === 'string') {
			var normalized = value.toLowerCase();

			if (['1', 'true', 'yes', 'on'].indexOf(normalized) !== -1) {
				return true;
			}

			if (['0', 'false', 'no', 'off'].indexOf(normalized) !== -1) {
				return false;
			}
		}

		return fallback;
	}

	/**
	 * Make a slider shell span the full viewport width.
	 *
	 * The theme's block templates constrain page content, so the wrapper is
	 * centred on its container and sized to the document's client width. This
	 * avoids the horizontal overflow that 100vw can cause with scrollbars.
	 *
	 * @param {jQuery} $shell Wrapper element.
	 * @return {void}
	 */
	function applyFullWidth($shell) {
		if (!$shell.hasClass('banner-slider-shell--full')) {
			return;
		}

		var viewportWidth = document.documentElement.clientWidth;

		$shell.css({
			position: 'relative',
			left: '50%',
			transform: 'translateX(-50%)',
			width: viewportWidth + 'px',
			maxWidth: 'none'
		});
	}

	/**
	 * Keep the clone boxes in sync with the rendered slider size.
	 *
	 * Skitter sizes each animation clone by rendering the source image at the
	 * slider's dimensions. WordPress themes commonly set `max-width: 100%` on
	 * all images, which shrinks those clones to the size of their small
	 * clipping box. Publishing the dimensions as custom properties lets the
	 * CSS restore the expected size for every clone.
	 *
	 * @param {jQuery} $slider Slider element.
	 * @return {void}
	 */
	function syncSliderSize($slider) {
		$slider.css({
			'--banner-slider-width': $slider.width() + 'px',
			'--banner-slider-height': $slider.height() + 'px'
		});
	}

	/**
	 * Initialise one slider.
	 *
	 * @param {Element} element Slider element.
	 * @return {void}
	 */
	function initSlider(element) {
		var $slider = $(element);

		if ($slider.data('bannerSliderReady') || typeof $.fn.skitter !== 'function') {
			return;
		}

		$slider.data('bannerSliderReady', true);

		var $shell = $slider.closest('[data-banner-slider-shell]');
		var animation = String($slider.attr('data-animation') || 'fade');
		var interval = parseInt($slider.attr('data-interval'), 10);
		var velocity = parseFloat($slider.attr('data-velocity'));
		var easing = String($slider.attr('data-easing') || '');
		var navigation = toBool($slider.attr('data-navigation'), true);
		var dots = toBool($slider.attr('data-dots'), true);
		var pauseOnHover = toBool($slider.attr('data-pause'), true);

		if (isNaN(interval) || interval < 1000) {
			interval = 5000;
		}

		if (isNaN(velocity) || velocity < 0.5 || velocity > 2) {
			velocity = 1;
		}

		// Respect visitors who ask their operating system to reduce motion.
		if (
			window.matchMedia &&
			window.matchMedia('(prefers-reduced-motion: reduce)').matches
		) {
			animation = 'fade';
		}

		applyFullWidth($shell);

		$slider.skitter({
			animation: animation,
			interval: interval,
			velocity: velocity,
			easing: easing,
			navigation: navigation,
			dots: dots,
			numbers: false,
			label: false,
			stop_over: pauseOnHover,
			auto_play: true,
			responsive: {
				small: {
					max_width: 768
				}
			}
		});

		syncSliderSize($slider);

		// Skitter recalculates its height once the first image loads.
		if (typeof window.ResizeObserver === 'function') {
			new window.ResizeObserver(function () {
				syncSliderSize($slider);
			}).observe($slider[0]);
		} else {
			$(window).on('load', function () {
				syncSliderSize($slider);
			});
		}

		$slider
			.find('.prev_button')
			.attr('aria-label', $slider.attr('data-prev-label') || 'Previous slide');

		$slider
			.find('.next_button')
			.attr('aria-label', $slider.attr('data-next-label') || 'Next slide');

		var goToLabel = $slider.attr('data-go-to-label') || 'Go to slide';
		var $dots = $slider.find('.info_slide_dots .image_number');

		$dots.each(function (index) {
			$(this)
				.attr({
					role: 'button',
					tabindex: 0,
					'aria-label': goToLabel + ' ' + (index + 1)
				})
				.on('keydown', function (event) {
					if (event.key === 'Enter' || event.key === ' ') {
						event.preventDefault();
						$(this).trigger('click');
					}
				});
		});

		// Keep the banner full-width and recalculate Skitter's dimensions.
		var resizeTimer = null;

		$(window).on('resize.bannerSlider', function () {
			window.clearTimeout(resizeTimer);

			resizeTimer = window.setTimeout(function () {
				applyFullWidth($shell);

				if (typeof $slider.data('skitter_number') !== 'undefined') {
					$slider.skitter('setDimensions');
					syncSliderSize($slider);
				}
			}, 260);
		});
	}

	$(function () {
		$('[data-banner-slider]').each(function () {
			initSlider(this);
		});
	});
})(jQuery);
