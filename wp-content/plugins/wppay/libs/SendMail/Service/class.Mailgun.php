<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 01.03.2016
 * Time: 14:16
 */
namespace SendMail\Service;

use SendMail\Message as msg;

class Mailgun extends Service {

	protected $fields = array(
		'api_key' => array(
			'label'       => 'API Key Mailgun',
			'name'        => 'options[Mailgun][api_key]',
			'type'        => 'text',
			'placeholder' => 'Api key',
			'value'       => '',
		),
		'domain'=> array(
			'label'       => 'Domain Mailgun',
			'name'        => 'options[Mailgun][domain]',
			'type'        => 'text',
			'placeholder' => 'Domain',
			'value'       => '',
		),
	);

	public function __construct($options) {
		parent::__construct($options);
	}

	public function oneSend($to) {
		$data = array(
			'from'    => $this->message->from_name . '< '.$this->message->from_email.'>',
			'to'      => $to,
			'subject' => $this->message->subject,
			'html'    => $this->message->html,
		);
		$url = 'https://api.mailgun.net/v3/' . $this->domain . '/messages';
		$args = array(
			'method'  => 'POST',
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'api:' . $this->api_key )
			),
			'body'    => $data
		);

		error_log(print_r($url, true));
		error_log(print_r($data, true));
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