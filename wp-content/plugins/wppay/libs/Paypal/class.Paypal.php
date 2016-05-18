<?php

/**
 * User: Vitaly Kukin
 * Date: 12.05.2016
 * Time: 9:06
 */
namespace Paypal;

class Paypal{

    private $endpoint = 'https://api-3t.paypal.com/nvp';

    private $email;
    private $type;

    private $currency  = 'USD';
    private $description;
    private $name;
    private $period;
    private $frequency;
    private $amount;
    private $fullamount;
    private $paymentType;
    private $subscription = 1;

    private $mode = '';
    private $username;
    private $password;
    private $signature;
    private $returnURL;
    private $cancelURL;
    private $notifyURL;

    private $logo;
    private $brand;
    private $bordercolor;

    private $coupon;
    private $discount;

    public function __construct(){
        $this->setURL();
    }

    public function setLogo( $logo ){
        $this->logo = $logo ? $logo : false;
        return $this;
    }

    public function setBrand( $brand ){
        $this->brand = $brand ? $brand : false;
        return $this;
    }

    public function setBorderColor( $bordercolor ){
        $this->bordercolor = $bordercolor ? $bordercolor : false;
        return $this;
    }

    /**
     * Set Email
     * @param $email
     * @return $this
     */
    public function setEmail( $email ){
        $this->email = is_email($email) ? $email : false;
        return $this;
    }

    /**
     * Set Email
     * @param $type
     * @return $this
     */
    public function setType( $type ){
        $this->type = $type != '' ? $type : 'paypal';
        return $this;
    }

    /**
     * Set Mode
     * @param $mode
     * @return $this
     */
    public function setMode( $mode ){
        $this->mode = $mode == '' ? '' : 'sandbox';
        return $this;
    }

    /**
     * Set signature
     * @param $sign
     * @return $this
     */
    public function setSignature( $sign ){
        $this->signature = $sign;
        return $this;
    }

    /**
     * Set Password
     * @param $password
     * @return $this
     */
    public function setPassword( $password ){
        $this->password = $password;
        return $this;
    }

    /**
     * Set Username
     * @param $username
     * @return $this
     */
    public function setUsername( $username ){
        $this->username = $username;
        return $this;
    }

    /**
     * Set Currency
     * @param string $type
     * @return $this
     */
    public function setPaymentType( $type = 'Sale' ){

        $foo = array('Sale', 'Authorization', 'Order');

        $this->paymentType = in_array($type, $foo) ? $type : 'Sale';
        return $this;
    }

    /**
     * Set Marker to Recurring payment or not
     * @param int $type
     * @return $this
     */
    public function setSubscription( $type = 1 ){

        $this->subscription = $type == 1 ? 1 : 0;
        return $this;
    }

    /**
     * Set Currency
     * @param float $amount
     * @return $this
     */
    public function setAmount( $amount = 0.00 ){
        $this->amount = wppay_floatvalue($amount);
        return $this;
    }

    /**
     * Set Currency
     * @param float $fullamount
     * @return $this
     */
    public function setFullamount( $fullamount = 0.00 ){
        $this->fullamount = wppay_floatvalue($fullamount);
        return $this;
    }

    /**
     * Set Currency
     * @param string $currency
     * @return $this
     */
    public function setCurrency( $currency = 'USD' ){
        $this->currency = $currency;
        return $this;
    }

    /**
     * Set Name of product
     * @param string $str
     * @return $this
     */
    public function setName( $str = '' ){
        $this->name = $str;
        return $this;
    }

    /**
     * Set Description of billing agreement
     * @param string $str
     * @return $this
     */
    public function setDescription( $str = '' ){
        $this->description = $str;
        return $this;
    }

    public function setPeriod( $period ){

        $args = array('Day', 'Week', 'SemiMonth', 'Month', 'Year');

        $this->period = in_array($period, $args) ? $period : 'Month';
        return $this;
    }

    public function setFrequency( $count ){

        $this->frequency = $count <= 12 && $count >= 1 ? $count : 1;
        return $this;
    }

    public function setCoupon( $coupon ){
        $this->coupon = $coupon;
    }

    public function setDiscount( $discount ){
        $this->discount = $discount;
    }

    /**
     * Set returned URL
     * @param string $return
     * @param string $cancel
     */
    public function setURL( $return = '', $cancel = '', $notify = '' ){
	$args = wppay_current_pay_settings('paypal');
        $this->returnURL = (isset($args['returnUrl'])) ? home_url($args['returnUrl']) : $return;
        $this->cancelURL = (isset($args['cancelUrl'])) ? home_url($args['cancelUrl']) : $cancel;
        $this->notifyURL = (isset($args['notifyUrl'])) ? home_url($args['notifyUrl']) : $notify;
    }

    /**
     * Set Express Checkout and redirect customer to PayPal gateway
     */
    function SetExpressCheckout(){

        //@todo запилить проверку если есть скидка в 100%, то переадресовывать сразу на страницу спасибо

        $result = $this->request(
            array(
                'METHOD'                           => 'SetExpressCheckout',
                'VERSION'                          => 108,
                'PWD'                              => $this->password,
                'USER'                             => $this->username,
                'SIGNATURE'                        => $this->signature,
                'CARTBORDERCOLOR'                  => $this->bordercolor,
                'LOGOIMG'                          => $this->logo,
                'BRANDNAME'                        => $this->brand,
                'PAYMENTREQUEST_0_AMT'             => $this->fullamount,
                'PAYMENTREQUEST_0_PAYMENTACTION'   => $this->paymentType,
                'PAYMENTREQUEST_0_CURRENCYCODE'    => $this->currency,
                'AMT'                              => $this->fullamount,
                //'INITAMT'                          => $this->amount,
                'L_PAYMENTREQUEST_0_NAME0'         => $this->name,
                'L_PAYMENTREQUEST_0_DESC0'         => $this->description,
                'L_PAYMENTREQUEST_0_QTY0'          => 1,
                'L_PAYMENTREQUEST_0_AMT0'          => $this->fullamount,
                //'L_PAYMENTREQUEST_0_ITEMCATEGORY0' => 'Digital', //You are not signed up to accept payment for digitally delivered goods.
                'L_BILLINGTYPE0'                   => 'RecurringPayments',
                'L_BILLINGAGREEMENTDESCRIPTION0'   => $this->description,
                'CANCELURL'                        => $this->cancelURL,
                'RETURNURL'                        => $this->returnURL,
                //'CALLBACK'                         => $this->notifyURL //Flat-rate shipping options are missing; you must specify flat-rate shipping options when you specify a callback URL.
            )
        );

        if( isset($result['ACK']) && $result['ACK'] == 'Success' ){

            $query = array(
                'cmd'    => '_express-checkout',
                'token'  => $result['TOKEN']
            );

            $_SESSION['TOKEN'] = $result['TOKEN'];

            $date = gmdate('Y-m-d H:i:s');

            global $wpdb;

            $wpdb->insert(
                $wpdb->transaction,
                array(
                    'token'         => $result['TOKEN'],
                    'status'        => 'create',
                    'type'          => $this->type,
                    'email'         => $this->email,
                    'name'          => $this->name,
                    'description'   => $this->description,
                    'period'        => $this->period,
                    'frequency'     => $this->frequency,
                    'amount'        => $this->amount,
                    'fullamount'    => $this->fullamount,
                    'coupon'        => $this->coupon,
                    'discount'      => $this->discount,
                    'currency_code' => $this->currency,
                    'date'          => $date,
                    'subscription'  => $this->subscription
                )
            );

            $redirectURL = sprintf('https://www.paypal.com/cgi-bin/webscr?%s', http_build_query($query));

            wp_redirect($redirectURL);
            exit;
        }
        else{
            $_SESSION['error_msg'] = print_r($result, true);
        }
    }

    /**
     * Get Checkout Details Data
     * @param $token
     * @return array
     */
    public function getCheckoutDetails( $token ){
        //@todo предварительно проверить данные которые приходят от getCheckoutDetails
        return $this->request(
            array(
                'METHOD'    => 'GetExpressCheckoutDetails',
                'VERSION'   => 108,
                'PWD'       => $this->password,
                'USER'      => $this->username,
                'SIGNATURE' => $this->signature,
                'TOKEN'     => $token
            )
        );
    }

    /**
     * Create Recurring Payments and return data to save in DataBase
     * @param $token
     * @param $payer_id
     * @return array
     */
    public function createRecurring( $token, $payer_id ){
        
        return $this->request(
            array(
                'METHOD'                        => 'CreateRecurringPaymentsProfile',
                'VERSION'                       => 108,
                'PWD'                           => $this->password,
                'USER'                          => $this->username,
                'SIGNATURE'                     => $this->signature,
                'TOKEN'                         => $token,
                'PayerID'                       => $payer_id,
                'PROFILESTARTDATE'              => gmdate('Y-m-d\TH:i:s\Z'),
                'DESC'                          => $this->description,
                'BILLINGPERIOD'                 => $this->period,
                'BILLINGFREQUENCY'              => $this->frequency,
                'AMT'                           => $this->fullamount,
                //'INITAMT'                       => $this->fullamount,
                'CURRENCYCODE'                  => $this->currency,
                'COUNTRYCODE'                   => 'US', //@todo запилить country code
                'MAXFAILEDPAYMENTS'             => 3,
/*
                'PAYMENTREQUEST_0_DESC'         => $this->description,
                'PAYMENTREQUEST_0_AMT'          => $this->fullamount,
                'PAYMENTREQUEST_n_CURRENCYCODE' => $this->currency,*/
            )
        );
    }

    /**
     * Create One-Time Payments and return data to save in DataBase
     * @param $token
     * @param $payer_id
     * @return array
     */
    public function doCheckoutPayment( $token, $payer_id ){

        return $this->request(
            array(
                'METHOD'                        => 'DoExpressCheckoutPayment',
                'PAYMENTACTION'                 => 'Sale',
                'VERSION'                       => 108,
                'PWD'                           => $this->password,
                'USER'                          => $this->username,
                'SIGNATURE'                     => $this->signature,
                'TOKEN'                         => $token,
                'PayerID'                       => $payer_id,
                'DESC'                          => $this->description,
                'AMT'                           => $this->fullamount,
                'CURRENCYCODE'                  => $this->currency,
                'COUNTRYCODE'                   => 'US', //@todo запилить country code
/*
                'PAYMENTREQUEST_0_DESC'         => $this->description,
                'PAYMENTREQUEST_0_AMT'          => $this->fullamount,
                'PAYMENTREQUEST_0_CURRENCYCODE' => $this->currency,*/
            )
        );
    }

    private function request( $args ){

        $data_field = http_build_query($args);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_URL, $this->endpoint);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_field);

        $response = curl_exec($curl);

        curl_close($curl);

        $result = array();

        if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
            foreach ($matches['name'] as $offset => $name) {
                $result[$name] = urldecode($matches['value'][$offset]);
            }
        }

        return $result;
    }
}