<?php
/**
 * Flagship Logo Init
 *
 * @package     FlagshipLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, Flagship Software, LLC
 * @license     GPL-2.0+
 * @link        https://flagshipwp.com/
 * @since       1.1.0
 */

add_action( 'init', 'flaghip_logo_init', 12 );
/**
 * Activate the Flagship Logo plugin.
 *
 * @since  1.1.0
 * @uses   current_theme_supports()
 * @return void
 */
function flaghip_logo_init() {
	// Return early if the standalone plugin and/or Jetpack module is activated.
	if ( class_exists( 'Site_Logo', false ) ) {
		return;
	}
	$dir = trailingslashit( dirname( __FILE__ ) );

	require_once $dir . 'classes/site-logo.php';

	if ( flagship_library()->is_customizer_preview() ) {
		require_once $dir . 'classes/site-logo-control.php';
	}
}

add_action( 'init', 'flagship_logo_class_loader', 13 );
/**
 * Because the Automattic and Jetpack Site Logo feature is hooked into init, we
 * need to hook in a little later to add our functionality. If one of the other
 * plugins is detected, we'll just return and allow them to function normally.
 *
 * @since  1.1.0
 * @uses   Flagship_Site_Logo::run()
 * @return object Site_Logo
 */
function flagship_logo_class_loader() {
	if ( ! class_exists( 'Flagship_Site_Logo', false ) ) {
		return null;
	}
	return flagship_site_logo()->run();
}

/**
 * Allow themes and plugins to access Flagship_Logo methods and properties.
 *
 * Because we aren't using a singleton pattern for this class, we need to make
 * sure it's only instantiated once through the helper function. Plugins and
 * themes should use the provided template tags, but if you need to access
 * methods inside our class for some reason, use this function.
 *
 * Example:
 *
 * <?php flagship_site_logo()->has_site_logo(); ?>
 *
 * @since  1.1.0
 * @uses   Flagship_Site_Logo
 * @return object Flagship_Site_Logo
 */
function flagship_site_logo() {
	static $extension;
	if ( null === $extension ) {
		$extension = new Flagship_Site_Logo();
	}
	return $extension;
}
