<?php
/**
 *	Plugin Name: elPay
 *	Plugin URI: https://yellowduck.me/
 *	Description: Wordpress plugin to integrate Payment on site
 *	Version: 0.4
 *	Text Domain: elp
 *	Domain Path: /lang
 *	Requires at least: WP 4.4.2
 *	Author: Vitaly Kukin
 *	Author URI: http://yellowduck.me/
 *	License: SHAREWARE
 */

if ( !defined('ELP_VERSION') ) define( 'ELP_VERSION', '0.4' );
if ( !defined('ELP_PATH') ) define( 'ELP_PATH', plugin_dir_path( __FILE__ ) );

require( ELP_PATH . 'core/cron.php' );
require( ELP_PATH . 'core/core.php' );
require( ELP_PATH . 'sdk/paypal/paypal.php' );
require( ELP_PATH . 'core/handler.php' );

if( is_admin() ) :

    require( ELP_PATH . 'core/setup.php' );
    require( ELP_PATH . 'core/template.class.php' );
    require( ELP_PATH . 'core/admin.class.php' );
    require( ELP_PATH . 'core/controller.php' );

endif;


register_activation_hook( __FILE__, 'elp_install' );
register_uninstall_hook( __FILE__, 'elp_uninstall' );
register_activation_hook( __FILE__, 'elp_activate' );