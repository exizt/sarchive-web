<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $dates = ['published_at'];
	protected $fillable = ['title', 'content', 'content_html','content_summary','published_at','image_header','is_secret','is_completed'];
   
	
	static public function createWithEmptyFields(){
		$post = new Post();
		$post->title = '';
		$post->content = '';
		$post->content_summary = '';
		$post->tags = [];
		$post->image_header = '';
		$post->is_secret = false;
		$post->is_completed = true;
		$post->published_at = new Carbon();
		return $post;
	}
    /**
     * The many-to-many relationship between posts and tags.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
    	return $this->belongsToMany('App\Models\PostTag', 'post_tag_relations','post_id','tag_id')->withTimestamps();
    }
    
    /**
     * Sync tag relation adding new tags as needed
     *
     * @param array $tags
     */
    public function syncTags(array $tags)
    {
    	// tag 필요하다면 테이블에 추가함
    	if(count($tags) > 0){
    		$found = PostTag::whereIn('tag', $tags)->pluck('tag')->all();
    		
    		foreach (array_diff($tags, $found) as $tag) {
    			PostTag::create([
    					'tag' => $tag,
    			]);
    		}
    	}
    	
    	// tag_relations 에 추가
    	if (count($tags)) {
    		$this->tags()->sync(
    				PostTag::whereIn('tag', $tags)->pluck('id')->all()
    				);
    	} else {
    		$this->tags()->detach();
    	}
    }
    
    /**
     * title 이 지정이 될 때, 필요하다면 unique slug 를 생성한다.
     * @param string $value
     */
    public function setTitleAttribute($value)
    {
    	$this->attributes['title'] = $value;
    	
    	if(! $this->exists) {
    		//$this->attributes['slug'] = str_slug($value);
    		$this->setUniqueSlug($value, '');
    	}
    }
    
    /**
     * Recursive routine to set a unique slug
     *
     * @param string $title
     * @param mixed $extra
     */
    protected function setUniqueSlug($title, $extra)
    {
    	$slug = $this->slug($title.'-'.$extra);
    	
    	// 이미 있는 slug 인 경우에는, extra 카운트를 증가
    	if (static::whereSlug($slug)->exists()) {
    		$this->setUniqueSlug($title, $extra + 1);
    		return;
    	}
    	
    	$this->attributes['slug'] = $slug;
    }
    
    /**
     * Str::slug 참조
     * @param string $title
     * @param string $separator
     * @return string
     */
    protected function slug($title, $separator = '-')
    {
    	//$title = static::ascii($title);
    	
    	// Convert all dashes/underscores into separator
    	$flip = $separator == '-' ? '_' : '-';
    	
    	$title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);
    	
    	// Remove all characters that are not the separator, letters, numbers, or whitespace.
    	$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));
    	
    	// Replace all separator characters and whitespace by a single separator
    	$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);
    	
    	return trim($title, $separator);
    }
    
    static protected function setContentSummaryAlter(Post $post)
    {
    	if(is_null($post->content_summary)||strlen($post->content_summary) < 1){
    		$post->content_summary_alter = str_limit($post->content_html);
    	} else {
    		$post->content_summary_alter = $post->content_summary;
    	}
    }
}
