<?php

namespace Katanium\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Email extends Eloquent {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'prelaunching';
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = array('password');
	public $timestamps = false;
}