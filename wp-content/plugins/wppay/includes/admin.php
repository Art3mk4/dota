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
    }
    
    /**
     * tmplPayPal
     */
    public function tmplPayPal()
    {
        $args = elp_pay_settings('paypal');
        ?>
<h1>Pay Pal settings</h1>
        <?php
    }

    /**
     * tmplOrder
     */
    public function tmplOrder()
    {
        ?>
        <h1>Orders page</h1>
        <?php
    }
    
    /**
     * tmplDashboard
     */
    public function tmplDashboard()
    {
        ?>
        <h1>Dashboard</h1>
        <?php
    }
    
    public function tmplMailgun()
    {
        ?>
        <h1>Mailgun</h1>
        <?php
    }

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
}