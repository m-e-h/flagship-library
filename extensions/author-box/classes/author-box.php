<?php
/**
 * General theme helper functions.
 *
 * @package     FlagshipLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, Flagship Software, LLC
 * @license     GPL-2.0+
 * @link        https://flagshipwp.com/
 * @since       1.4.0
 */

/**
 * A class to register settings and load templates for author boxes.
 *
 * @package FlagshipLibrary
 */
class Flagship_Author_Box {

	protected $lib_dir;

	/**
	 * Get our class up and running!
	 *
	 * @since  1.4.0
	 * @access public
	 * @uses   Flagship_Author_Box::$wp_hooks
	 * @return void
	 */
	public function run() {
		$this->lib_dir = flagship_library()->get_library_directory();
		self::wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  1.4.0
	 * @access public
	 * @return void
	 */
	private function wp_hooks() {
		add_filter( 'hybrid_attr_author-box',   array( $this, 'attr_author_box' ), 10, 2 );
		add_action( 'tha_entry_after',          array( $this, 'author_box_single' ) );
		add_action( 'tha_content_top',          array( $this, 'author_box_archive' ) );
		if ( ! is_admin() ) {
			return;
		}
		add_filter( 'user_contactmethods',      array( $this, 'user_contactmethods' ) );
		add_action( 'show_user_profile',        array( $this, 'user_fields' ) );
		add_action( 'edit_user_profile',        array( $this, 'user_fields' ) );
		add_action( 'personal_options_update',  array( $this, 'meta_save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'meta_save' ) );
	}

	/**
	 * Author box attributes.
	 *
	 * @since  1.4.0
	 * @access public
	 * @param  array $attr
	 * @param  string $context
	 * @return array
	 */
	public function attr_author_box( $attr, $context ) {
		$class      = 'author-box';
		$attr['id'] = 'author-box';

		if ( ! empty( $context ) ) {
			$attr['id'] = "author-box-{$context}";
			$class    .= " author-box-{$context}";
		}

		$attr['class']     = $class;
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/Person';
		$attr['itemprop']  = 'author';

		return $attr;
	}

	/**
	 * Displays the single author box using a template.
	 *
	 * @since  1.4.0
	 * @access public
	 * @uses   locate_template() Load the single author box template.
	 * @return void
	 */
	public function author_box_single() {
		if ( ! is_singular( apply_filters( 'flagship_author_box_types', array( 'post' ) ) ) ) {
			return;
		}

		$display = get_the_author_meta( 'flagship_author_box_single' );

		// Bail if display is disabled. Continue if no author meta exists.
		if ( '' !== $display && 0 === absint( $display ) ) {
			return;
		}

		// Use the theme's single author box template if it exists.
		if ( '' !== locate_template( 'flagship/author-box-single.php' ) ) {
			return require_once locate_template( 'flagship/author-box-single.php' );
		}
		require_once $this->lib_dir . '/templates/author-box-single.php';
	}

	/**
	* Displays the archive author box using a template.
	 *
	 * @since  1.4.0
	 * @access public
	 * @uses   locate_template() Load the archive author box template.
	 * @return void
	 */
	public function author_box_archive() {
		if ( ! is_author() || is_paged() ) {
			return;
		}
		$display = get_the_author_meta( 'flagship_author_box_archive' );

		// Bail if display is disabled or no author meta exists.
		if ( '' === $display || '0' === $display ) {
			return;
		}
		$types = apply_filters( 'flagship_author_box_types', array( 'post' ) );

		if ( ! in_array( get_post_type(), (array) $types ) ) {
			return;
		}

		// Use the theme's archive author box template if it exists.
		if ( '' !== locate_template( 'flagship/author-box-archive.php' ) ) {
			return require_once locate_template( 'flagship/author-box-archive.php' );
		}
		require_once $this->lib_dir . '/templates/author-box-archive.php';
	}

	/**
	 * Add additional contact methods for registered users.
	 *
	 * @since  1.4.0
	 * @access public
	 * @param  array $contactmethods Existing contact methods.
	 * @return array $contactmethods Modifed contact methods.
	 */
	public function user_contactmethods( array $contactmethods ) {
		$contactmethods['googleplus'] = __( 'Google+', 'flagship-library' );
		$contactmethods['twitter']    = __( 'Twitter (Without @)', 'flagship-library' );
		$contactmethods['facebook']   = __( 'Facebook', 'flagship-library' );
		return $contactmethods;
	}

	/**
	 * Add fields for author archives contents to the user edit screen.
	 *
	 * @since  1.4.0
	 * @access public
	 * @param  $user Object WordPress user object.
	 * @return void
	 */
	public function user_fields( $user ) {
		if ( ! current_user_can( 'edit_users', $user->ID ) ) {
			return false;
		}
		$single_box  = get_the_author_meta( 'flagship_author_box_single',  $user->ID );
		$archive_box = get_the_author_meta( 'flagship_author_box_archive', $user->ID );
		// Set the single author box to enabled when no author meta has been set.
		if ( '' === $single_box ) {
			$single_box = 1;
		}
		require_once $this->lib_dir . '/templates/admin/settings-author-box.php';
	}

	/**
	 * Update user meta when user edit page is saved.
	 *
	 * @since  1.4.0
	 * @access public
	 * @param  $user_id integer The current user ID
	 * @return void
	 */
	public function meta_save( $user_id ) {
		if ( ! current_user_can( 'edit_users', $user_id ) ) {
			return;
		}

		$defaults = array(
			'flagship_author_box_single'  => 0,
			'flagship_author_box_archive' => 0,
		);

		if ( ! isset( $_POST['flagbox'] ) || ! is_array( $_POST['flagbox'] ) ) {
			foreach ( $defaults as $key => $value ) {
				update_user_meta( $user_id, $key, $value );
			}
			return;
		}

		$meta = wp_parse_args( $_POST['flagbox'], $defaults );

		foreach ( $meta as $key => $value ) {
			update_user_meta( $user_id, sanitize_key( $key ), absint( $value ) );
		}
	}

}
