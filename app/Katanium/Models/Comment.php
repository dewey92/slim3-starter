<?php

namespace Katanium\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Comment extends Eloquent {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';
	protected $primaryKey = 'comment_id';
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	public $timestamps = true;
	
	/**
	 * Get the user that owns the post.
	 */
	public function user()
	{
		return $this->belongsTo('Katanium\Models\User', 'author');
	}

	public function post()
	{
		return $this->belongsTo('Katanium\Models\Post', 'post_id');
	}

}