<?php

namespace Katanium\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Tag extends Eloquent {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tags';
	protected $primaryKey = 'tag_id';
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
}