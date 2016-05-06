<?php
function dota_theme_scripts() {
    wp_enqueue_style( 'fonts', '//fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700&subset=greek,cyrillic');
    wp_enqueue_style( 'bootstrap', get_template_directory_uri() .
        '/css/bootstrap.min.css'
    );
    wp_enqueue_style( 'style-name', get_stylesheet_uri() );
    wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css');
    wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array('jquery'), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'dota_theme_scripts' );