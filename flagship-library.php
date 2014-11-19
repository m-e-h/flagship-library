<?php
/**
 * Load all required library files.
 *
 * @package     FlagshipLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2014, Flagship Software, LLC
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
		 * @type  string
		 */
		protected $prefix;

		/**
		 * Static placeholder for our main class instance.
		 *
		 * @since 1.0.0
		 * @var   Flagship_Library
		 */
		private static $instance;

		/**
		 * Constructor method to initialize the class.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args {
		 *     Configuration options. Optional
		 *
		 *    @type string $prefix  Optional. Theme prefix. Defaults to the template name.
		 *    @type array  $strings List of internationalized strings.
		 * }
		 */
		public function __construct( $args = array() ) {
			$this->prefix = empty( $args['prefix'] ) ? get_template() : sanitize_key( $args['prefix'] );
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since  1.0.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp' ), '1.0' );
		}

		/**
		 * Main Flagship_Library Instance
		 *
		 * Insures that only one instance of Flagship_Library exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @static
		 * @uses   Flagship_Library::includes() Include the required files
		 * @return Flagship_Library
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Flagship_Library ) ) {
				self::$instance = new Flagship_Library;
				self::$instance->includes();
				self::$instance->extensions_includes();
			}
			return self::$instance;
		}

		/**
		 * Whether the current request is a Customizer preview.
		 *
		 * @since   1.0.0
		 * @access  public
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
		 * @access  public
		 * @return  bool
		 */
		public function is_wpcom() {
			return defined( 'IS_WPCOM' ) && true === IS_WPCOM;
		}

		/**
		 * Return the correct path to the flagship library directory.
		 *
		 * @since   1.0.0
		 * @access  public
		 * @return  string
		 */
		public function get_library_directory() {
			return apply_filters( 'flagship_library_directory', dirname( __FILE__ ) );
		}

		/**
		 * Return the correct path to the flagship library directory.
		 *
		 * @since   1.1.0
		 * @access  public
		 * @return  string
		 */
		public function get_library_uri() {
			return str_replace( realpath( get_theme_root() ), get_theme_root_uri(), realpath( $this->get_library_directory() ) );
		}

		/**
		 * Include required library files.
		 *
		 * If for some reason you would prefer that a particular file isn't
		 * loaded you can use the flagship_library_includes filter to unset it
		 * before the includes runs.
		 *
		 * @since   1.0.0
		 * @access  private
		 * @return  void
		 */
		private function includes() {
			// Set up an array of library file paths which can be filtered.
			$includes = apply_filters( 'flagship_library_includes',
				array(
					'classes/class-search-form.php',
					'functions/attr.php',
					'functions/seo.php',
					'functions/template-entry.php',
					'functions/template-general.php',
					'functions/template.php',
				)
			);
			// Include our library files.
			foreach ( $includes as $include ) {
				require_once trailingslashit( $this->get_library_directory() ) . $include;
			}
		}

		/**
		 * Include extensions init files only when theme support has been added.
		 *
		 * @since   1.1.0
		 * @access  private
		 * @return  void
		 */
		private function extensions_includes() {
			$extensions_dir = trailingslashit( $this->get_library_directory() ) . 'extensions/';
			require_if_theme_supports( 'site-logo', $extensions_dir . 'site-logo/init.php' );
			require_if_theme_supports( 'breadcrumb-trail', $extensions_dir . 'breadcrumb-display/init.php' );
			require_if_theme_supports( 'flagship-footer-widgets', $extensions_dir . 'footer-widgets/init.php' );
		}

	}
}

if ( ! function_exists( 'flagship_library' ) ) {
	/**
	 * Grab an instance of the main library class. If you need to reference a
	 * method in the class for some reason, do it using this function.
	 *
	 * Example:
	 *
	 * <?php flagship_library()->is_customizer_preview(); ?>
	 *
	 * @version 1.0.0
	 */
	function flagship_library() {
		return Flagship_Library::instance();
	}
}

// Get the library up and running.
flagship_library();
