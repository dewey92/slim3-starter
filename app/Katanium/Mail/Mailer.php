<?php

namespace Katanium\Mail;

/**
* Class that handles mailing functionality
*/
class Mailer
{
	protected $view;
	protected $mailer;

	public function __construct($view, $mailer)
	{
		$this->view = $view;
		$this->mailer = $mailer;
	}

	public function send($template, $data, $cb)
	{
		$message = new Message($this->mailer);

		$message->body( $this->view->render($template, $data) );
		call_user_func($cb, $message);

		if ( ! $this->mailer->send() ) {
			die( 'Mailer Error: ' . $this->mailer->ErrorInfo );
		}
		else {
			echo "Success sending email";
		}
	}
}