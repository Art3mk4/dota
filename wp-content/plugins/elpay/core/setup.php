<?php

/**
 * Setup the plugin
 */
function elp_install(){

	global $wpdb;

	$charset_collate = !empty($wpdb->charset) ?
		"DEFAULT CHARACTER SET $wpdb->charset" :
		"DEFAULT CHARACTER SET CHARSET=utf8mb4";

	$sql = array(
		"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}elpay_transaction` (
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

		"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}elpay_transaction_log` (
			  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
			  `tnx_id` BIGINT(20) NOT NULL,
			  `amount` DECIMAL(12,2) DEFAULT '0.00',
			  `fullamount` DECIMAL(12,2) DEFAULT '0.00',
			  `date` DATETIME DEFAULT '0000-00-00 00:00:00',
			  `currency_code` CHAR(4) DEFAULT NULL,
			  `subscription` INT(1) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY (`tnx_id`)
			) ENGINE = InnoDB {$charset_collate};",

		"CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}elpay_discounts` (
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
			`code` VARCHAR(10) NOT NULL, /* discount code */
			`limit` INT(11) DEFAULT 1, /* how much time will be used  */
			`type` VARCHAR(40) NOT NULL,	/* type (plugin|service|site) example plugin can be other socialrabbit*/
			`price` DECIMAL(12,2) DEFAULT 0.00, /* discount for total amount  */
			`percent` INT(3) DEFAULT 0, /* discount for percent of total amount  */
			`moreprice` DECIMAL(12,2) DEFAULT 0.00, /* discount if total amount more then this  */
			`date_start` DATETIME NOT NULL,
			`date_end` DATETIME DEFAULT '0000-00-00 00:00:00',
			`status` INT(1) DEFAULT 0,
			`used` INT(11) DEFAULT 0,
			PRIMARY KEY (`id`),
			KEY (`code`),
			KEY (`type`)
		) ENGINE = InnoDB  {$charset_collate};",
	);

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	foreach($sql as $key)
		dbDelta($key);

	//elp_upgrade_sql();

	update_site_option( 'elp-version', ELP_VERSION  );
}

function elp_upgrade_sql(){

	//global $wpdb;

	//maybe_add_column($wpdb->prefix . 'paypal_transaction', 'license', "ALTER TABLE `{$wpdb->prefix}paypal_transaction` ADD `license` varchar(255) NOT NULL;");
	//maybe_add_column($wpdb->prefix . 'paypal_transaction', 'review', "ALTER TABLE `{$wpdb->prefix}paypal_transaction` ADD `review` int(1) NOT NULL;");
}

function elp_installed(){

	if ( !current_user_can('install_plugins') )
		return;

	if ( get_site_option( 'elp-version' ) < ELP_VERSION )
		elp_install();

	elp_set_pay_settings();
}
add_action( 'admin_menu', 'elp_installed' );
	
function elp_activate(){

	elp_installed();

	wp_schedule_event( time(), 'daily', 'elp_cron_currency');

	do_action( 'elp_activate' );
}

function elp_deactivate(){

	do_action( 'elp_deactivate' );
}

function elp_set_pay_settings(){

	$default = elp_default_settings();

	if ( !get_site_option('elp-epay-settings') )
		update_site_option( 'elp-epay-settings', $default );

	if ( !get_site_option('elp-mail-setting') )
		update_site_option( 'elp-mail-setting', array('api' => '', 'logo' => '', 'from' => '', 'name' => '') );
}