<?php
/**
 * Create wp-config.php with per-site database settings and random salts.
 *
 * Usage:
 *   php bin/create-wp-config.php --db-name=NAME --db-user=USER --db-password=PASS [options]
 *
 * Options:
 *   --db-name=NAME                 Database name. Required.
 *   --db-user=USER                 Database user. Required.
 *   --db-password=PASS             Database password. May be empty.
 *   --db-host=HOST                 Database host. Default: localhost.
 *   --prefix=PREFIX                Table prefix. Default: wp_.
 *   --recaptcha-site-key=KEY       Optional Google reCAPTCHA v2 site key.
 *   --recaptcha-secret-key=KEY     Optional Google reCAPTCHA v2 secret key.
 *   --debug                        Enable WP_DEBUG.
 *   --force                        Overwrite an existing wp-config.php.
 *   --help                         Show this help.
 *
 * The file is generated from wp-config-sample.php. Every run creates eight new
 * random authentication keys/salts. The generated wp-config.php is gitignored.
 */

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 403 );
	exit( "CLI only.\n" );
}

$wpc_root   = dirname( __DIR__ );
$wpc_sample = $wpc_root . '/wp-config-sample.php';
$wpc_target = $wpc_root . '/wp-config.php';

$wpc_options = array(
	'db-name'              => '',
	'db-user'              => '',
	'db-password'          => '',
	'db-host'              => 'localhost',
	'prefix'               => 'wp_',
	'recaptcha-site-key'   => '',
	'recaptcha-secret-key' => '',
	'debug'                => false,
	'force'                => false,
);

/**
 * Print command usage.
 *
 * @return void
 */
function wpc_usage() {
	echo "Usage: php bin/create-wp-config.php --db-name=NAME --db-user=USER --db-password=PASS [options]\n\n";
	echo "  --db-name=NAME              Database name. Required.\n";
	echo "  --db-user=USER              Database user. Required.\n";
	echo "  --db-password=PASS          Database password. May be empty.\n";
	echo "  --db-host=HOST              Database host. Default: localhost.\n";
	echo "  --prefix=PREFIX             Table prefix. Default: wp_.\n";
	echo "  --recaptcha-site-key=KEY    Optional reCAPTCHA v2 site key.\n";
	echo "  --recaptcha-secret-key=KEY  Optional reCAPTCHA v2 secret key.\n";
	echo "  --debug                     Enable WP_DEBUG.\n";
	echo "  --force                     Overwrite an existing wp-config.php.\n";
	echo "  --help                      Show this help.\n";
}

/**
 * Stop with an error.
 *
 * @param string $message Message.
 * @return void
 */
function wpc_fail( $message ) {
	fwrite( STDERR, 'ERROR: ' . $message . "\n" );
	exit( 1 );
}

/**
 * Replace a single-quoted string constant in wp-config-sample.php.
 *
 * @param string $config Config contents.
 * @param string $name   Constant name.
 * @param string $value  New value.
 * @return string
 */
function wpc_replace_constant( $config, $name, $value ) {
	$pattern     = "/define\(\s*'" . preg_quote( $name, '/' ) . "'\s*,\s*'[^']*'\s*\);/";
	$replacement = "define( '" . $name . "', " . var_export( (string) $value, true ) . ' );';
	$count       = 0;
	$updated     = preg_replace_callback(
		$pattern,
		static function () use ( $replacement ) {
			return $replacement;
		},
		$config,
		1,
		$count
	);

	if ( null === $updated || 1 !== $count ) {
		wpc_fail( 'Could not find define( \'' . $name . '\' ) in wp-config-sample.php.' );
	}

	return $updated;
}

/**
 * Replace the WP_DEBUG constant.
 *
 * @param string $config Config contents.
 * @param bool   $debug  Whether debug is enabled.
 * @return string
 */
function wpc_replace_debug( $config, $debug ) {
	$pattern     = "/define\(\s*'WP_DEBUG'\s*,\s*(?:true|false)\s*\);/";
	$replacement = "define( 'WP_DEBUG', " . ( $debug ? 'true' : 'false' ) . ' );';
	$count       = 0;
	$updated     = preg_replace( $pattern, $replacement, $config, 1, $count );

	if ( null === $updated || 1 !== $count ) {
		wpc_fail( 'Could not find the WP_DEBUG setting in wp-config-sample.php.' );
	}

	return $updated;
}

/**
 * Replace the table prefix assignment.
 *
 * @param string $config Config contents.
 * @param string $prefix Table prefix.
 * @return string
 */
function wpc_replace_prefix( $config, $prefix ) {
	$pattern     = "/\\\$table_prefix\s*=\s*'[^']*';/";
	$replacement = '$table_prefix = ' . var_export( $prefix, true ) . ';';
	$count       = 0;
	$updated     = preg_replace_callback(
		$pattern,
		static function () use ( $replacement ) {
			return $replacement;
		},
		$config,
		1,
		$count
	);

	if ( null === $updated || 1 !== $count ) {
		wpc_fail( 'Could not find the table prefix in wp-config-sample.php.' );
	}

	return $updated;
}

$wpc_raw_args = isset( $_SERVER['argv'] ) && is_array( $_SERVER['argv'] )
	? array_slice( $_SERVER['argv'], 1 )
	: array();

foreach ( $wpc_raw_args as $wpc_arg ) {
	if ( '--' === $wpc_arg ) {
		continue;
	}

	if ( '--help' === $wpc_arg ) {
		wpc_usage();
		exit( 0 );
	}

	if ( '--debug' === $wpc_arg ) {
		$wpc_options['debug'] = true;
		continue;
	}

	if ( '--force' === $wpc_arg ) {
		$wpc_options['force'] = true;
		continue;
	}

	if ( preg_match( '/^--([a-z0-9-]+)=(.*)$/s', $wpc_arg, $wpc_match ) ) {
		$wpc_key = $wpc_match[1];

		if ( ! array_key_exists( $wpc_key, $wpc_options ) ) {
			wpc_fail( 'Unknown option: --' . $wpc_key );
		}

		if ( is_bool( $wpc_options[ $wpc_key ] ) ) {
			wpc_fail( 'Option --' . $wpc_key . ' does not take a value.' );
		}

		$wpc_options[ $wpc_key ] = $wpc_match[2];
		continue;
	}

	wpc_fail( 'Unknown argument: ' . $wpc_arg . ' (use --help for usage)' );
}

if ( '' === $wpc_options['db-name'] ) {
	wpc_fail( 'Missing --db-name=NAME.' );
}

if ( '' === $wpc_options['db-user'] ) {
	wpc_fail( 'Missing --db-user=USER.' );
}

if ( ! preg_match( '/^[A-Za-z0-9_]+$/', $wpc_options['prefix'] ) ) {
	wpc_fail( 'The table prefix may contain only letters, numbers, and underscores.' );
}

if ( ! file_exists( $wpc_sample ) ) {
	wpc_fail( 'Missing wp-config-sample.php.' );
}

if ( file_exists( $wpc_target ) && ! $wpc_options['force'] ) {
	wpc_fail( 'wp-config.php already exists. Use --force to replace it (this invalidates existing logins).' );
}

$wpc_config = file_get_contents( $wpc_sample );

if ( false === $wpc_config ) {
	wpc_fail( 'Could not read wp-config-sample.php.' );
}

$wpc_config = wpc_replace_constant( $wpc_config, 'DB_NAME', $wpc_options['db-name'] );
$wpc_config = wpc_replace_constant( $wpc_config, 'DB_USER', $wpc_options['db-user'] );
$wpc_config = wpc_replace_constant( $wpc_config, 'DB_PASSWORD', $wpc_options['db-password'] );
$wpc_config = wpc_replace_constant( $wpc_config, 'DB_HOST', $wpc_options['db-host'] );
$wpc_config = wpc_replace_constant( $wpc_config, 'GOOGLE_RECAPTCHA_SITE_KEY', $wpc_options['recaptcha-site-key'] );
$wpc_config = wpc_replace_constant( $wpc_config, 'GOOGLE_RECAPTCHA_SECRET_KEY', $wpc_options['recaptcha-secret-key'] );
$wpc_config = wpc_replace_prefix( $wpc_config, $wpc_options['prefix'] );
$wpc_config = wpc_replace_debug( $wpc_config, $wpc_options['debug'] );

try {
	foreach ( array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT' ) as $wpc_salt_name ) {
		$wpc_config = wpc_replace_constant( $wpc_config, $wpc_salt_name, bin2hex( random_bytes( 32 ) ) );
	}
} catch ( Exception $wpc_exception ) {
	wpc_fail( 'Could not generate authentication salts: ' . $wpc_exception->getMessage() );
}

if ( false !== strpos( $wpc_config, 'put your unique phrase here' ) ) {
	wpc_fail( 'The generated config still contains placeholder salts.' );
}

if ( false === file_put_contents( $wpc_target, $wpc_config ) ) {
	wpc_fail( 'Could not write wp-config.php.' );
}

@chmod( $wpc_target, 0600 );

echo "Created wp-config.php\n";
echo 'Database: ' . $wpc_options['db-name'] . "\n";
echo 'Table prefix: ' . $wpc_options['prefix'] . "\n";
echo "Authentication salts: 8 new random values\n";
echo "Next: open /wp-admin/install.php and complete the WordPress installer.\n";
