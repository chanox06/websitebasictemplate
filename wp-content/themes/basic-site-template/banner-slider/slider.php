<?php
/**
 * Skitter banner slider.
 *
 * Registers the [banner_slider] shortcode and loads the self-hosted Skitter
 * library only on pages that use the shortcode.
 *
 * @package Basic_Site_Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Available Skitter animations.
 *
 * Mirrors the animation names supported by the bundled Skitter 5.1.8 build.
 *
 * @return string[]
 */
function banner_slider_animations() {
	return array(
		'cube',
		'cubeRandom',
		'block',
		'cubeStop',
		'cubeStopRandom',
		'cubeHide',
		'cubeSize',
		'horizontal',
		'showBars',
		'showBarsRandom',
		'tube',
		'fade',
		'fadeFour',
		'paralell',
		'blind',
		'blindHeight',
		'blindWidth',
		'directionTop',
		'directionBottom',
		'directionRight',
		'directionLeft',
		'cubeSpread',
		'glassCube',
		'glassBlock',
		'circles',
		'circlesInside',
		'circlesRotate',
		'cubeShow',
		'upBars',
		'downBars',
		'hideBars',
		'swapBars',
		'swapBarsBack',
		'swapBlocks',
		'cut',
		'random',
		'randomSmart',
	);
}

/**
 * Easing names supplied by the bundled jQuery Easing 1.3 file.
 *
 * @return string[]
 */
function banner_slider_easings() {
	return array(
		'easeInQuad',
		'easeOutQuad',
		'easeInOutQuad',
		'easeInCubic',
		'easeOutCubic',
		'easeInOutCubic',
		'easeInQuart',
		'easeOutQuart',
		'easeInOutQuart',
		'easeInQuint',
		'easeOutQuint',
		'easeInOutQuint',
		'easeInSine',
		'easeOutSine',
		'easeInOutSine',
		'easeInExpo',
		'easeOutExpo',
		'easeInOutExpo',
		'easeInCirc',
		'easeOutCirc',
		'easeInOutCirc',
		'easeInElastic',
		'easeOutElastic',
		'easeInOutElastic',
		'easeInBack',
		'easeOutBack',
		'easeInOutBack',
		'easeInBounce',
		'easeOutBounce',
		'easeInOutBounce',
	);
}

/**
 * Validate an animation name and map friendly aliases to Skitter names.
 *
 * @param string $animation Requested animation.
 * @return string
 */
function banner_slider_sanitize_animation( $animation ) {
	$animation = sanitize_text_field( (string) $animation );
	$map       = array(
		'blocks'      => 'block',
		'blinds'      => 'blind',
		'random'      => 'random',
		'randomsmart' => 'randomSmart',
	);

	foreach ( banner_slider_animations() as $allowed ) {
		$map[ strtolower( $allowed ) ] = $allowed;
	}

	$key = strtolower( $animation );

	return isset( $map[ $key ] ) ? $map[ $key ] : 'fade';
}

/**
 * Validate an easing name.
 *
 * @param string $easing Requested easing.
 * @return string Empty string when the easing is not supported.
 */
function banner_slider_sanitize_easing( $easing ) {
	$easing = sanitize_text_field( (string) $easing );

	if ( '' === $easing ) {
		return '';
	}

	foreach ( banner_slider_easings() as $allowed ) {
		if ( 0 === strcasecmp( $allowed, $easing ) ) {
			return $allowed;
		}
	}

	return '';
}

/**
 * Convert a shortcode boolean to a real boolean.
 *
 * @param mixed $value Raw shortcode value.
 * @return bool
 */
function banner_slider_to_bool( $value ) {
	if ( is_bool( $value ) ) {
		return $value;
	}

	$value = strtolower( trim( (string) $value ) );

	return in_array( $value, array( '1', 'true', 'yes', 'on' ), true );
}

/**
 * Find slide images in banner-slider/images and sort them numerically.
 *
 * Supported names: slide-1.jpg, slide-2.png, slide-3.webp, etc.
 *
 * @return array<int, array<string, mixed>>
 */
function banner_slider_get_images() {
	$images = array();
	$dir    = get_theme_file_path( 'banner-slider/images' );
	$uri    = get_theme_file_uri( 'banner-slider/images' );

	if ( ! is_dir( $dir ) ) {
		return $images;
	}

	$files = glob( trailingslashit( $dir ) . 'slide-*' );

	if ( ! is_array( $files ) ) {
		return $images;
	}

	foreach ( $files as $file ) {
		if ( ! is_file( $file ) ) {
			continue;
		}

		$filename = wp_basename( $file );

		if ( ! preg_match( '/^slide-(\d+)\.(jpe?g|png|webp)$/i', $filename, $matches ) ) {
			continue;
		}

		$number = (int) $matches[1];

		if ( $number < 1 ) {
			continue;
		}

		$images[] = array(
			'path'   => $file,
			'uri'    => trailingslashit( $uri ) . $filename,
			'number' => $number,
		);
	}

	usort(
		$images,
		static function ( $a, $b ) {
			if ( $a['number'] === $b['number'] ) {
				return strcmp( $a['uri'], $b['uri'] );
			}

			return ( $a['number'] < $b['number'] ) ? -1 : 1;
		}
	);

	return $images;
}

/**
 * Register the slider assets.
 *
 * @return void
 */
function banner_slider_register_assets() {
	$base_path = get_theme_file_path( 'banner-slider' );
	$base_uri  = get_theme_file_uri( 'banner-slider' );

	wp_register_style(
		'banner-slider-skitter',
		$base_uri . '/assets/css/skitter.css',
		array(),
		filemtime( $base_path . '/assets/css/skitter.css' )
	);

	wp_register_style(
		'banner-slider-custom',
		$base_uri . '/custom-slider.css',
		array( 'banner-slider-skitter' ),
		filemtime( $base_path . '/custom-slider.css' )
	);

	wp_register_script(
		'banner-slider-easing',
		$base_uri . '/assets/js/jquery.easing.1.3.js',
		array( 'jquery' ),
		'1.3',
		true
	);

	wp_register_script(
		'banner-slider-skitter',
		$base_uri . '/assets/js/jquery.skitter.min.js',
		array( 'jquery', 'banner-slider-easing' ),
		'5.1.8',
		true
	);

	wp_register_script(
		'banner-slider-custom',
		$base_uri . '/custom-slider.js',
		array( 'jquery', 'banner-slider-skitter' ),
		filemtime( $base_path . '/custom-slider.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'banner_slider_register_assets', 5 );

/**
 * Enqueue the slider assets.
 *
 * @return void
 */
function banner_slider_enqueue_assets() {
	if ( ! wp_style_is( 'banner-slider-custom', 'registered' ) ) {
		banner_slider_register_assets();
	}

	wp_enqueue_style( 'banner-slider-skitter' );
	wp_enqueue_style( 'banner-slider-custom' );
	wp_enqueue_script( 'banner-slider-easing' );
	wp_enqueue_script( 'banner-slider-skitter' );
	wp_enqueue_script( 'banner-slider-custom' );
}

/**
 * Enqueue assets early when a singular post contains the shortcode.
 *
 * This avoids a flash of unstyled slider markup. The shortcode callback also
 * enqueues the handles as a fallback for templates and reusable blocks.
 *
 * @return void
 */
function banner_slider_maybe_enqueue_assets() {
	if ( ! is_singular() ) {
		return;
	}

	$post = get_queried_object();

	if ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'banner_slider' ) ) {
		banner_slider_enqueue_assets();
	}
}
add_action( 'wp_enqueue_scripts', 'banner_slider_maybe_enqueue_assets', 20 );

/**
 * Render the [banner_slider] shortcode.
 *
 * Usage:
 *   [banner_slider]
 *   [banner_slider animation="fade" interval="6000"]
 *   [banner_slider animation="blinds" navigation="false" dots="false"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function banner_slider_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'animation'  => 'fade',
			'interval'   => '5000',
			'velocity'   => '1',
			'navigation' => 'true',
			'dots'       => 'true',
			'pause'      => 'true',
			'easing'     => '',
			'fullwidth'  => 'true',
			'class'      => '',
		),
		$atts,
		'banner_slider'
	);

	$images = banner_slider_get_images();

	if ( empty( $images ) ) {
		if ( current_user_can( 'edit_posts' ) ) {
			return '<p class="banner-slider-notice">' . esc_html__( 'Banner Slider: add slide-1.png, slide-2.png, slide-3.png, etc. to banner-slider/images/.', 'basic-site-template' ) . '</p>';
		}

		return '';
	}

	$animation = banner_slider_sanitize_animation( $atts['animation'] );
	$easing    = banner_slider_sanitize_easing( $atts['easing'] );

	$interval = absint( $atts['interval'] );
	if ( $interval < 1000 || $interval > 30000 ) {
		$interval = 5000;
	}

	$velocity = (float) $atts['velocity'];
	if ( $velocity < 0.5 || $velocity > 2 ) {
		$velocity = 1;
	}

	$navigation = banner_slider_to_bool( $atts['navigation'] );
	$dots       = banner_slider_to_bool( $atts['dots'] );
	$pause      = banner_slider_to_bool( $atts['pause'] );
	$fullwidth  = banner_slider_to_bool( $atts['fullwidth'] );

	$custom_classes = array();
	foreach ( preg_split( '/\s+/', (string) $atts['class'] ) as $class_name ) {
		$class_name = sanitize_html_class( $class_name );

		if ( '' !== $class_name ) {
			$custom_classes[] = $class_name;
		}
	}

	static $instance = 0;
	++$instance;

	$slider_id       = 'banner-slider-' . $instance;
	$wrapper_classes = array( 'banner-slider-shell' );

	if ( $fullwidth ) {
		$wrapper_classes[] = 'banner-slider-shell--full';
	}

	$wrapper_classes = array_merge( $wrapper_classes, $custom_classes );

	$first_size   = wp_getimagesize( $images[0]['path'] );
	$first_width  = is_array( $first_size ) ? (int) $first_size[0] : 1920;
	$first_height = is_array( $first_size ) ? (int) $first_size[1] : 700;

	banner_slider_enqueue_assets();

	ob_start();
	?>
	<div id="<?php echo esc_attr( $slider_id ); ?>" class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-banner-slider-shell>
		<div
			class="skitter banner-slider with-dots"
			data-banner-slider
			style="<?php echo esc_attr( '--banner-slider-aspect: ' . $first_width . ' / ' . $first_height . ';' ); ?>"
			data-animation="<?php echo esc_attr( $animation ); ?>"
			data-interval="<?php echo esc_attr( (string) $interval ); ?>"
			data-velocity="<?php echo esc_attr( (string) $velocity ); ?>"
			data-navigation="<?php echo esc_attr( $navigation ? 'true' : 'false' ); ?>"
			data-dots="<?php echo esc_attr( $dots ? 'true' : 'false' ); ?>"
			data-pause="<?php echo esc_attr( $pause ? 'true' : 'false' ); ?>"
			data-easing="<?php echo esc_attr( $easing ); ?>"
			data-prev-label="<?php esc_attr_e( 'Previous slide', 'basic-site-template' ); ?>"
			data-next-label="<?php esc_attr_e( 'Next slide', 'basic-site-template' ); ?>"
			data-go-to-label="<?php esc_attr_e( 'Go to slide', 'basic-site-template' ); ?>"
			role="region"
			aria-roledescription="<?php esc_attr_e( 'carousel', 'basic-site-template' ); ?>"
			aria-label="<?php esc_attr_e( 'Banner slideshow', 'basic-site-template' ); ?>"
		>
			<ul class="banner-slider-slides">
				<?php
				foreach ( $images as $index => $image ) {
					$size   = wp_getimagesize( $image['path'] );
					$width  = is_array( $size ) ? (int) $size[0] : 1920;
					$height = is_array( $size ) ? (int) $size[1] : 700;
					$alt    = sprintf(
						/* translators: %d: slide number. */
						__( 'Slide %d', 'basic-site-template' ),
						(int) $image['number']
					);
					?>
					<li>
						<img
							src="<?php echo esc_url( $image['uri'] ); ?>"
							alt="<?php echo esc_attr( $alt ); ?>"
							width="<?php echo esc_attr( (string) $width ); ?>"
							height="<?php echo esc_attr( (string) $height ); ?>"
							<?php echo 0 === $index ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'; ?>
						/>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'banner_slider', 'banner_slider_shortcode' );

