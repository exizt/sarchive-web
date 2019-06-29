<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostTag;
use Carbon\Carbon;

class BlogController extends Controller
{
	protected const VIEW_PATH = 'site.blog';
	protected const ROUTE_ID = 'blog';
	public function index()
	{
		$posts = Post::where('published_at', '<=', Carbon::now())
		->where('is_secret', '=', 0)
		->where('is_completed', '=', 1)
		->orderBy('published_at', 'desc')
		->paginate(config('blog.posts_per_page'));
		
		foreach ($posts as &$post){
			Post::setContentSummaryAlter($post);
		}
		unset($post);
		
		$data = $this->createViewData ();
		$data['posts'] = $posts;
		return view(self::VIEW_PATH.'.blog_index_card', $data);
	}
	
	public function showPost($slug)
	{
		try{
			$post = Post::whereSlug($slug)->where('is_secret', '=', 0)
			->where('is_completed', '=', 1)
			->firstOrFail();
			Post::setContentSummaryAlter($post);
			$data = $this->createViewData ();
			$data['post'] = $post;
			$data['disqus_pid'] = '/blog/'.$post->id;
			
			return view(self::VIEW_PATH.'.blog_post', $data);
		} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e){
			//return Redirect::to('error')->with('message', 'error');
			return view(self::VIEW_PATH.'.blog_notfound');
		}
	}

	public function indexTag(){
		// 태그 의 목록을 조회
	    $masterList = PostTag::orderBy ('count','desc')->orderBy ( 'created_at', 'desc' )->paginate ( 10 );
		
		$data = $this->createViewData ();
		$data ['tags'] = $masterList;
		return view ( self::VIEW_PATH . '.blog_tag_index', $data );
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function showTag($tag_name)
	{
		// 태그에 해당되는 게시물을 조회
		$tag = PostTag::where('tag', $tag_name)->firstOrFail();
		
		$posts = Post::whereHas('tags', function ($q) use ($tag) {
			$q->where('tag', '=', $tag->tag);
		})->orderBy ( 'created_at', 'desc' )->paginate ( 10 );
		
		$data = $this->createViewData ();
		$data ['tag_name'] = $tag_name;
		$data ['posts'] = $posts;
		return view ( self::VIEW_PATH . '.blog_tag_show', $data );
	}
	/**
	 *
	 * @return string[]
	 */
	protected function createViewData() {
		$data = array ();
		$data ['ROUTE_ID'] = self::ROUTE_ID;
		return $data;
	}
}
