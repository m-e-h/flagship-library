<?php
/**
 * General theme helper functions.
 *
 * @package     FlagshipLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2014, Flagship, LLC
 * @license     GPL-2.0+
 * @link        http://flagshipwp.com/
 * @since       1.0.0
 */

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
 * Outputs an entry's author.
 *
 * @since  1.1.0
 * @access public
 * @param  array   $args
 * @return void
 */
function flagship_entry_author( $args = array() ) {
	echo flagship_get_entry_author( $args );
}

/**
 * This is simply a wrapper function for hybrid_get_post_author which adds a few
 * filters to make the function a bit more flexible. This will allow us to avoid
 * passing args into the function by default in our templates. Instead, we can
 * filter the defaults globally which gives us a cleaner template file.
 *
 * @since  1.1.0
 * @access public
 * @param  array   $args
 * @return string
 */
function flagship_get_entry_author( $args = array() ) {

	$defaults = apply_filters( 'flagship_entry_author_defaults',
		array(
			'text'   => '%s',
			'before' => '',
			'after'  => '',
			'wrap'   => '<span %s>%s</span>',
		)
	);

	$args = wp_parse_args( $args, $defaults );

	return apply_filters( 'flagship_entry_author', hybrid_get_post_author( $args ), $args );
}

/**
 * Outputs a post's pulbished date.
 *
 * @since  1.1.0
 * @access public
 * @param  array   $args
 * @return void
 */
function flagship_entry_published( $args = array() ) {
	echo flagship_get_entry_published( $args );
}

/**
 * Function for getting the current post's author in The Loop and linking to the author archive page.
 * This function was created because core WordPress does not have template tags with proper translation
 * and RTL support for this.  An equivalent getter function for `the_author_posts_link()` would
 * instantly solve this issue.
 *
 * @since  1.1.0
 * @access public
 * @param  array   $args
 * @return string
 */
function flagship_get_entry_published( $args = array() ) {

	$output = '';

	$defaults = apply_filters( 'flagship_entry_published_defaults',
		array(
			'before' => '',
			'after'  => '',
			'wrap'   => '<time %s>%s</time>',
		)
	);

	$args = wp_parse_args( $args, $defaults );

	$output .= $args['before'];
	$output .= sprintf( $args['wrap'], hybrid_get_attr( 'entry-published' ), get_the_date() );
	$output .= $args['after'];

	return apply_filters( 'flagship_entry_published', $output, $args );
}

/**
 * Displays a formatted link to the current entry comments.
 *
 * @since  1.1.0
 * @access public
 * @param  array   $args
 * @return void
 */
function flagship_entry_comments_link( $args = array() ) {
	echo flagship_get_entry_comments_link( $args );
}

/**
 * Produces a formatted link to the current entry comments.
 *
 * Supported arguments are:
 * - after (output after link, default is empty string),
 * - before (output before link, default is empty string),
 * - hide_if_off (hide link if comments are off, default is 'enabled' (true)),
 * - more (text when there is more than 1 comment, use % character as placeholder
 *   for actual number, default is '% Comments')
 * - one (text when there is exactly one comment, default is '1 Comment'),
 * - zero (text when there are no comments, default is 'Leave a Comment').
 *
 * Output passes through 'flagship_get_entry_comments_link' filter before returning.
 *
 * @since  1.1.0
 * @param  array $args Empty array if no arguments.
 * @return string output
 */
function flagship_get_entry_comments_link( $args = array() ) {

	$defaults = apply_filters( 'flagship_entry_comments_link_defaults',
		array(
			'after'       => '',
			'before'      => '',
			'hide_if_off' => 'enabled',
			'more'        => __( '% Comments', 'compass' ),
			'one'         => __( '1 Comment', 'compass' ),
			'zero'        => __( 'Leave a Comment', 'compass' ),
		)
	);
	$args = wp_parse_args( $args, $defaults );

	if ( ! comments_open() && 'enabled' === $args['hide_if_off'] ) {
		return;
	}

	// I really would rather not do this, but WordPress is forcing me to.
	ob_start();
	comments_number( $args['zero'], $args['one'], $args['more'] );
	$comments = ob_get_clean();

	$comments = sprintf( '<a rel="nofollow" href="%s">%s</a>', get_comments_link(), $comments );

	$output = '<span class="entry-comments-link">' . $args['before'] . $comments . $args['after'] . '</span>';

	return apply_filters( 'flagship_entry_comments_link', $output, $args );

}

add_action( 'widgets_init', 'flagship_register_footer_widget_areas' );
/**
 * Register footer widget areas based on the number of widget areas the user
 * wishes to create with `add_theme_support()`.
 *
 * @since  1.0.0
 * @uses   register_sidebar() Register footer widget areas.
 * @return null Return early if there's no theme support.
 */
function flagship_register_footer_widget_areas() {
	// Get the current theme's support for footer widgets.
	$footer_widgets = get_theme_support( 'flagship-footer-widgets' );

	if ( ! $footer_widgets || ! isset( $footer_widgets[0] ) || ! is_numeric( $footer_widgets[0] ) ) {
		return;
	}

	$footer_widgets = (int) $footer_widgets[0];

	$counter = 1;

	while ( $counter <= $footer_widgets ) {
		hybrid_register_sidebar(
			array(
				'id'          => sprintf( 'footer-%d', $counter ),
				'name'        => sprintf( __( 'Footer %d', 'compass' ), $counter ),
				'description' => sprintf( __( 'Footer %d widget area.', 'compass' ), $counter ),
			)
		);

		$counter++;
	}
}

add_action( 'tha_footer_before', 'flagship_footer_widgets' );
/**
 * Displays all registered footer widget areas using a template.
 *
 * @since  1.0.0
 * @uses   locate_template() Load the footer widget template.
 * @return null Return early if there's no theme support.
 */
function flagship_footer_widgets() {
	// Get the current theme's support for footer widgets.
	$footer_widgets = get_theme_support( 'flagship-footer-widgets' );

	if ( ! $footer_widgets || ! isset( $footer_widgets[0] ) || ! is_numeric( $footer_widgets[0] ) ) {
		return;
	}

	// Return early if the first widget area has no widgets.
	if ( ! is_active_sidebar( 'footer-1' ) ) {
		return;
	}

	$footer_widgets = (int) $footer_widgets[0];

	$counter = 1;

	?>
	<div <?php hybrid_attr( 'footer-widgets' ); ?>>
		<div class="wrap">
			<?php while ( $counter <= $footer_widgets ) : ?>
				<?php include( locate_template( 'sidebar/footer-widgets.php' ) ); ?>
				<?php $counter++; ?>
			<?php endwhile; ?>
		</div>
	</div>
	<?php
}
