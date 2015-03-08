<?php
/**
 * Additional helper functions that the library or themes may use.
 *
 * @package     FlagshipLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, Flagship Software, LLC
 * @license     GPL-2.0+
 * @link        https://flagshipwp.com/
 * @since       1.1.1
 */

/**
 * A helper function to simplify the process of building complicated styles
 * based on user input within the WordPress customizer.
 *
 * Example:
 *
 * <?php flagship_style_builder()->add( $data ); ?>
 *
 * @since   1.1.1
 * @access  public
 * @uses    Flagship_Style_Builder
 * @see     /classes/class-style-builder.php
 * @return  object Flagship_Style_Builder
 */
function flagship_style_builder() {
	static $builder;
	if ( null === $builder ) {
		$builder = new Flagship_Style_Builder();
	}
	return $builder;
}

/**
 * Display our breadcrumbs based on selections made in the WordPress customizer.
 *
 * @since  1.1.0
 * @access public
 * @return bool true if both our template tag and theme mod return true.
 */
function flagship_display_breadcrumbs() {
	// Return early if our theme doesn't support breadcrumbs.
	if ( ! function_exists( 'flagship_breadcrumb_display' ) ) {
		return false;
	}
	// Grab our available breadcrumb display options.
	$options = array_keys( flagship_breadcrumb_display()->get_options() );
	// Set up an array of template tags to map to our breadcrumb display options.
	$tags = apply_filters( 'flagship_breadcrumb_tags',
		array(
			is_singular() && ! is_attachment() && ! is_page(),
			is_page(),
			is_home() && ! is_front_page(),
			is_archive(),
			is_404(),
			is_attachment(),
		)
	);

	// Loop through our theme mods to see if we have a match.
	foreach ( array_combine( $options, $tags ) as $mod => $tag ) {
		// Return true if we find an enabled theme mod within the correct section.
		if ( 1 === absint( get_theme_mod( $mod, 0 ) ) && true === $tag ) {
			return true;
		}
	}
	return false;
}

/**
 * Retrieve the site logo URL or ID (URL by default). Pass in the string
 * 'id' for ID.
 *
 * @since  1.1.0
 * @uses   Flagship_Site_Logo::get_flagship_logo
 * @param  string $format the format to return
 * @return mixed The URL or ID of our site logo, false if not set
 */
function flagship_get_logo( $format = 'url' ) {
	if ( ! class_exists( 'Flagship_Site_Logo', false ) ) {
		if ( function_exists( 'jetpack_the_site_logo' ) ) {
			return jetpack_get_site_logo( $format );
		}
		if ( function_exists( 'the_site_logo' ) ) {
			return get_site_logo( $format );
		}
		return null;
	}
	return flagship_site_logo()->get_flagship_logo( $format );
}

/**
 * Determine if a site logo is assigned or not.
 *
 * @since  1.1.0
 * @uses   Flagship_Site_Logo::has_site_logo
 * @return boolean True if there is an active logo, false otherwise
 */
function flagship_has_logo() {
	if ( ! class_exists( 'Flagship_Site_Logo', false ) ) {
		if ( function_exists( 'jetpack_the_site_logo' ) ) {
			return jetpack_has_site_logo();
		}
		if ( function_exists( 'the_site_logo' ) ) {
			return has_site_logo();
		}
		return null;
	}
	return flagship_site_logo()->has_site_logo();
}

/**
 * Output an <img> tag of the site logo, at the size specified
 * in the theme's add_theme_support() declaration.
 *
 * @since  1.1.0
 * @uses   Flagship_Site_Logo::the_site_logo
 * @return void
 */
function flagship_the_logo() {
	if ( ! class_exists( 'Flagship_Site_Logo', false ) ) {
		if ( function_exists( 'jetpack_the_site_logo' ) ) {
			jetpack_the_site_logo();
			return;
		}
		if ( function_exists( 'the_site_logo' ) ) {
			the_site_logo();
			return;
		}
		return;
	}
	flagship_site_logo()->the_site_logo();
}
