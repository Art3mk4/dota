<?php
function dota_theme_scripts() {
    wp_enqueue_style( 'fonts', get_template_directory_uri() . '/css/font.css');
    wp_enqueue_style( 'bootstrap', get_template_directory_uri() .
        '/css/bootstrap.min.css'
    );
    /*if ( is_home() ) {*/
    wp_enqueue_style( 'style-name', get_stylesheet_uri() );    
    /*}*/
    
    wp_enqueue_style( 'font-awesome', get_template_directory_uri() .
        '/css/font-awesome.min.css');
    wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array('jquery'), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'dota_theme_scripts' );