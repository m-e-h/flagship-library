<?php
/**
 * Rather than writing basic sanitization and registration methods every time we
 * want to hook into the WordPress customizer, we should try to reuse code as
 * much as possible. This abstract class allows us to add any type of customizer
 * setting we like and reference our existing methods within the child class.
 *
 * @package     FlagshipLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2014, Flagship Software, LLC
 * @license     GPL-2.0+
 * @link        http://flagshipwp.com/
 * @since       1.1.1
 */

/**
 * Flagship_Customizer_Base
 *
 * An abstract class to provide basic helper methods to use when registering new
 * customizer sections within a theme.
 *
 * @since   1.1.1
 * @version 1.0.0
 */
abstract class Flagship_Customizer_Base {

	/**
	 * A default capability required for customizer options.
	 *
	 * @since 1.1.1
	 * @var   string
	 */
	protected $capability = 'edit_theme_options';

	/**
	 * Get our class up and running!
	 *
	 * @since  1.1.1
	 * @access public
	 * @uses   Flagship_Customizer_Base::$customizer_hooks
	 * @return void
	 */
	public function run() {
		self::customizer_hooks();
	}

	/**
	 * Define defaults, call the `register` method, add css to head.
	 *
	 * @since  1.1.1
	 * @access public
	 * @return void
	 */
	protected function customizer_hooks() {
		// Throw a warning if no register method exists in the child class.
		if ( ! method_exists( $this, 'register' ) ) {
			_doing_it_wrong(
				'Flagship_Customizer_Base',
				__( 'When extending Flagship_Customizer_Base, you must create a register method.', 'flagship-library' )
			);
		}
		// Register our customizer sections.
		add_action( 'customize_register', array( $this, 'register' ), 15 );

		// Register customizer scripts if the child class has added any.
		if ( method_exists( $this, 'scripts' ) ) {
			add_action( 'customize_preview_init', array( $this, 'scripts' ) );
		}
	}

	/**
	 * Sanitize a string to allow only tags in the allowedtags array.
	 *
	 * @since  1.1.1
	 * @param  string $string The unsanitized string.
	 * @return string The sanitized string.
	 */
	public function sanitize_text( $string ) {
		global $allowedtags;
		return wp_kses( $string , $allowedtags );
	}

	/**
	 * Sanitize a checkbox to only allow 0 or 1
	 *
	 * @since  1.1.1
	 * @access public
	 * @param  $input
	 * @return int
	 */
	public function sanitize_checkbox( $input ) {
		return ( 1 === absint( $input ) ) ? 1 : 0;
	}

	/**
	 * Sanitize a value from a list of allowed values.
	 *
	 * @since  1.1.1
	 * @access public
	 * @param  mixed $value The value to sanitize.
	 * @param  mixed $setting The setting for which the sanitizing is occurring.
	 * @return mixed The sanitized value.
	 */
	public function sanitize_choices( $choices, $setting, $default = '' ) {
		if ( is_object( $setting ) ) {
			$setting = $setting->id;
		}

		$allowed_choices = array_keys( $choices );

		if ( ! in_array( $value, $allowed_choices ) ) {
			$value = $default;
		}

		return $value;
	}

	/**
	 * Sanitize the url of uploaded media.
	 *
	 * @since  1.1.1
	 * @access public
	 * @param  string $value The url to sanitize
	 * @return string $output The sanitized url.
	 */
	public function sanitize_file_url( $url ) {
		$output = '';

		$filetype = wp_check_filetype( $url );
		if ( $filetype['ext'] ) {
			$output = esc_url( $url );
		}

		return $output;
	}

	/**
	 * Sanitizes a hex color.
	 *
	 * Returns either '', a 3 or 6 digit hex color (with #), or null.
	 * For sanitizing values without a #, see sanitize_hex_color_no_hash().
	 *
	 * @since  1.1.1
	 * @access public
	 * @param  string $color
	 * @return string|null
	 */
	public function sanitize_hex_color( $color ) {
		if ( '' === $color ) {
			return '';
		}

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}

		return null;
	}

}
