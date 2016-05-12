<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 01.03.2016
 * Time: 14:16
 */
namespace SendMail\Service;

use SendMail\Message as msg;

class Sparkpost extends Service {
	protected $fields = array(
		'api_key' => array(
			'label'       => 'API Key Sparkpost',
			'name'        => 'options[Sparkpost][api_key]',
			'type'        => 'text',
			'placeholder' => 'Api key',
			'value'       => '',
		)
	);

	public function __construct( $options ) {
		parent::__construct( $options );
	}

	public function send() {
		$to = $this->message->to;

		if ( ! is_array( $to ) ) {
			$to = array( $to );
		}
		$mailTo = array();
		foreach ( $to as $email ) {
			$mailTo[] = array( "address" => $email );
		}

		$data = array(
			"content"    => array(
				"from"    => array(
					'name' => $this->message->from_name,
					'email' => $this->message->from_email
				),
				"subject" => $this->message->subject,
				"html"    => $this->message->html,
			),
			"recipients" => $mailTo
		);

		$data = json_encode( $data );

		$url = 'https://api.sparkpost.com/api/v1/transmissions';

		$args     = array(
			'method'  => 'POST',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => $this->api_key,
			),
			'body'    => $data
		);
		$response = \wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			$res =  array(
				'send' => $to,
				'success'=> false,
				'error' => $response,
				'info'=> 'wp_error'
			);
		}else if ( wp_remote_retrieve_response_code( $response ) != '200' ) {
			$res = array(
				'send' => $to,
				'success'=> false,
				'error' => $response,
				'info'=> 'error code'
			);
		}else{
			$res = array(
				'send' => $to,
				'success' => true
			);
		}

		return array($this->formatmsg($res));
	}

	private function formatmsg($res){
		if($res['success']){
			$res['msg'] = 'Service sent a message';
		}else{
			$res['body']  = $res["error"]["body"];
			$res['msg'] = $res["error"]["response"]["message"];
			$res['code'] = $res["error"]["response"] ["code"];
		}
		return $res;
	}

}