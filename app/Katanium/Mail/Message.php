<?php

namespace Katanium\Mail;

/**
* Class that handles the message body of an email
*/
class Message
{
	protected $mailer;

	public function __construct( $mailer )
	{
		$this->mailer = $mailer;
	}

	public function to( $address )
	{
		$this->mailer->addAddress($address);
	}

	public function subject( $subject )
	{
		$this->mailer->Subject = $subject;
	}

	public function body( $body )
	{
		$this->mailer->Body = $body;
	}
}