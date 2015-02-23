<?php
/**
 * General template helper functions.
 *
 * @package     FlagshipLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, Flagship Software, LLC
 * @license     GPL-2.0+
 * @link        https://flagshipwp.com/
 * @since       1.0.0
 */

add_action( 'wp_head', 'flagship_load_favicon', 5 );
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
	$favicon = '';
	$path    = 'images/favicon.ico';

	// Use the child theme favicon if it exists.
	if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $path ) ) {
		$favicon = trailingslashit( get_stylesheet_directory_uri() ) . $path;
	}
	// Fall back to the parent favicon if it exists.
	if ( file_exists( trailingslashit( get_template_directory() ) . $path ) ) {
		$favicon = trailingslashit( get_template_directory_uri() ) . $path;
	}

	// Allow developers to set a custom favicon file.
	$favicon = apply_filters( 'flagship_favicon_url', $favicon );

	// Bail if we don't have a favicon to display.
	if ( empty( $favicon ) ) {
		return;
	}

	echo '<link rel="Shortcut Icon" href="' . esc_url( $favicon ) . '" type="image/x-icon" />' . "\n";
}

/**
 * Sets a common class, `.nav-menu`, for the custom menu widget if used in the
 * header right sidebar.
 *
 * @since  1.0.0
 * @access public
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
 * @access public
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
 * @access public
 * @return string Search form markup.
 */
function flagship_get_search_form() {
	$search = new Flagship_Search_Form;
	return $search->get_form();
}

/**
 * Outputs a navigation element for a singular entry.
 *
 * @since  1.3.0
 * @access public
 * @param  $args array
 * @return void
 */
function flagship_singular_nav( $args = array() ) {
	echo flagship_get_singular_nav( $args );
}

/**
 * Helper function to build a next and previous posts navigation element on
 * single entries. This takes care of all the annoying formatting which usually
 * would need to be done within a template.
 *
 * @since  1.3.0
 * @access public
 * @param  $args array
 * @return string
 */
function flagship_get_singular_nav( $args = array() ) {
	$defaults = apply_filters( 'flagship_singular_nav_defaults',
		array(
			'post_types'     => array( 'post', ),
			'prev_format'    => '<span class="nav-previous">%link</span>',
			'next_format'    => '<span class="nav-next">%link</span>',
			'prev_link'      => __( 'Previous Post', 'flagship' ),
			'next_link'      => __( 'Next Post', 'flagship' ),
			'in_same_term'   => false,
			'excluded_terms' => '',
			'taxonomy'       => 'category',
		)
	);

	$args = wp_parse_args( $args, $defaults );

	if ( ! is_singular( $args['post_types'] ) ) {
		return;
	}

	$output = '';

	$output .= '<nav ' . hybrid_get_attr( 'nav', 'single' ) . '>';

	// Seriously, WordPress?
	ob_start();
	previous_post_link(
		$args['prev_format'],
		$args['prev_link'],
		$args['in_same_term'],
		$args['excluded_terms'],
		$args['taxonomy']
	);
	next_post_link(
		$args['next_format'],
		$args['next_link'],
		$args['in_same_term'],
		$args['excluded_terms'],
		$args['taxonomy']
	);
	$output .= ob_get_clean();

	$output .= '</nav><!-- .nav-single -->';

	return apply_filters( 'flagship_singular_nav', $output, $args );
}

/**
 * Outputs a navigation element for a loop.
 *
 * @since  1.3.0
 * @access public
 * @param  $args array
 * @return void
 */
function flagship_loop_nav( $args = array() ) {
	echo flagship_get_loop_nav( $args );
}

/**
 * Helper function to build a newer/older or paginated navigation element within
 * a loop of multiple entries. This takes care of all the annoying formatting
 * which usually would need to be done within a template. This defaults to a
 * pagination format unless the site is using a version of WordPress older than
 * 4.1. For older sites, we fall back to the next and previous post links by
 * default.
 *
 * @since  1.3.0
 * @access public
 * @param  $args array
 * @return string
 */
function flagship_get_loop_nav( $args = array() ) {
	global $wp_query;
	// Return early if we're on a singular post or we only have one page.
	if ( is_singular() || 1 === $wp_query->max_num_pages ) {
		return;
	}

	$defaults = apply_filters( 'flagship_loop_nav_defaults',
		array(
			'format'         => 'pagination',
			'prev_text'      => sprintf( '<span class="screen-reader-text">%s</span>' , __( 'Previous Page', 'flagship' ) ),
			'next_text'      => sprintf( '<span class="screen-reader-text">%s</span>', __( 'Next Page', 'flagship' ) ),
			'prev_link_text' => __( 'Newer Posts', 'flagship' ),
			'next_link_text' => __( 'Older Posts', 'flagship' ),
		)
	);

	$args = wp_parse_args( $args, $defaults );

	$output = '';

	$output .= '<nav ' . hybrid_get_attr( 'nav', 'archive' ) . '>';
	$output .= sprintf( '<span class="nav-previous">%s</span>', get_previous_posts_link( $args['prev_link_text'] ) );
	$output .= sprintf( '<span class="nav-next">%s</span>', get_next_posts_link( $args['next_link_text'] ) );
	$output .= '</nav><!-- .nav-archive -->';

	if ( function_exists( 'the_posts_pagination' ) && 'pagination' === $args['format'] ) {
		$output = get_the_posts_pagination(
			array(
				'prev_text' => $args['prev_text'],
				'next_text' => $args['next_text'],
			)
		);
	}

	return apply_filters( 'flagship_loop_nav', $output, $args );
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

	// Translators: Theme name.
	$title = sprintf( __( 'Purpose-Built WordPress Theme by %s', 'flagship-library' ), $name );

	return sprintf( '<a class="author-link" href="%s" title="%s">%s</a>', esc_url( $uri ), esc_attr( $title ), $name );
}
