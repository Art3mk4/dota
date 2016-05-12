<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 01.03.2016
 * Time: 14:16
 */
namespace SendMail\Service;

use SendMail\Message as msg;

class Mandrill extends Service {
	protected $fields = array(
		'api_key' => array(
			'label'       => 'API Key Mandrill',
			'name'        => 'options[Mandrill][api_key]',
			'type'        => 'text',
			'placeholder' => 'api key',
			'value'       => '',
		)
	);

	public function __construct($options) {
		parent::__construct($options);
	}


	public function oneSend($to) {
		$to = array(array( 'email' => $to, 'name' => '', 'type' => 'to' ));
		$data  = array(
			'html'       => $this->message->html,
			'text'       => '',
			'subject'    => $this->message->subject,
			'from_email' => $this->message->from_email,
			'from_name'  => $this->message->from_name,
			'to'         => $to,
			'metadata'   => array( 'website' => get_option( 'home' ) ),
		);

		$params = array("message" => $data, "async" => false, "ip_pool" => null, "send_at" => null);
		$params[ 'key' ] = $this->api_key;

		$data = json_encode( $params );

		$url  = 'https://mandrillapp.com/api/1.0/messages/send.json';

		$args = array(
			'method'  => 'POST',
			'headers' => array(
				'Content-Type'  => 'application/json'
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

		return $this->formatmsg($res);
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