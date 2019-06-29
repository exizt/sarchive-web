<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
	protected $fillable = ['tag'];

	/**
	 * The many-to-many relationship between tags and posts.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function posts()
	{
		return $this->belongsToMany('App\Models\Post', 'post_tag_relations','tag_id','post_id')->withTimestamps();
	}
}
