<?php
/**
 *	Plugin Name: WPay Pay
 *	Plugin URI: http://yellowduck.me/
 *	Description: Wordpress plugin to integrate PayPal on site
 *	Version: 0.1
 *	Requires at least: WP 4.5.2
 *	Author: Vitaly Kukin
 *	Author URI: http://yellowduck.me/
 *	License: SHAREWARE
 */

if ( !defined('WPPAY_VERSION') ) define( 'WPPAY_VERSION', '0.1' );
if ( !defined('WPPAY_PATH') ) define( 'WPPAY_PATH', plugin_dir_path( __FILE__ ) );

require( WPPAY_PATH . 'includes/core.php' );
require( WPPAY_PATH . 'includes/handler.php' );

if( is_admin() ) :
    require( WPPAY_PATH . 'includes/setup.php' );
    require( WPPAY_PATH . 'includes/template.class.php');
    require( WPPAY_PATH . 'includes/admin.php');
    require( WPPAY_PATH . 'includes/controller.php');
endif;


register_activation_hook( __FILE__, 'wppay_install' );
register_uninstall_hook( __FILE__, 'wppay_uninstall' );
register_activation_hook( __FILE__, 'wppay_activate' );