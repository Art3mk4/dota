<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 01.03.2016
 * Time: 14:55
 */

namespace SendMail\Message;


/**
 * @property  to
 */
class Message
{

	protected $options    = array();
	protected $optionsKey = array(
		'to',
		'from_email',
		'from_name',
		'subject',
		'html'
	);

	public function __construct()
	{
	}

	public function __set( $name, $value )
	{
		if ( in_array( $name, $this->optionsKey ) )
			$this->options[ $name ] = $value;
	}

	public function __get( $name )
	{
		if ( isset( $this->options[ $name ] ) ) {
			return $this->options[ $name ];
		}

		return '';
	}

	/**
	 * @return array
	 */
	public function getOptionsKey()
	{
		return $this->optionsKey;
	}
}