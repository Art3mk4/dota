<?php
/**
 * User: Pavel Shishkin
 * Date: 01.03.2016
 * Time: 11:03
 */

namespace SendMail;

use SendMail\Service as srv;

class SendMail {

	public static $listService = array(
		'Mailgun'   => 'Mailgun',
		'Sparkpost' => 'Sparkpost',
		'Mandrill'  => 'Mandrill'
	);

	public static function i() {

		//$settings = self::settings();
		$settings = array(
			'service' => 'Mailgun',
			'options' => array(
				'api_key' => 'key-0dc63ed76a87aff3bc463b17e6a13621',
				'domain'  => 'socialrabbitplugin.com'
			)
		);

		if ( array_key_exists( $settings['service'], static::$listService ) ) {
			$class                   = 'SendMail\\Service\\' . $settings['service'];
			$sm                      = new $class( $settings['options'] );
			$sm->message->to         = '';
			$sm->message->from_email = '';
			$sm->message->from_name  = '';
			$sm->message->subject    = '';

			return $sm;
		}
		throw new \Exception( 'no class:' . $settings['service'] );
	}
/*
	public static function settings(){
		$defaults = array(
			'service'       => 'Mailgun',
			'options'       => array(),
		);

		$args = get_site_option( 'ads_notifi_service' );
		$args = ( ! $args ) ? array() : unserialize( $args );

		$args    = wp_parse_args( $args, $defaults );
		$options = isset( $args['options'][ $args['service'] ] ) ? $args['options'][ $args['service'] ] : array();

		return  array(
			'service'       => $args['service'],
			'options'       => $options
		);
	}
	
	public  static function saveSettings($params){
		$defaults = array(
			'service'       => 'Mailgun',
			'options'       => array(),
		);

		$args = wp_parse_args( $params, $defaults );

		$args_notifi = get_site_option( 'ads_notifi_service' );

		$args_notifi = ( !$args_notifi ) ? $defaults : unserialize( $args_notifi );
		$args_notifi = ( !$args_notifi ) ? $defaults : $args_notifi;
		$args['options'] = array_merge($args_notifi['options'], $args['options']);

		return update_site_option( 'ads_notifi_service', serialize( $args ) );
	}*/
	
}



