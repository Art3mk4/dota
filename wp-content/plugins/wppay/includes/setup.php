<?php
/**
 * User: Vitaly Kukin
 * Date: 12.05.2016
 * Time: 9:06
 */
/**
 * Setup the plugin
 */
function wppay_install(){

	global $wpdb;

	$charset_collate = !empty($wpdb->charset) ?
		"DEFAULT CHARACTER SET $wpdb->charset" :
		"DEFAULT CHARACTER SET CHARSET=utf8mb4";

	$sql = array(
		"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wppay_transaction` (
			  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
			  `token` VARCHAR(40) DEFAULT NULL,
			  `status` VARCHAR(20) DEFAULT NULL,
			  `type` VARCHAR(20) DEFAULT NULL,
			  `fullname` VARCHAR(255) DEFAULT NULL,
			  `country` VARCHAR(40) DEFAULT NULL,
			  `email` VARCHAR(100) NOT NULL,
			  `name` VARCHAR(255) DEFAULT NULL,
			  `description` TEXT DEFAULT NULL,
			  `payer_id` VARCHAR(20) DEFAULT NULL,
			  `period` VARCHAR(10) DEFAULT NULL,
			  `frequency` int(2) DEFAULT 1,
			  `amount` DECIMAL(12,2) DEFAULT '0.00',
			  `fullamount` DECIMAL(12,2) DEFAULT '0.00',
			  `coupon` VARCHAR(40) DEFAULT NULL,
			  `discount` VARCHAR(255) DEFAULT NULL,
			  `currency_code` CHAR(4) DEFAULT NULL,
			  `date` DATETIME DEFAULT '0000-00-00 00:00:00',
			  `date_update` DATETIME DEFAULT '0000-00-00 00:00:00',
			  `expiration_date` DATETIME DEFAULT '0000-00-00 00:00:00',
			  `details` TEXT DEFAULT NULL,
			  `response` TEXT DEFAULT NULL,
			  `subscription` INT(1) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY (`token`)
			) ENGINE = InnoDB {$charset_collate};",

	);

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	foreach($sql as $key)
		dbDelta($key);

	//wppay_upgrade_sql();

	update_site_option( 'wppay-version', WPPAY_VERSION  );
}

function wppay_upgrade_sql(){

	//global $wpdb;

	//maybe_add_column($wpdb->prefix . 'wppay_transaction', 'license', "ALTER TABLE `{$wpdb->prefix}wppay_transaction` ADD `license` varchar(255) NOT NULL;");
	//maybe_add_column($wpdb->prefix . 'wppay_transaction', 'review', "ALTER TABLE `{$wpdb->prefix}wppay_transaction` ADD `review` int(1) NOT NULL;");
}

function wppay_installed(){

	if ( !current_user_can('install_plugins') )
		return;

	if ( get_site_option( 'wppay-version' ) < WPPAY_VERSION )
		wppay_install();

	wppay_set_pay_settings();
}
add_action( 'admin_menu', 'wppay_installed' );
	
function wppay_activate(){

	wppay_installed();

	do_action( 'wppay_activate' );
}

function wppay_deactivate(){

	do_action( 'wppay_deactivate' );
}

function wppay_set_pay_settings(){

	$default = wppay_default_settings();

	if ( !get_site_option('wppay-settings') )
		update_site_option( 'wppay-settings', $default );

	if ( !get_site_option('wppay-mail-setting') )
		update_site_option( 'wppay-mail-setting', array('api' => '', 'logo' => '', 'from' => '', 'name' => '') );
}