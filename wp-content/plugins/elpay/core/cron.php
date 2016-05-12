<?php
/**
 * User: Maxim Doronin
 * Date: 06.04.2016
 * Time: 14:10
 */


/**
 * Auto update real currency
 */
add_action( 'elp_cron_currency', 'elp_do_cron_currency' );
function elp_do_cron_currency() {

	$currency = elp_convert_currency( 1, 'AED', 'USD' );

	if( count($currency) > 0 ) {
		return;
	}

  update_site_option( 'yp-currency', $currency );
}
