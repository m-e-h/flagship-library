<?php
/**
 * Flagship Author Box Init
 *
 * @package     FlagshipLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, Flagship Software, LLC
 * @license     GPL-2.0+
 * @link        https://flagshipwp.com/
 * @since       1.1.0
 */

// Include our required extension files.
require_once trailingslashit( dirname( __FILE__ ) ) . 'classes/author-box.php';

/**
 * Allow themes and plugins to access Flagship_Author_Box methods and
 * properties.
 *
 * Because we aren't using a singleton pattern for this class, we need to make
 * sure it's only instantiated once through the helper function. Plugins and
 * themes shouldn't need to access anything in this class, but if you need to
 * for some reason, use this function.
 *
 * Example:
 *
 * <?php Flagship_Author_Box()->single_author_box(); ?>
 *
 * @uses   Flagship_Author_Box
 * @return object Flagship_Author_Box
 */
function flagship_author_box() {
	static $extension;
	if ( null === $extension ) {
		$extension = new Flagship_Author_Box();
	}
	return $extension;
}

// Get Flagship footer widgets running!
flagship_author_box()->run();
