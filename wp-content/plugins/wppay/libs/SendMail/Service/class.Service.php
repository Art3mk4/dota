<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 01.03.2016
 * Time: 14:16
 */
namespace SendMail\Service;

use SendMail\Message\Message;


abstract class Service implements iService{

	public $message;

	protected $fields = array();

	public function __construct($options)
	{
		$this->setFields($options);
		$this->message = new Message();
	}

	public function __set( $name, $value ) {
		if(in_array($name, $this->fields))
			$this->fields[$name]['value'] = $value;
	}

	public function __get( $name ) {
		return $this->fields[$name]['value'];
	}

	/**
	 * @return array
	 */
	public function getFields() {
		return $this->fields;
	}

	public function send() {
		$to = $this->message->to;
		$rez = array();
		if ( !is_array( $to ) ) {
			$to = array($to);
		}

		foreach($to as $mail)
			$rez[] = $this->oneSend($mail);

		return $rez;
	}

	public function oneSend($to){}

	/**
	 * @param array $options
	 */
	public function setFields( $options ) {
		//TODO
		foreach($this->fields as $key=>$field){
			if(isset($options[$key]))
				$this->fields[$key]['value'] = $options[$key];
		}
	}
}