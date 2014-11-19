<?php
/**
 * General template helper functions.
 *
 * @package     FlagshipLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2014, Flagship Software, LLC
 * @license     GPL-2.0+
 * @link        http://flagshipwp.com/
 * @since       1.0.0
 */

add_action( 'wp_head', 'flagship_load_favicon' );
/**
 * Echos a favicon link if one is found and falls back to the default Flagship
 * theme favicon when no custom one has been set.
 *
 * URL to favicon is filtered via `flagship_favicon_url` before being echoed.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function flagship_load_favicon() {
	$parent_uri = trailingslashit( get_template_directory_uri() );
	$child_uri  = trailingslashit( get_stylesheet_directory_uri() );

	$favicon = $parent_uri . 'images/favicon.ico';

	//* Allow child theme to short-circuit this function
	$pre = apply_filters( 'flagship_pre_load_favicon', false );

	if ( $pre !== false ) {
		$favicon = $pre;
	}
	elseif ( file_exists( $child_uri . 'images/favicon.ico' ) ) {
		$favicon = $child_uri . 'images/favicon.ico';
	}

	$favicon = apply_filters( 'flagship_favicon_url', $favicon );

	if ( $favicon ) {
		echo '<link rel="Shortcut Icon" href="' . esc_url( $favicon ) . '" type="image/x-icon" />' . "\n";
	}
}

/**
 * Sets a common class, `.nav-menu`, for the custom menu widget if used in the
 * header right sidebar.
 *
 * @since  1.0.0
 * @param  array $args Header menu args.
 * @return array $args Modified header menu args.
 */
function flagship_header_menu_args( $args ) {
	$args['menu_class'] .= ' nav-menu';
	return $args;
}

/**
 * Wrap the header navigation menu in its own nav tags with markup API.
 *
 * @since  1.0.0
 * @param  $menu Menu output.
 * @return string $menu Modified menu output.
 */
function flagship_header_menu_wrap( $menu ) {
	return sprintf( '<nav %s>', hybrid_get_attr( 'widget-menu', 'header' ) ) . $menu . '</nav>';
}

add_filter( 'get_search_form', 'flagship_get_search_form' );
/**
 * Customize the search form to improve accessibility.
 *
 * @since  1.0.0
 * @return string Search form markup.
 */
function flagship_get_search_form() {
	$search = new Flagship_Search_Form;
	return $search->get_form();
}

/**
 * Returns a formatted theme credit link.
 *
 * @since  1.1.0
 * @access public
 * @return string
 */
function flagship_get_credit_link() {
	$theme = wp_get_theme( get_template() );
	$uri   = $theme->get( 'AuthorURI' );
	$name  = $theme->display( 'Author', false, true );

	/* Translators: Theme name. */
	$title = sprintf( __( 'Purpose-Built WordPress Theme by %s', 'flagship-library' ), $name );

	return sprintf( '<a class="author-link" href="%s" title="%s">%s</a>', esc_url( $uri ), esc_attr( $title ), $name );
}
