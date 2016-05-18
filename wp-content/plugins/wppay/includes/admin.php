<?php

/**
 * Description of Wpadmin
 *
 * @author Artem Yuriev <Art3mk4@gmail.com> 12.05.2016 12:26:05
 */

namespace wpPay;
use appWtemplate\Template;

class Wpadmin extends Template
{
    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->page = 'wpPay';
        $this->page_var = 'xpage';
        
        $this->templList(
            array(
                'dash'    => 'tmplDashboard',
                'guide'   => 'tmplGuide',
                'order'   => 'tmplOrder',
                'paypal'  => 'tmplPayPal',
                'mailgun' => 'tmplMailgun'
            )
        );

        $this->listMenu(
            array(
                'dash' => array(
                    'title'       => __('Dasboard', 'wppay'),
                    'description' => __('This displays the list of activities performed by elPay', 'wppay'),
                    'icon'        => 'tachometer',
                    'separator'   => 'line'
                ),
                'guide' => array(
                    'title'       => __('Guide settings', 'wppay'),
                    'description' => __('Set options for guide'),
                    'icon'        => 'usd'
                ),
                'order' => array(
                    'title'       => __('Orders', 'wppay'),
                    'description' => __('List of Transactions', 'wppay'),
                    'icon'        => 'list'
                ),
                'paypal' => array(
                    'title'       => __('PayPal', 'sr'),
                    'description' => __('Setup up Your PayPal settings', 'wppay'),
                    'icon'        => 'paypal'
                ),
                'mailgun' => array(
                    'title'       => __('Mailgun', 'sr'),
                    'description' => __('Setup up Mailgun settings', 'wppay'),
                    'icon'        => 'envelope'
                )
            )
        );

        if (in_array($this->current, array('paypal', 'mailgun', 'guide'))) {
            $this->catch_config();
        }
    }

    /**
     * tmplPayPal
     */
    public function tmplPayPal()
    {
        $args = wppay_pay_settings('paypal');
        ?>
        <form action="" method="POST">
            <?php wp_nonce_field('wppay_paypal_config', 'wppay_setting_action'); ?>
            <?php 
            foreach ($args['fields'] as $key => $val):
                if ($val['type'] == 'text') {
                    $this->textField(
                        $key,
                        array(
                            'label'       => $val['name'],
                            'id'          => $key,
                            'value'       => $val['default'],
                            'description' => $val['description'],
                            'class'       => 'regular-text'
                        )
                    );
                } elseif ($val['type'] == 'select') {
                    $this->dropDownField(
                        $key,
                        array(
                            'label'       => $val['name'],
                            'selected'    => $val['default'],
                            'id'          => $key,
                            'values'      => $val['values'],
                            'description' => $val['description'],
                        )
                    );
                }
            endforeach;

            $this->btn(
                'sr-config-submit',
                array(
                    'id' => 'sr-config-submit'
                )
            );?>
        </form>
	<?php
    }

    /**
     * tmplOrder
     */
    public function tmplOrder()
    {
        include_once( WPPAY_PATH . 'includes/page-order.php');
    }

    /**
     * tmplDashboard
     */
    public function tmplDashboard()
    {
        ?>
            <h1>Dashboard<?php _e('Current Time:', 'sr')?> <?php echo date('d.m.Y H:i:s')?></h1>
        <?php
    }

    /**
     * tmplMailgun
     */
    public function tmplMailgun()
    {
        $args = get_site_option('wppay_mailgun_setting');

        $foo = array(
            'domain' => array(
               'name'         => __('Mailgun Domain name', 'wppay'),
                'default'     => '',
                'description' => __('Set up your Domain name', 'wppay')
            ),
            'api' => array(
                'name'        => __('Mailgun api', 'wppay'),
                'default'     => '',
                'description' => __('Set up your Mailgun settings', 'wppay')
            ),
            'logo' => array(
                'name'        => __('Logo', 'wppay'),
                'default'     => '',
                'description' => __('Logo to Header mail message', 'wppay')
            ),
            'from' => array(
                'name'        => __('Email', 'wppay'),
                'default'     => '',
                'description' => __('Email to mail message', 'wppay')
            ),
            'name' => array(
                'name'        => __('Name', 'wppay'),
                'default'     => '',
                'description' => __('Name to mail message', 'wppay')
            )
        );?>

        <form action="" method="POST">
            <?php wp_nonce_field('wppay_mailgun_config', 'wppay_setting_action'); ?>
            <?php
            foreach($foo as $key => $val) {
                $this->textField(
                    $key,
                    array(
                        'label'       => $val['name'],
                        'id'          => $key,
                        'value'       => isset($args[$key]) ? $args[$key] : $val['default'],
                        'description' => $val['description'],
                        'class'       => 'regular-text'
                    )
                );
            }

            $this->btn(
                'sr-config-submit',
                array(
                    'id' => 'sr-config-submit'
                )
            );?>
        </form>
	<?php
    }
    
    public function tmplGuide()
    {
        $args = get_site_option('wppay_guide_setting');

        $foo = array(
            'price' => array(
                'name'         => __('Guide price', 'wppay'),
                'default'     => '',
                'description' => __('Set up price for Guide', 'wppay')
            ),
            'name' => array(
            	'name' => __('Guide name', 'wppay'),
            	'default' => '',
            	'description' => __('Set up name', 'wppay')
            ),
            'description' => array(
            	'name' => __('Guide description', 'wppay'),
            	'default' => '',
            	'description' => __('Set up description', 'wppay')
            )
        );?>

        <form action="" method="POST">
            <?php wp_nonce_field('wppay_guide_config', 'wppay_setting_action'); ?>
            <?php
            foreach($foo as $key => $val) {
                $this->textField(
                    $key,
                    array(
                        'label'       => $val['name'],
                        'id'          => $key,
                        'value'       => isset($args[$key]) ? $args[$key] : $val['default'],
                        'description' => $val['description'],
                        'class'       => 'regular-text'
                    )
                );
            }

            $this->btn(
                'sr-config-submit',
                array(
                    'id' => 'sr-config-submit'
                )
            );?>
        </form>
        <?php
    }

    /**
     * getTemplate
     * 
     * @return boolean
     */
    public function getTemplate()
    {
        $current = $this->current;

        $tmpls = $this->tmpls;
        $argc  = $this->menu;
        if (!isset($tmpls[$current])) {
            return false;
        }

        $cancel = false;
        foreach ($argc as $key => $val) {
            if ($cancel) {
                continue;
            }

            if ($key == $current) {
                $this->title = $val['title'];
                $this->description = $val['description'];
                $this->icon = $val['icon'];
                $cancel = true;
            }

            if (isset($val['submenu'])) {
                foreach($val['submenu'] as $skey => $sval) {
                    if ($skey == $current) {
                        $this->title = $sval['title'];
                        $this->description = $sval['description'];
                        $this->icon = $val['icon'];
                        $cancel = true;
                    }
                }
            }
        }

        $method = $tmpls[$current];
        if (method_exists($this, $method)) {
            ob_start();
            $this->$method();
            $content = ob_get_contents();
            ob_end_clean();
            $this->create($content);
            return true;
        } else {
            return false;
        }
    }

    /**
     * catch_config
     */
    private function catch_config()
    {
        if (!isset($_POST['wppay_setting_action'])) {
            return false;
        }

        if (wp_verify_nonce($_POST['wppay_setting_action'], 'wppay_paypal_config')) {
            wppay_pay_save_setting('paypal', $_POST);
            $this->message = __('Paypal settings have been saved!', 'wppay');
        }

        if (wp_verify_nonce($_POST['wppay_setting_action'], 'wppay_mailgun_config')) {
            $args = $this->fill(array('domain', 'api', 'logo', 'from', 'name'));

            update_site_option('wppay_mailgun_setting', $args);
            $this->message = __('Mailgun settings have been saved!', 'wppay');
        }

        if (wp_verify_nonce($_POST['wppay_setting_action'], 'wppay_guide_config')) {
            $args = $this->fill(array('price', 'name', 'description'));
            update_site_option('wppay_guide_setting', $args);
            $this->message = __('Guide settings have been saved!', 'wppay');
        }
    }
    
    /**
     * fill
     * 
     * @param type $params
     * @return type
     */
    private function fill($params)
    {
        $args = array();
        foreach ($params as $key) {
            $args[$key] = isset($_POST[$key]) ? trim($_POST[$key]) : '';
        }
        return $args;
    }
}