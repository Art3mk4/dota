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
                'order'   => 'tmplOrder',
                'paypal'  => 'tmplPayPal',
                'mailgun' => 'tmplMailgun'
            )
        );

        $this->listMenu(
            array(
                'dash' => array(
                    'title'       => __('Dasboard', 'elp'),
                    'description' => __('This displays the list of activities performed by elPay', 'elp'),
                    'icon'        => 'tachometer',
                    'separator'   => 'line'
                ),
                'order' => array(
                    'title'       => __('Orders', 'sr'),
                    'description' => __('List of Transactions', 'sr'),
                    'icon'        => 'list'
                ),
                'paypal' => array(
                    'title'       => __('PayPal', 'sr'),
                    'description' => __('Setup up Your PayPal settings', 'sr'),
                    'icon'        => 'paypal'
                ),
                'mailgun' => array(
                    'title'       => __('Mailgun', 'sr'),
                    'description' => __('Setup up Mailgun settings', 'sr'),
                    'icon'        => 'envelope'
                )
            )
        );

        if (in_array($this->current, array('paypal', 'mailgun'))) {
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
        ?>
        <h1>Mailgun</h1>
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
            $this->message = __('Paypal settings have been saved!', 'elp');
        }
    }
}