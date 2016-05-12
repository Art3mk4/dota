<?php
/**
 * Author: Vitaly Kukin
 * Date: 11.11.2015
 * Time: 13:04
 */

/**
 * Add scripts and styles to admin
 */
function elp_load_scripts_admin() {

    wp_register_script( 'ajaxQueue', plugins_url( '/elpay/js/jquery.ajaxQueue.min.js' ), array( 'jquery' ), '0.1.2' );
    wp_register_script( 'bootstrap', plugins_url( '/elpay/js/bootstrap.min.js' ), array( 'jquery' ), '3.3.5' );
    wp_register_script( 'elpayScript', plugins_url( '/elpay/js/script.js' ), array( 'ajaxQueue', 'jquery-ui-dialog', 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.2' );

    $screen = get_current_screen();

    if( $screen->id == 'toplevel_page_elpay' ) {

        wp_enqueue_script( 'elpayScript' );
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
    }
}
add_action('admin_print_scripts', 'elp_load_scripts_admin');

function elp_load_styles_admin(){

    $foo = array(
        'elpay-awesome' => plugins_url('/elpay/css/font-awesome.min.css?ver=3.2'),
        'elpay-ui'   => '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
        'elpay-style'   => plugins_url('/elpay/css/style.css?ver=3.2')
    );

    $screen = get_current_screen();

    if( $screen->id == 'toplevel_page_elpay' ) {

        foreach($foo as $key => $val)
            printf('<link id="%s" href="%s" rel="stylesheet" type="text/css"/>' . "\n", $key, $val);
    }
}
add_action( 'admin_head', 'elp_load_styles_admin' );

/**
 * Add menu items to admin_menu
 */
function elp_admin_menu() {

    if ( function_exists('add_menu_page') ) {
        add_menu_page(
            'elPay',
            'elPay',
            'activate_plugins',
            'elpay',
            'elp_admin_index',
            plugins_url( 'elpay/img/logo.png')
        );
    }
}
add_action('admin_menu', 'elp_admin_menu');

function elp_admin_index() {
    $obj = new \elpApp\elpAdmin();

    try {
        $obj->getTemplate();
    }
    catch( Exception $e ) { pr($e->getMessage()); }
}

function elp_action_discount(){
    echo  ' <a href="#" class="page-title-action" id="elp_add_new" data-title="' . __('Add New', 'elp') . '">' .
    __('Add New', 'elp') . '</a>';
}
add_action('elp_action_gallery', 'elp_action_discount');