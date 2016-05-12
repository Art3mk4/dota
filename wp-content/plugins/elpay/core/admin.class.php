<?php
/**
 * Author: Vitaly Kukin
 * Date: 11.11.2015
 * Time: 13:16
 */

namespace elpApp;

use appTemplate\Template;

class elpAdmin extends Template{

    function __construct() {

        parent::__construct();

        $this->page = 'elpay';
        $this->page_var = 'xpage';

        $this->templList(
            array(
                'dash'     => 'tmplDashboard',
                'order'    => 'tmplOrder',
                'paypal'   => 'tmplPayPal',
                'discount' => 'tmplDiscount',
                'mandrill' => 'tmplMandrill'
            )
        );

        $this->listMenu(
            array(
                'dash' 	=> array(
                    'title' 		=> __('Dasboard', 'elp'),
                    'description'	=> __('This displays the list of activities performed by elPay', 'elp'),
                    'icon' 			=> 'tachometer',
                    'separator'     => 'line'
                ),
                'order' 	=> array(
                    'title' 		=> __('Orders', 'sr'),
                    'description'	=> __('List of Transactions', 'sr'),
                    'icon' 			=> 'list'
                ),
                'paypal' 	=> array(
                    'title' 		=> __('PayPal', 'sr'),
                    'description'	=> __('Setup up Your PayPal setting', 'sr'),
                    'icon' 			=> 'paypal'
                ),
                'discount' 	=> array(
                    'title' 		=> __('Discount', 'sr'),
                    'description'	=> __('Setup up Your discount code', 'sr'),
                    'icon' 			=> 'percent'
                ),
                'mandrill' 	=> array(
                    'title' 		=> __('Mandrill', 'sr'),
                    'description'	=> __('Setup up Your Mandrill Settings', 'sr'),
                    'icon' 			=> 'envelope-o'
                )
            )
        );

        if( in_array($this->current, array('discount', 'paypal', 'mandrill')) ){
            $this->catch_config();
        }
    }

    public function tmplDashboard(){

        ?>
        <div class="content-inner">
            <div class="time-plate">
                <h3><?php _e('Current Time:', 'sr')?> <?php echo date('d.m.Y H:i:s')?></h3>
            </div>
	        <div class="row">
		        <div class="col-md-30">
			        <div class="item-plate first-child facebook">

			        </div>
                    <div class="list-activity first-child">

                    </div>
		        </div>
		        <div class="col-md-30">
			        <div class="item-plate last-child gallery">

			        </div>
			        <div class="item-plate last-child pinterest">

			        </div>
                    <div class="list-activity last-child">

                    </div>
		        </div>
            </div>
        </div>
    <?php
    }

    public function tmplOrder(){
        include_once( ELP_PATH . 'core/page-order.php');
    }

	public function tmplPayPal(){

		$args = elp_pay_settings('paypal');

        ?>
		<form action="" method="POST">

			<?php wp_nonce_field( 'elp_paypal_config', 'elp_setting_action' ); ?>

			<table class="form-table">
				<tbody>

				<?php

                foreach($args['fields'] as $key => $val){

                    if($val['type'] == 'text'){
                        $this->textField(
                            $key,
                            array(
                                'label'         => $val['name'],
                                'id'            => $key,
                                'value'         => $val['default'],
                                'description'   => $val['description'],
                                'class'         => 'regular-text'
                            )
                        );
                    }
                    elseif($val['type'] == 'select'){
                        $this->dropDownField(
                            $key,
                            array(
                                'label'         => $val['name'],
                                'selected'      => $val['default'],
                                'id'            => $key,
                                'values'        => $val['values'],
                                'description'   => $val['description'],
                            )
                        );
                    }
                }

                $this->btn(
                    'sr-config-submit',
                    array(
                        'id' => 'sr-config-submit'
                    )
                );

				?>

				</tbody>
			</table>
		</form>
	<?php
	}

    public function tmplDiscount(){

        $types = elp_access_types();

        ?>
        <form action="" method="POST">

            <?php wp_nonce_field( 'elp_discount_add', 'elp_setting_action' ); ?>

            <table class="form-table">
                <tbody>

                <?php

                $this->textField(
                    'code',
                    array(
                        'label'         => 'Code',
                        'id'            => 'code',
                        'value'         => elp_get_rand_string(9),
                        'description'   => 'Discount code',
                        'class'         => 'regular-text'
                    )
                );

                $this->textField(
                    'limit',
                    array(
                        'label'         => 'Limit',
                        'id'            => 'limit',
                        'value'         => 0,
                        'description'   => 'How much time will be used. If set 0 then the code has no limit',
                        'class'         => 'regular-text'
                    )
                );

                $this->dropDownField(
                    'type',
                    array(
                        'label'         => 'Type',
                        'selected'      => '',
                        'id'            => 'type',
                        'values'        => $types,
                        'description'   => 'Discounts type',
                    )
                );

                $this->textField(
                    'price',
                    array(
                        'label'         => 'Discount',
                        'id'            => 'price',
                        'value'         => '',
                        'placeholder'   => '19.00',
                        'description'   => 'Discount in the currency specified in the settings of the payment system',
                        'class'         => 'regular-text'
                    )
                );

                $this->textField(
                    'percent',
                    array(
                        'label'         => 'Percent',
                        'id'            => 'percent',
                        'value'         => '',
                        'placeholder'   => '10',
                        'description'   => 'Discount in percent by total amount',
                        'class'         => 'regular-text'
                    )
                );

                $this->textField(
                    'moreprice',
                    array(
                        'label'         => 'More Price',
                        'id'            => 'moreprice',
                        'value'         => '',
                        'placeholder'   => '56.00',
                        'description'   => 'If total price More that, the discount will be apply by percent or price value',
                        'class'         => 'regular-text'
                    )
                );

                $this->textField(
                    'date_start',
                    array(
                        'label'         => 'Date Start',
                        'id'            => 'date_start',
                        'value'         => '',
                        'description'   => 'Set date to start discount',
                        'placeholder'   => '20.01.2016',
                        'class'         => 'regular-text datepicker'
                    )
                );

                $this->textField(
                    'date_end',
                    array(
                        'label'         => 'Expired Date',
                        'id'            => 'date_end',
                        'value'         => '',
                        'description'   => 'If field is empty then the discount code has no expiry time',
                        'placeholder'   => '22.01.2016',
                        'class'         => 'regular-text datepicker'
                    )
                );

                $this->btn(
                    'sr-config-submit',
                    array(
                        'id' => 'sr-config-submit'
                    )
                );

                ?>

                </tbody>
            </table>
        </form>
        <?php

        include_once( ELP_PATH . 'core/page-discount.php');
    }

	public function tmplMandrill(){


		$args = get_site_option('elp-mail-setting');

        $foo = array(
            'api' => array(
                'name'        => __('Mandrill api', 'elp'),
                'default'     => '',
                'description' => __('Set up your Mandrill settings', 'elp')
            ),
            'logo' => array(
                'name'        => __('Logo', 'elp'),
                'default'     => '',
                'description' => __('Logo to Header mail message', 'elp')
            ),
            'from' => array(
                'name'        => __('Email', 'elp'),
                'default'     => '',
                'description' => __('Email to mail message', 'elp')
            ),
            'name' => array(
                'name'        => __('Name', 'elp'),
                'default'     => '',
                'description' => __('Name to mail message', 'elp')
            )
        );

        ?>
		<form action="" method="POST">

			<?php wp_nonce_field( 'elp_mandrill_config', 'elp_setting_action' ); ?>

			<table class="form-table">
				<tbody>

				<?php

                foreach($foo as $key => $val){

                    $this->textField(
                        $key,
                        array(
                            'label'         => $val['name'],
                            'id'            => $key,
                            'value'         => isset($args[$key]) ? $args[$key] : $val['default'],
                            'description'   => $val['description'],
                            'class'         => 'regular-text'
                        )
                    );
                }

                $this->btn(
                    'sr-config-submit',
                    array(
                        'id' => 'sr-config-submit'
                    )
                );

				?>

				</tbody>
			</table>
		</form>
	<?php
	}

    private function catch_config(){

        if( !isset($_POST['elp_setting_action']) ) return false;

        if( wp_verify_nonce($_POST['elp_setting_action'], 'elp_paypal_config')) {

            elp_pay_save_setting('paypal', $_POST);

            $this->message = __('Paypal settings have been saved!', 'elp');
        }

        if( wp_verify_nonce($_POST['elp_setting_action'], 'elp_mandrill_config')) {

            $foo  = array( 'api', 'logo', 'from', 'name');
            $args = array();

            foreach( $foo as $key ){

                $args[$key] = isset($_POST[$key]) ? trim($_POST[$key]) : '';
            }

            update_site_option('elp-mail-setting', $args);

            $this->message = __('Mandrill settings have been saved!', 'elp');
        }

        if( wp_verify_nonce($_POST['elp_setting_action'], 'elp_discount_add')) {

            $defaults  = array(
                'code'       => array( 'default' => elp_get_rand_string(9), 'call' => 'trim' ),
                'limit'      => array( 'default' => 0, 'call' => 'intval' ),
                'type'       => array( 'default' => 'all', 'call' => 'trim' ),
                'price'      => array( 'default' => 0.00, 'call' => 'elp_floatvalue' ),
                'percent'    => array( 'default' => 0, 'call' => 'intval' ),
                'moreprice'  => array( 'default' => 0.00, 'call' => 'elp_floatvalue' ),
                'date_start' => array( 'default' => date('Y-m-d H:i:s'), 'call' => 'date', 'format' => 'Y-m-d H:i:s' ),
                'date_end'   => array( 'default' => '0000-00-00 00:00:00', 'call' => 'date', 'format' => 'Y-m-d H:i:s' ),
            );
            $args = array();

            foreach( $defaults as $key => $val ){

                $args[$key] = $val['default'];

                if( isset($_POST[$key]) && !empty($_POST[$key]) ){
                    if($val['call'] == 'date')
                        $args[$key] = date($val['format'], strtotime($_POST[$key]));
                    else
                        $args[$key] = call_user_func($val['call'], $_POST[$key]);
                }
            }

            global $wpdb;

            $wpdb->insert($wpdb->discounts, $args);

            $this->message = __('The code has been added!', 'elp') . ' <strong>' . $args['code'] . '</strong>';
        }
    }

    public function message( $message ){
        if( $message != '' )
            printf('<div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="%s">
                        <span aria-hidden="true">&times;</span></button>
                        %s
                    </div>',
                __('Close', 'elp'), $message
            );
    }

    public function getTemplate(){

        $current = $this->current;

        $tmpls 	= $this->tmpls;
        $args	= $this->menu;

        if( !isset( $tmpls[$current] ) ) return false;

        $cancel = false;

        foreach($args as $key => $val) {

            if( $cancel ) continue;

            if( $key == $current ) {
                $this->title 		= $val['title'];
                $this->description 	= $val['description'];
                $this->icon 		= $val['icon'];

                $cancel = true;
            }

            if( isset($val['submenu']) ) {

                foreach( $val['submenu'] as $skey => $sval ) {

                    if( $skey == $current ) {
                        $this->title 		= $sval['title'];
                        $this->description 	= $sval['description'];
                        $this->icon 		= $val['icon'];

                        $cancel = true;
                    }
                }
            }
        }

        $method = $tmpls[$current];

        if( method_exists( $this, $method ) ) {

            ob_start();

            $this->$method();

            $content = ob_get_contents();

            ob_end_clean();

            $this->create( $content );

            return true;
        }
        else
            return false;
    }
}