<?php
/**
 * Load all required library files.
 *
 * @package     FlagshipLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2014, Flagship, LLC
 * @license     GPL-2.0+
 * @link        http://flagshipwp.com/
 * @since       1.0.0
 */

if ( ! class_exists( 'Flagship_Library' ) ) {

	/**
	 * Class for common Flagship theme functionality.
	 *
	 * @version 1.0.0
	 */
	class Flagship_Library {

		/**
		 * Prefix to prevent conflicts.
		 *
		 * Used to prefix filters to make them unique.
		 *
		 * @since 1.0.0
		 * @type string
		 */
		protected $prefix;

		/**
		 * Constructor method to initialize the class.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args {
		 *    Configuration options. Optional
		 *
		 *    @type string $prefix  Optional. Theme prefix. Defaults to the template name.
		 *    @type array  $strings List of internationalized strings.
		 * }
		 */
		public function __construct( $args = array() ) {
			$this->prefix = empty( $args['prefix'] ) ? get_template() : sanitize_key( $args['prefix'] );
			self::includes();
		}

		/**
		 * Whether the current request is a Customizer preview.
		 *
		 * @since   1.0.0
		 * @return  bool
		 */
		public function is_customizer_preview() {
			global $wp_customize;
			return $wp_customize instanceof WP_Customize_Manager && $wp_customize->is_preview();
		}

		/**
		 * Whether the current environment is WordPress.com.
		 *
		 * @since   1.0.0
		 * @return  bool
		 */
		public function is_wpcom() {
			return defined( 'IS_WPCOM' ) && true === IS_WPCOM;
		}

		/**
		 * Return the correct path to the flagship library directory.
		 *
		 * @since   1.0.0
		 * @return  string
		 */
		public function get_library_directory() {
			return apply_filters( 'flagship_library_directory', dirname( __FILE__ ) );
		}

		/**
		 * Whether the current environment is WordPress.com.
		 *
		 * @since   1.0.0
		 * @return  bool
		 */
		public function includes() {
			$library_dir = $this->get_library_directory();
			require_once $library_dir . '/classes/class-search-form.php';
			require_once $library_dir . '/functions/attributes.php';
			require_once $library_dir . '/functions/hybrid-tweaks.php';
			require_once $library_dir . '/functions/template-helpers.php';
		}
	}
}
