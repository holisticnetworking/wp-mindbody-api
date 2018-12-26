<?php
/**
 * PSR4 autoloader using spl_autoload_register
 * @package HN_Reactive
 * @author Thomas J Belknap <tbelknap@holisticnetworking.net>
 */

namespace HN_Reactive;

/**
 * Format the incoming class name to the WordPress file naming standard.
 * @param $class
 * @throws \Exception e
 * @return string
 */
spl_autoload_register( function ( $class ) {
	$wordpress = wordpress_include( $class );

	if ( file_exists( plugin_dir_path( __FILE__ ) . $wordpress . '.php' ) ) {
		require_once( plugin_dir_path( __FILE__ ) . $wordpress . '.php' );
	} else {
		try {
			if ( file_exists( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) ) {
				require_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );
			} else {
				throw new \Exception( 'Unable to locate file ' );
			}
		} catch ( \Exception $e ) {
			error_log(sprintf(
				'Unable to load library %1$s. Error message: %2$s',
				$class,
				$e->getMessage()
			));
			error_log( $e->getTraceAsString() );
			die( '<h1>Sorry! There has been an error rendering your request.</h1> If the problem persists, please contact a site administrator.' );
		}
	}
} );

/**
 * Format the incoming class name to the WordPress file naming standard.
 * @param $class
 * @return string
 */
function wordpress_include( $class ) {
	$parts = explode( '\\', $class );
	// Remove HN_Reactive index:
	unset( $parts[0] );
	if ( ! empty( $parts ) ) :
		$parts[ count( $parts ) ] = 'class-' . wp_class_to_file( $parts[ count( $parts ) ] );
		$class_path               = implode( '/', $parts );
		return $class_path;
	else :
		return false;
	endif;
}

/**
 * Translates a WordPress-valid class name to a WordPress-valid file name (e.g. Class_Name - class-name)
 * @param    string   $str    String in camel case format
 * @return    string            $str Translated into dashed format
 */
function wp_class_to_file( $str ) {
	return implode(
		'-',
		explode(
			'_',
			preg_replace_callback(
				'/[A-Z]*?/',
				function( $matches ) {
					return strtolower( $matches[0] );
				},
				$str
			)
		)
	);
}