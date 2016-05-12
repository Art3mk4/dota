<?php
/**
 * User: Vitaly Kukin
 * Date: 10.03.2016
 * Time: 11:07
 */

function elp_catch_request(){

	error_log(print_r($_REQUEST, true));
}
//add_action('wp', 'elp_catch_request');

function elp_delete_tnx(){

	if( current_user_can('level_9') ){

		global $wpdb;

		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

		$var = $wpdb->get_var( $wpdb->prepare("SELECT id FROM {$wpdb->transaction} WHERE id = %d AND status = 'create'", $id) );
		if( !empty($var) ){
			$wpdb->delete($wpdb->transaction, array('id' => $id), array('%d'));
			echo 'success';
		}
	}
	die();
}
add_action('wp_ajax_elp_delete_tnx', 'elp_delete_tnx');

function elp_delete_discount(){

	if( current_user_can('level_9') ){

		global $wpdb;

		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

		$var = $wpdb->get_var( $wpdb->prepare("SELECT id FROM {$wpdb->discounts} WHERE id = %d", $id) );
		if( !empty($var) ){
			$wpdb->delete($wpdb->discounts, array('id' => $id), array('%d'));
			echo 'success';
		}
	}
	die();
}
add_action('wp_ajax_elp_delete_discount', 'elp_delete_discount');

function elp_ajax_check_discounts(){

	if( isset($_POST['type']) && isset($_POST['code']) ){

		//@todo брать ценник от конкретного товара
		$response = elp_check_discounts($_POST['type'], $_POST['code'], $_POST['price']);

		echo json_encode($response);
	}
	die();
}
add_action('wp_ajax_elp_check_discounts', 'elp_ajax_check_discounts');

function elp_redirect_to_payment(){

	if( !isset($_POST['elp_pay']) ) return false;

	$email     = isset($_POST['email']) && is_email($_POST['email']) ? trim($_POST['email']) : false;
	$type      = isset($_POST['pay_type']) ? $_POST['pay_type'] : 'paypal';
	$amount    = isset($_POST['amount']) ? elp_floatvalue($_POST['amount']) : 0.00;
	$currency  = isset($_POST['currency']) ? trim($_POST['currency']) : 'USD';
	$name      = isset($_POST['name']) ? trim($_POST['name']) : 'Sample Name';
	$desc      = isset($_POST['desc']) ? trim($_POST['desc']) : 'Sample Description';
	$period    = isset($_POST['period']) ? trim($_POST['period']) : 'Month';
	$frequency = isset($_POST['frequency']) ? intval($_POST['frequency']) : 1;
	$discount  = isset($_POST['discount']) ? trim($_POST['discount']) : 'all';
	$code      = isset($_POST['code']) ? trim($_POST['code']) : '';

	$response  = elp_check_discounts($discount, $code, $amount);

	$args = elp_current_pay_settings( $type );

	if( !$args || !$email ) return false;

	$obj = new paypal();
	$obj->setUsername($args['username'])
		->setPassword($args['password'])
		->setSignature($args['signature'])
		->setLogo($args['logoImageUrl'])
		->setBrand($args['brandName'])
		->setBorderColor($args['borderColor'])
		->setPaymentType('Sale')
		->setType($type)
		->setEmail($email)
		->setPeriod($period)
		->setFrequency($frequency)
		->setAmount($amount)
		->setFullamount($amount)
		->setCurrency($currency)
		->setName($name)
		->setDescription($desc);

	if( isset($response['amount']) ){
		$obj->setFullamount($response['amount']);
		$obj->setCoupon($code);
		$obj->setDiscount($response['success']);
	}

	$obj->SetExpressCheckout();
}
add_action('elp_redirect_to_payment', 'elp_redirect_to_payment');

function elp_redirect_to_payment_single(){

	if( !isset($_POST['elp_pay_single']) ) return false;

	$email     = isset($_POST['email']) && is_email($_POST['email']) ? trim($_POST['email']) : false;
	$type      = isset($_POST['pay_type']) ? $_POST['pay_type'] : 'paypal';
	$amount    = isset($_POST['amount']) ? elp_floatvalue($_POST['amount']) : 0.00;
	$currency  = isset($_POST['currency']) ? trim($_POST['currency']) : 'USD';
	$name      = isset($_POST['name']) ? trim($_POST['name']) : 'Sample Name';
	$desc      = isset($_POST['desc']) ? trim($_POST['desc']) : 'Sample Description';
	$discount  = isset($_POST['discount']) ? trim($_POST['discount']) : 'all'; //delete
	$code      = isset($_POST['code']) ? trim($_POST['code']) : ''; //delete
	$additional = isset($_POST['additional']) ? serialize($_POST['additional']) : '';

	$response  = elp_check_discounts($discount, $code, $amount); //delete

	if( !$email ) return false;

	switch ($type) {
		case 'paypal':
		
			$args = elp_current_pay_settings( $type );
			
			if( !$args ) return false;

			$obj = new paypal();
			$obj->setUsername($args['username'])
				->setPassword($args['password'])
				->setSignature($args['signature'])
				->setLogo($args['logoImageUrl'])
				->setBrand($args['brandName'])
				->setBorderColor($args['borderColor'])
				->setPaymentType('Sale')
				->setType($type)
				->setEmail($email)
				->setAmount($amount)
				->setFullamount($amount)
				->setCurrency($currency)
				->setName($name)
				->setDescription($desc)
				->setResponse($additional)
				->setSubscription(0);

			//delete
			if( isset($response['amount']) ){
				$obj->setFullamount($response['amount']);
				$obj->setCoupon($code);
				$obj->setDiscount($response['success']);
			}

			$obj->SetExpressCheckout();
			break;

		case 'receipt':
			global $wpdb;
			$token = 'RE-'.elp_get_rand_string(8);

			$_SESSION['TOKEN'] = $token;

			$wpdb->insert(
				$wpdb->transaction,
				array(
					'token'         => $token,
					'status'        => 'receipt',
					'type'          => 'receipt',
					'email'         => $email,
					'name'          => $name,
					'description'   => $desc,
					// 'period'        => $this->period,
					// 'frequency'     => $this->frequency,
					'amount'        => $amount,
					'fullamount'    => $amount, //@TODO: fullamount запилить после того как будут введены купоны на скидку
					// 'coupon'        => $this->coupon,
					// 'discount'      => $this->discount,
					'currency_code' => $currency,
					'date'          => gmdate('Y-m-d H:i:s'),
					'subscription'  => 0,
					'response'      => $additional
				)
			);
			
			wp_redirect( home_url( '/thankyou/?fail=no&token=' . $token ) );
			exit();
			
			break;
		
		default:
			# code...
			break;
	}

   
}
add_action('elp_redirect_to_payment_single', 'elp_redirect_to_payment_single');

function elp_get_request_from_redirect(){

	if( isset($_GET['fail']) && $_GET['fail'] == 'no' &&
		isset($_SESSION['TOKEN']) && isset($_GET['token']) &&
		!empty($_GET['token']) && $_GET['token'] == $_SESSION['TOKEN'] &&
		isset($_GET['PayerID']) && !empty($_GET['PayerID'])
	){

		$_SESSION['TOKEN'] = '';

		global $wpdb;

		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->transaction} WHERE token = %s LIMIT 1",
				$_GET['token']
			)
		);

		if( empty($row) ){
			$_POST['error_msg'] = __('Transaction not found', 'elp');
			return false;
		}

		$args = elp_current_pay_settings( $row->type );

		if( !$args ){
			$_POST['error_msg'] = __('Payment Settings is Wrong', 'elp');
			return false;
		}

		$obj = new paypal();
		$obj->setUsername($args['username'])
			->setPassword($args['password'])
			->setSignature($args['signature'])
			->setName($row->name)
			->setDescription($row->description)
			->setAmount($row->amount)
			->setFullamount($row->fullamount)
			->setCurrency($row->currency_code);

		$response = $obj->getCheckoutDetails( $_GET['token'] );

		if( $row->subscription == 1 ) {
			$obj->setPeriod( $row->period )
				->setFrequency( $row->frequency );
			//$result = $obj->getCheckoutDetails($_GET['token']);
			$result = $obj->createRecurring( $_GET['token'], $_GET['PayerID'] );
		}
		else{
			if( $response['TOKEN'] ){
				$_GET['PayerID'] = $response['PAYERID'];

				$result = $obj->doCheckoutPayment( $_GET['token'], $_GET['PayerID'] );
			}
			else{
				$_POST['error_msg'] = __('The payment does not setup', 'elp');
			}
		}

		if( isset($result['ACK']) && $result['ACK'] == 'Success' ){

			elp_update_discount($row->coupon); //delete

			$wpdb->update(
				$wpdb->transaction,
				array(
					'status'   => 'paid',
					'payer_id' => $_GET['PayerID']
				),
				array('id' => $row->id),
				array('%s','%s'),
				array('%d')
			);

			$wpdb->insert(
				$wpdb->transaction_log,
				array(
					'tnx_id'        => $row->id,
					'amount'        => $row->amount,
					'fullamount'    => $row->fullamount,
					'date'          => $row->date,
					'currency_code' => $row->currency_code,
					'subscription'  => $row->subscription
				),
				array('%d', '%f', '%f', '%s', '%s', '%d')
			);

			if(isset($response['TOKEN'])){

				$name = $response['FIRSTNAME'] . ' ' . $response['LASTNAME'];

				$wpdb->update(
					$wpdb->transaction,
					array(
						'fullname'   => $name,
						'country'    => $response['COUNTRYCODE'],
						'details'    => serialize($response)
					),
					array('id' => $row->id),
					array('%s','%s'),
					array('%d')
				);
			}

			do_action('elp_paid_success', $row->id, $row->email, 'regular');

			$_POST['success_msg'] = __('The Recurring payment is successfully installed!', 'elp');
		}
		else{
			$_POST['error_msg'] = __('Recurring payment does not setup', 'elp');
		}
	}
	elseif ( isset($_GET['fail']) && $_GET['fail'] == 'no' && isset($_SESSION['TOKEN']) && isset($_GET['token']) ) {
		global $wpdb;

		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->transaction} WHERE token = %s LIMIT 1",
				$_GET['token']
			)
		);

		$_SESSION['TOKEN'] = '';

		do_action('elp_paid_success', $row->id, $row->email, 'regular');
	}
}
add_action('wp', 'elp_get_request_from_redirect');

function elp_get_tnx_details($token, $type){

	$args = elp_current_pay_settings( $type );

	if( !$args ) return false;

	$obj = new paypal();
	$obj->setUsername($args['username'])
		->setPassword($args['password'])
		->setSignature($args['signature']);

	$response = $obj->getCheckoutDetails( $token );

	pr($response);
}
add_action('elp_get_tnx_details', 'elp_get_tnx_details', 10, 2);