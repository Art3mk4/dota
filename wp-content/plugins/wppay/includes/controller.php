<?php

/**
 * Description of controller
 * 
 * @author Artem Yuriev <Art3mk4@gmail.com> 12.05.2016 13:56:30
 */

function wppay_load_scripts_admin()
{
    wp_register_script('ajaxQueue', plugins_url('/wppay/js/jquery.ajaxQueue.min.js'), array( 'jquery' ), '0.1.2' );
    wp_register_script('bootstrapMin', plugins_url('/wppay/js/bootstrap.min.js'), array( 'jquery' ), '3.3.5' );
    wp_register_script('wppayScript', plugins_url('/wppay/js/script.js'), array( 'ajaxQueue', 'jquery-ui-dialog', 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.2' );

    $screen = get_current_screen();

    if ($screen->id == 'toplevel_page_wpPay') {
        wp_enqueue_script('bootstrapMin');
        wp_enqueue_script('wppayScript');
        wp_enqueue_style('wp-jquery-ui-dialog');
    }
}
add_action('admin_print_scripts', 'wppay_load_scripts_admin');

function wppay_load_styles_admin()
{
    $foo = array(
        'wppay-awesome' => plugins_url('/wppay/css/font-awesome.min.css?ver=3.2'),
        'wppay-ui'      => '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
        'wppay-bootstrap' => plugins_url('/wppay/css/bootstrap.min.css'),
        'wppay-style'   => plugins_url('/wppay/css/style.css?ver=3.2')
    );

    $screen = get_current_screen();

    if ($screen->id == 'toplevel_page_wpPay') {
        foreach ($foo as $key => $val) {
            printf('<link id="%s" href="%s" rel="stylesheet" type="text/css"/>' . "\n", $key, $val);
        }
    }
}
add_action('admin_head', 'wppay_load_styles_admin');

function wppay_admin_menu()
{
    if (function_exists('add_menu_page')) {
        add_menu_page(
            'wpPay',
            'wpPay',
            'activate_plugins',
            'wpPay',
            'wppay_admin_index',
            plugins_url('wppay/img/logo.png')
        );
    }
}
add_action('admin_menu', 'wppay_admin_menu');

function wppay_admin_index()
{
    $obj = new wpPay\Wpadmin();
    try {
        $obj->getTemplate();
    } catch (Exception $ex) {
        pr($e->getMessage());
    }
}