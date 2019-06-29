<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Post;
use App\Models\PostTag;
use Michelf\Markdown,Michelf\MarkdownExtra, Michelf\SmartyPants;
use Parsedown;

class PostController extends Controller
{
	protected const VIEW_PATH = 'admin.post';
	protected const ROUTE_ID = 'admin.post';
	public function __construct() {
		$this->middleware('auth');
	}
	
	/**
	 * Display a listing of the posts.
	 */
	public function index(Request $request)
	{
		$isAjax = $request->input('ajax',false);
		if(!$isAjax){
			$posts = Post::orderBy('published_at', 'desc')
			->paginate(10);
			
			$data = $this->createViewData ();
			
			foreach ($posts as &$post){
				Post::setContentSummaryAlter($post);
			}
			unset($post);
			
			$data['posts'] = $posts;
			return view(self::VIEW_PATH.'.index', $data);
		} else return $this->ajax($request);
	}
	
	/**
	 * Ajax 호출일 경우
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function ajax(Request $request)
	{
	    $num = $request->input ( 'num' , 10);
	    
	    $posts = Post::select(['title','id','created_at','published_at'])->orderBy ( 'published_at', 'desc' )->paginate ( $num );
		
		$resultSet = array();
		foreach ($posts as $post)
		{
			$item = new \stdClass();
			$item->title = $post->title;
			$item->id = $post->id;
			$item->created_at = $post->created_at->format('Y-m-d H:i');
			$item->published_at= $post->published_at->format('Y-m-d H:i');
			$resultSet[] = $item;
		}
		//echo json_encode($resultSet);
		return response()->json($resultSet);
	}
	
	public function show($id)
	{
		$post = Post::where('id',$id)->firstOrFail();
		$data = $this->createViewData ();
		$data['post'] = $post;
		
		
		return view(self::VIEW_PATH.'.show', $data);
	}
	
	public function indexTag(Request $request){
	    // 태그 의 목록을 조회
	    $masterList = PostTag::orderBy ('count','desc')->orderBy ( 'created_at', 'desc' )->paginate ( 10 );
	    
	    $data = $this->createViewData ();
	    $data ['tags'] = $masterList;
	    return view ( self::VIEW_PATH . '.tag_index', $data );
	}
	
	/**
	 * Show the new post form
	 */
	public function create()
	{
		$post = Post::createWithEmptyFields();
		
		$data = $this->createViewData ();
		$data['post'] = $post;
		return view(self::VIEW_PATH.'.create', $data);
	}
	
	/**
	 * Store a newly created Post
	 *
	 * @param Request $request
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
				'title' => 'required|unique:posts|max:255'
		]);
		
		$title = $request->input ( 'title' );
		$content = $request->input( 'content' );
		$content_summary = $request->input( 'content_summary' );
		$image_header = $request->input( 'image_header' );
		$is_secret = (bool)$request->input( 'is_secret' , false);
		$is_completed = ((bool)$request->input( 'is_incompleted' , false))? 0: 1;
		$publish_date = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('publish_date'));
		
		$my_html = MarkdownExtra::defaultTransform($content);
		$my_html = SmartyPants::defaultTransform($my_html);

		$data = array ();
		$data ['title'] = $title;
		$data ['content'] = $content;
		$data ['content_html'] = $my_html;
		$data ['content_summary'] = $content_summary;
		$data ['image_header'] = $image_header;
		$data ['is_secret'] = $is_secret;
		$data ['is_completed'] = $is_completed;
		$data ['published_at'] = $publish_date;
		
		$post = Post::create($data);
		$post->save();

		// sync tags
		$post->syncTags($request->get('tags', []));
		
		// result processing
		return redirect()
		->route(self::ROUTE_ID.'.index')
		->withSuccess('New Post Successfully Created.');
	}
	
	/**
	 * Show the post edit form
	 *
	 * @param  int  $id
	 */
	public function edit($id)
	{
		$post = Post::with('tags')->where('id',$id)->firstOrFail();
		$data = $this->createViewData ();
		$data['post'] = $post;
		if(stripos($_SERVER['HTTP_USER_AGENT'],"iPhone")){
			$data['editor'] = '';
		} else {
			$data['editor'] = 'simplemde';
		}
		return view(self::VIEW_PATH.'.edit',$data);
	}

	/**
	 * Update the Post
	 * 
	 * @param Request $request
	 * @param int $id
	 */
	public function update(Request $request, $id)
	{
		if ($request->is('admin/*')) {
			// 있는 값인지 id 체크
			$post = Post::findOrFail($id);
			
			// 값 셋팅
			$title = $request->input('title');
			$content = $request->input('content');
			$content_summary = $request->input( 'content_summary' );
			$image_header = $request->input( 'image_header' );
			$is_secret = (bool)$request->input( 'is_secret' , false);
			$is_completed = ((bool)$request->input( 'is_incompleted' , false))? 0: 1;
			$publish_date = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('publish_date'));
			
			//print_r($publish_date);exit;
			$Parsedown = new Parsedown();
			$content_html= $Parsedown->text($content); # prints: <p>Hello <em>Parsedown</em>!</p>
			//$my_html = Markdown::defaultTransform($content);
			//$my_html = SmartyPants::defaultTransform($my_html);
			
			// saving
			$post->title = $title;
			$post->content = $content;
			$post->content_html = $content_html;
			$post->content_summary = $content_summary;
			$post->image_header = $image_header;
			$post->published_at = $publish_date;
			$post->is_secret = $is_secret;
			$post->is_completed = $is_completed;
			$post->save();
			
			// sync tags
			$post->syncTags($request->get('tags', []));
			
			// after processing
			if ($request->action === 'continue') {
				return redirect()
				->back()
				->withSuccess('Post saved.');
			}
			return redirect()
			->route(self::ROUTE_ID.'.index')
			->withSuccess('Post saved.');
		} else {
			//failed
		}
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		$post = Post::findOrFail($id);
		$post->delete();

		return redirect()
		->route(self::ROUTE_ID.'.index')
		->withSuccess('Post deleted.');
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
