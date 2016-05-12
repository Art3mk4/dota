<?php
/**
 * Created by PhpStorm.
 * User: Violeta
 * Date: 09.03.2016
 * Time: 14:10
 */

if( !function_exists('pr') ) {

	function pr( $any ) {

		print_r( "<pre>" );
		print_r( $any );
		print_r( "</pre>" );
	}
}

function elp_get_rand_string($length = 9) {
	$string = '';
	$chrs = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	for($i=0; $i<$length; $i++) {
		$loc = mt_rand(0, strlen($chrs)-1);
		$string .= $chrs[$loc];
	}
	return $string;
}

function elp_decimal($value){

	$value = preg_replace('/[^\d.]/','',$value);

	return $value > 0 ? $value : 0;
}

function elp_init_session(){
	if( !session_id() )
		session_start();
}
add_action('init', 'elp_init_session');

function elp_set_database(){

	global $wpdb;

	$wpdb->transaction 		= $wpdb->prefix . 'elpay_transaction';
	$wpdb->transaction_log  = $wpdb->prefix . 'elpay_transaction_log';
	$wpdb->discounts 		= $wpdb->prefix . 'elpay_discounts';
}
add_action('init', 'elp_set_database');

function elp_access_types(){
	return array(
		'socialrabbit' => 'Social Rabbit plugin',
		'all'          => 'For All'
	);
}

function elp_check_discounts($type = '', $code = '', $price = ''){

	$types = elp_access_types();

	$type = trim($type);
	$code = trim($code);

	if($type == '' || !isset($types[$type]) || empty($code) )
		return array('error' => 'Discount code not found');

	global $wpdb;

	$row = $wpdb->get_row(
		$wpdb->prepare("SELECT * FROM {$wpdb->discounts} WHERE code = %s", $code)
	);

    if( empty($row) )
        return array('error' => 'Discount code not found');

	if( $row->type != 'all' && $row->type != $type )
		return array('error' => 'Discount code not found');

	if( $row->limit > 0 && $row->used >= $row->limit )
		return array('error' => 'Limit exceeded');

	$now = strtotime('now');
	$date_start = strtotime($row->date_start);
	$date_end = $row->date_end != '0000-00-00 00:00:00' ? strtotime($row->date_end) : false;

	if( $date_start > strtotime('now') || ($date_end && $date_end < $now) )
		return array('error' => 'Code has expired');

    if( $row->moreprice > 0 ){

        if( $price < $row->moreprice ){
            return array('error' => 'The total amount is not sufficient to obtain discounts');
        }
        else{

            if( $row->price > 0 ){
                $total = $price - $row->price;
                return array(
                    'success' => 'Your discount is $' . $row->price . '. Total amount is $' . elp_floatvalue($total),
                    'amount' => $total
                );
            }
            elseif( $row->percent > 0 ){
                $discount = round( ($price/100)*$row->percent, 2 );
                $total = $price - $discount;
                return array(
                    'success' => 'Your discount is ' . $row->percent . '%. Total amount is $' . elp_floatvalue($total),
                    'amount' => $total
                );
            }
            else
                return array('error' => 'Discount code not found');
        }
    }
    else{
        if( $row->price > 0 ){
            $total = $price - $row->price;
            return array(
                'success' => 'Your discount is $' . $row->price . '. Total amount is $' . elp_floatvalue($total),
                'amount' => $total
            );
        }
        elseif( $row->percent > 0 ){
            $discount = round( ($price/100)*$row->percent, 2 );
            $total = $price - $discount;
            return array(
                'success' => 'Your discount is ' . $row->percent . '%. Total amount is $' . elp_floatvalue($total),
                'amount' => $total
            );
        }
        else
            return array('error' => 'Discount code not found');
    }
}

function elp_update_discount($code){

    global $wpdb;

    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->discounts} WHERE code = %s", $code)
    );

    if( empty($row) )
        return false;

    $used = $row->used + 1;
    $wpdb->update(
        $wpdb->discounts,
        array('used' => $used),
        array('id' => $row->id),
        array('%d'),
        array('%d')
    );

    return true;
}

/**
 * Parse any str to float
 * @param $value
 * @return string
 */
function elp_floatvalue( $value ){

	$value = preg_replace("/[^0-9,.]/", "", $value);
	$value = str_replace(',', '.', $value);
	return number_format( floatval($value), 2, '.', '' );
}

function elp_default_settings(){

	return array(
		'paypal' => array(
			'name'   => 'PayPal',
			'image'  => plugins_url( '/../img/paypal.jpg', __FILE__ ),
			'fields' => array(
				'username' => array(
					'default'     => '',
					'name'        => 'Username',
					'description' => 'Enter username in your paypal',
					'type'        => 'text'
				),
				'password' => array(
					'default' 	  => '',
					'name' 		  => 'Password',
					'description' => 'Enter password in your paypal',
					'type' 		  => 'text'
				),
				'signature' => array(
					'default' 	  => '',
					'name' 	      => 'Signature',
					'description' => 'Enter signature in your paypal',
					'type' 		  => 'text'
				),
				'testMode' => array(
					'default' 	  => 0,
					'name' 	  	  => 'Demo',
					'type'    	  => 'select',
					'values'   	  => array(
						0 => 'No',
						1 => 'Yes',
					),
					'description' => 'Demo payments',
				),
				'solutionType' => array(
					'default' 	  => 'Sole',
					'name' 		  => 'Solution Type',
					'description' => 'Enter solutionType in your paypal',
					'type' 		  => 'text'
				),
				'landingPage' => array(
					'default' 	  => 'Billing',
					'name' 		  => 'Landing Page',
					'description' => 'Enter landingPage in your paypal',
					'type' 		  => 'text'
				),
				'brandName' => array(
					'default' 	  => '',
					'name' 		  => 'Brand Name',
					'description' => 'Enter brandName in your paypal',
					'type' 		  => 'text'
				),
				'headerImageUrl' => array(
					'default' 	  => '',
					'name' 		  => 'Header Image Url',
					'description' => 'Enter headerImageUrl in your paypal',
					'type' 		  => 'text'
				),
				'logoImageUrl' => array(
					'default' 	  => '',
					'name' 		  => 'Log Image Url',
					'description' => 'Enter logoImageUrl in your paypal',
					'type' 		  => 'text'
				),
				'borderColor' => array(
					'default' 	  => '',
					'name' 		  => 'Border Color',
					'description' => 'Enter borderColor in your paypal',
					'type' 		  => 'text'
				),
			),
		)
	);
}

function elp_pay_settings( $type ){

	$settings = get_site_option('elp-epay-settings');

	$default = elp_default_settings();

	if( isset($settings[$type]) && isset($default[$type]) ){

		foreach( $default[$type]['fields'] as $key => $val ){
			$default[$type]['fields'][$key] =
				isset($settings[$type]['fields'][$key]) ?
					$settings[$type]['fields'][$key] : $val;
		}

		return $default[$type];
	}

	return false;
}

function elp_pay_save_setting( $type, $data ){

	$default = elp_default_settings();

	if( isset($default[$type]) ){

		foreach( $default[$type]['fields'] as $key => $val ){
			$default[$type]['fields'][$key]['default'] =
				isset($data[$key]) ?
					trim($data[$key]) : $val['default'];
		}

		update_site_option('elp-epay-settings', $default);
	}

	return false;
}

function elp_current_pay_settings( $type ){

	$settings = elp_pay_settings( $type );

	if( !$settings ) return false;

	$args = array();

	foreach($settings['fields'] as $key => $val){
		$args[$key] = $val['default'];
	}

	return $args;
}

function elp_send_mail_to($email_to, $email_from, $name_from, $subject, $html){

	require_once( ELP_PATH . 'sdk/Mandrill/mail.php');

	$args = get_site_option('elp-mail-setting');

	$api  = isset($args['api']) ? $args['api'] : '';
	$logo = isset($args['logo']) ? $args['logo'] : '';
	$from = is_email($email_from) ? $email_from : 'mail@site.com';

	ob_start();

	?>
	<table border="0" cellspacing="0" cellpadding="0" width="600px" align="center" style="margin:auto">
		<tbody>
		<tr>
			<td align="center">
				<a href="<?php echo home_url('/?email') ?>" target="_blank"><img border="0" src="<?php echo $logo ?>"></a>
			</td>
		</tr>
		</tbody>
	</table>
	<table align="center" border="0" width="600px" cellpadding="0" cellspacing="0" style="margin:auto">
		<tbody>
		<tr>
			<td style="padding:20px 23px">
				<?php echo $html ?>
			</td>
		</tr>
		</tbody>
	</table>
	<?php

	$content = ob_get_contents();
	ob_end_clean();

	elp_mandrill( $api, $email_to, $subject, $content, $from, $name_from );
}

function elp_send_mail($email, $subject, $html){

	require_once( ELP_PATH . 'sdk/Mandrill/mail.php');

	$args = get_site_option('elp-mail-setting');

	$api  = isset($args['api']) ? $args['api'] : '';
	$logo = isset($args['logo']) ? $args['logo'] : '';
	$from = isset($args['from']) ? $args['from'] : 'mail@site.com';
	$name = isset($args['name']) ? $args['name'] : 'name';

	ob_start();

	?>
	<table border="0" cellspacing="0" cellpadding="0" width="600px" align="center" style="margin:auto">
		<tbody>
		<tr>
			<td align="center">
				<a href="<?php echo home_url('/?email') ?>" target="_blank"><img border="0" src="<?php echo $logo ?>"></a>
			</td>
		</tr>
		</tbody>
	</table>
	<table align="center" border="0" width="600px" cellpadding="0" cellspacing="0" style="margin:auto">
		<tbody>
		<tr>
			<td style="padding:20px 23px">
				<?php echo $html ?>
			</td>
		</tr>
		</tbody>
	</table>
	<?php

	$content = ob_get_contents();
	ob_end_clean();

	elp_mandrill( $api, $email, $subject, $content, $from, $name );
}
add_action('elp_send_mail', 'elp_send_mail', 10, 3);




/**
 * Converter currency gy google
 * @param int $amount
 * @param string $from
 *
 * @param $to
 * @return bool|float|mixed
 */
function elp_convert_currency( $amount = 1, $from = 'USD', $to ) {

	$url  = "https://www.google.com/finance/converter?a=" . $amount . "&from=" . $from . "&to=" . $to;

	$ch = curl_init($url); // Инициализируем сессию cURL
	// Устанавливаем параметры cURL
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Возвращает веб-страницу
	curl_setopt($ch, CURLOPT_TIMEOUT, 20); // Таймаут ответа
	$data = curl_exec($ch); // Выполняем запрос
	curl_close($ch); // Завершаем сессию cUrl

	preg_match("/<span class=bld>(.*)<\/span>/",$data, $converted);

	$converted = preg_replace("/[^0-9.]/", "", $converted[1]);
	$converted = round($converted, 2);

	return $converted;
}
