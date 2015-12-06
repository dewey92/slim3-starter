<?php

namespace Katanium\Helpers;

use Violin\Violin;
use Katanium\Models\User;

/**
* It's only a helper class to hash password
*/
class Validator extends Violin
{
	public function __construct()
	{
		$this->addFieldMessages([
			'email' => ['uniqueEmail' => 'Maaf! Email ini sudah terdaftar di Katanium']
		]);
	}

	public function validate_uniqueEmail( $value, $input, $args )
	{
		return ! (bool) User::where('email', $value)->count();
	}
}