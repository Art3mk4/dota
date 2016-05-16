<?php
/**
 * User: Vitaly Kukin
 * Date: 12.05.2016
 * Time: 10:07
 */

function wppay_delete_tnx(){

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
add_action('wp_ajax_wppay_delete_tnx', 'wppay_delete_tnx');

function wppay_redirect_to_payment(){

    if( !isset($_POST['wppay_pay']) ) return false;

    $email     = isset($_POST['email']) && is_email($_POST['email']) ? trim($_POST['email']) : false;
    $type      = isset($_POST['pay_type']) ? $_POST['pay_type'] : 'paypal';
    $amount    = isset($_POST['amount']) ? wppay_floatvalue($_POST['amount']) : 0.00;
    $currency  = isset($_POST['currency']) ? trim($_POST['currency']) : 'USD';
    $name      = isset($_POST['name']) ? trim($_POST['name']) : 'Sample Name';
    $desc      = isset($_POST['desc']) ? trim($_POST['desc']) : 'Sample Description';
    $period    = isset($_POST['period']) ? trim($_POST['period']) : 'Month';
    $frequency = isset($_POST['frequency']) ? intval($_POST['frequency']) : 1;

    $args = wppay_current_pay_settings( $type );

    if( !$args || !$email ) return false;

    $obj = new \Paypal\Paypal();
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

    $obj->SetExpressCheckout();
}
add_action('wppay_redirect_to_payment', 'wppay_redirect_to_payment');

function wppay_redirect_to_payment_single()
{
    if (!isset($_POST['wppay_pay_single'])) {
        return false;
    }

    $email    = isset($_POST['email']) && is_email($_POST['email']) ? trim($_POST['email']) : false;
    $type     = isset($_POST['pay_type']) ? $_POST['pay_type'] : 'paypal';
    $amount   = isset($_POST['amount']) ? wppay_floatvalue($_POST['amount']) : 0.00;
    $currency = isset($_POST['currency']) ? trim($_POST['currency']) : 'USD';
    $name     = isset($_POST['name']) ? trim($_POST['name']) : 'Sample Name';
    $desc     = isset($_POST['desc']) ? trim($_POST['desc']) : 'Sample Description';

    $args = wppay_current_pay_settings($type);

    if (!$args || !$email ) {
        return false;
    }

    $obj = new \Paypal\Paypal();
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
        ->setSubscription(0);
    
    $obj->SetExpressCheckout();
}
add_action('wppay_redirect_to_payment_single', 'wppay_redirect_to_payment_single');

function wppay_get_request_from_redirect(){

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
            $_POST['error_msg'] = __('Transaction not found', 'wppay');
            return false;
        }

        $args = wppay_current_pay_settings( $row->type );

        if( !$args ){
            $_POST['error_msg'] = __('Payment Settings is Wrong', 'wppay');
            return false;
        }

        $obj = new \Paypal\Paypal();
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
                $_POST['error_msg'] = __('The payment does not setup', 'wppay');
            }
        }

        if( isset($result['ACK']) && $result['ACK'] == 'Success' ){

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

            do_action('wppay_paid_success', $row->id, $row->email, 'regular');

            $_POST['success_msg'] = __('The Recurring payment is successfully installed!', 'wppay');
        }
        else{
            $_POST['error_msg'] = __('Recurring payment does not setup', 'wppay');
        }
    }
}
add_action('wp', 'wppay_get_request_from_redirect');

function wppay_get_tnx_details($token, $type){

    $args = wppay_current_pay_settings( $type );

    if( !$args ) return false;

    $obj = new \Paypal\Paypal();
    $obj->setUsername($args['username'])
        ->setPassword($args['password'])
        ->setSignature($args['signature']);

    $response = $obj->getCheckoutDetails( $token );

    pr($response);
}
add_action('wppay_get_tnx_details', 'wppay_get_tnx_details', 10, 2);

function wppay_download_file()
{
    if (!isset($_GET['download_file']) || $_GET['download_file'] != 'yes') {
        return ;
    }

    $file = WPPAY_PATH . 'files/guide.pdf';
    $fileBasename = basename($file);

    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileBasename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}
add_action('wp', 'wppay_download_file');

function wppay_plugin_handler()
{
    if (!isset($_POST['wppay_do_pay'])) {
        return false;
    }

    $quide = get_site_option('wppay_guide_setting');
    $amount = 0.01;
    if (isset($guide['price'])) {
        $amount = $guide['price']; // получение цены за товар
    }
    $name = 'guide'; // наименование товара или заказа
    $desc = 'esport_guide'; // описание товара
    $email = isset($_POST['email']) && is_email($_POST['email']) ? trim($_POST['email']) : false;

    if (!$email) {
        $_POST['error_msg'] = 'The Email was filled wrong!';
        return false;
    }

    $_POST = array(
        'email'            => $email,
        'type'             => 'paypal',
        'currency'         => 'USD',
        'amount'           => $amount,
        'name'             => $name,
        'desc'             => $desc,
        'wppay_pay_single' => ''
    );

    do_action('wppay_redirect_to_payment_single');
}
add_action('wp', 'wppay_plugin_handler');