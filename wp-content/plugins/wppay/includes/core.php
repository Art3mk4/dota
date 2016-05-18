<?php
/**
 * User: Vitaly Kukin
 * Date: 12.05.2016
 * Time: 10:21
 */

function wppay_autoload( $className ){
	$className = ltrim($className, '\\');
	$fileName  = '';

	if ($lastNsPos = strrpos($className, '\\')) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);

		$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR . 'class.';
	}

	$fileName .= $className . '.php';

	$file = WPPAY_PATH . 'libs/'. $fileName;

	if ( file_exists( $file ) ) {
		require( $file );
	}
}
spl_autoload_register('wppay_autoload');

if( !function_exists('pr') ) {

	function pr( $any ) {

		print_r( "<pre>" );
		print_r( $any );
		print_r( "</pre>" );
	}
}

function wppay_init_session(){
	if( !session_id() )
		session_start();
}
add_action('init', 'wppay_init_session');

function wppay_set_database(){

	global $wpdb;

	$wpdb->transaction = $wpdb->prefix . 'wppay_transaction';
}
add_action('init', 'wppay_set_database');

function wppay_get_rand_string($length = 9) {
	$string = '';
	$chrs = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	for($i=0; $i<$length; $i++) {
		$loc = mt_rand(0, strlen($chrs)-1);
		$string .= $chrs[$loc];
	}
	return $string;
}

function wppay_decimal($value){

	$value = preg_replace('/[^\d.]/','',$value);

	return $value > 0 ? $value : 0;
}

/**
 * Parse any str to float
 * @param $value
 * @return string
 */
function wppay_floatvalue( $value ){

	$value = preg_replace("/[^0-9,.]/", "", $value);
	$value = str_replace(',', '.', $value);
	return number_format( floatval($value), 2, '.', '' );
}

function wppay_default_settings(){

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
				'returnUrl' => array(
					'default'     => '',
					'name' 	      => 'Return Url',
					'description' => 'Enter return Url',
					'type'	      => 'text'
				),
				'cancelUrl' => array(
					'default'     => '',
					'name'	      => 'cancelUrl',
					'description' => 'Enter cancel Url',
					'type'        => 'text'
				),
				'notifyUrl' => array(
					'default'     => '',
					'name'        => 'notifyUrl',
					'description' => 'Enter notify Url',
					'type'        => 'text'
				)
			),
		)
	);
}

function wppay_pay_settings($type)
{
    $settings = get_site_option('wppay-settings');
    $default = wppay_default_settings();

    if (isset($settings[$type]) && isset($default[$type])) {
	foreach ($default[$type]['fields'] as $key => $val) {
            $default[$type]['fields'][$key] =
		isset($settings[$type]['fields'][$key]) ?
                    $settings[$type]['fields'][$key] : $val;
	}
        return $default[$type];
    }

    return false;
}

function wppay_pay_save_setting($type, $data)
{
    $default = wppay_default_settings();
    if (isset($default[$type])) {
        foreach ($default[$type]['fields'] as $key => $val) {
            $default[$type]['fields'][$key]['default'] =
                isset($data[$key]) ?
                    trim($data[$key]) : $val['default'];
        }
        update_site_option('wppay-settings', $default);
    }

    return false;
}

function wppay_current_pay_settings($type)
{
    $settings = wppay_pay_settings( $type );
    if (!$settings) return false;

    $args = array();

    foreach ($settings['fields'] as $key => $val) {
        $args[$key] = $val['default'];
    }

    return $args;
}

function wppay_send_mail($email, $subject, $html, $from = '', $name = '' )
{
    $args = get_site_option('wppay_mailgun_setting');

    $domain = isset($args['domain']) ? $args['domain'] : '';
    $api  = isset($args['api']) ? $args['api'] : '';
    $logo = isset($args['logo']) ? $args['logo'] : '';

    if ($from == '') {
        $from = isset($args['from']) ? $args['from'] : 'mail@site.com';
    }
    if($name == '') {
        $name = isset($args['name']) ? $args['name'] : 'name';
    }
    ob_start();?>
	
    <table border="0" cellspacing="0" cellpadding="0" width="100%" height="72px" bgcolor="#000000" align="center">
        <tbody>
            <tr>
                <td align="center">
                    <a href="<?php echo home_url('/?email') ?>" target="_blank"><img border="0" src="<?php echo $logo ?>"></a>
                </td>
            </tr>
        </tbody>
    </table>
    <table style="background:#fff" align="center" border="0" width="100%" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td style="border:1px solid #ddd;padding:20px 23px">
                    <?php echo $html ?>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
    $content = ob_get_contents();
    ob_end_clean();

    $mail = \SendMail\SendMail::i();
    $mail->message->to         = $email;
    $mail->message->from_email = $from;
    $mail->message->from_name  = $name;
    $mail->message->subject    = $subject;
    $mail->message->html       = $content;

    $mail->send();
}
add_action('wppay_send_mail', 'wppay_send_mail', 10, 3);