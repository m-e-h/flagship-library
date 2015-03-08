<?php
/**
 * Deprecated functions that are no longer recommended and will be removed.
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
	return flagship_library()->style_builder;
}

/**
 * Sets a common class, `.nav-menu`, for the custom menu widget if used in the
 * header right sidebar.
 *
 * @deprecated This is no longer recommended. Use flagship_widget_menu_args instead.
 * @since  1.0.0
 * @access public
 * @param  array $args Header menu args.
 * @return array $args Modified header menu args.
 */
function flagship_header_menu_args( $args ) {
	_deprecated_function( __FUNCTION__, '1.3.0', 'flagship_widget_menu_args' );
	return flagship_widget_menu_args( $args );
}

/**
* Allow themes and plugins to access Flagship_Breadcrumb_Display methods and
* properties.
*
* Legacy function no longer in use. This will be removed on the next major
* release. Recommend accessing the public variable directly.
 *
 * @deprecated 1.4.0
 * @since  1.1.0
 * @return object Flagship_Breadcrumb_Display
 */
function flagship_breadcrumb_display() {
	return flagship_library()->breadcrumb_display;
}

/**
 * Allow themes and plugins to access Flagship_Site_Logo methods and properties.
 *
 * Legacy function no longer in use. This will be removed on the next major
 * release. Recommend accessing the public variable directly.
 *
 * @deprecated 1.4.0
 * @since  1.1.0
 * @uses   Flagship_Site_Logo
 * @return object Flagship_Site_Logo
 */
function flagship_site_logo() {
	return flagship_library()->site_logo;
}

/**
 * Because the Automattic and Jetpack Site Logo feature is hooked into init, we
 * need to hook in a little later to add our functionality. If one of the other
 * plugins is detected, we'll just return and allow them to function normally.
 *
 * @deprecated 1.4.0
 * @since  1.1.0
 * @uses   Flagship_Site_Logo::run()
 * @return object Site_Logo
 */
function flagship_logo_class_loader() {}

/**
 * Allow themes and plugins to access Flagship_Footer_Widgets methods and
 * properties.
 *
 * Legacy function no longer in use. This will be removed on the next major
 * release. Recommend accessing the public variable directly.
 *
 * @deprecated 1.4.0
 * @since  1.1.0
 * @uses   Flagship_Footer_Widgets
 * @return object Flagship_Footer_Widgets
 */
function flagship_footer_widgets() {
	return flagship_library()->footer_widgets;
}

/**
 * Legacy function no longer in use. This will be removed on the next major
 * release. Originally used to register footer widget areas.
 *
 * @deprecated 1.4.0
 * @since  1.1.0
 * @return null
 */
function flagship_register_footer_widget_areas() {}
