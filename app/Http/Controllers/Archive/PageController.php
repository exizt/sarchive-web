<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller {
	protected const VIEW_PATH = 'archive.page';
	protected const ROUTE_ID = 'page';

	/**
	 * 생성자
	 */
	public function __construct() {
		$this->middleware ( 'auth' );
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
	    $masterList = Page::paginate(20);

		// dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['masterListSet'] = $masterList;
        return view ( self::VIEW_PATH . '.index', $dataSet );

	}
	

	/**
	 * 글 본문 읽기
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $pageTitle) {
	    $pageData = Page::where ( 'title', $pageTitle )->firstOrFail ();
	    
	    // create dataSet
	    $dataSet = $this->createViewData ();
	    $dataSet ['page'] = $pageData;
	    return view ( self::VIEW_PATH . '.show', $dataSet );
	}
	
	
	/**
	 * 글 작성
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request) {
	    $categoryId = $request->input('category', $this->rootCategoryId);
	    
	    // 
	    $article = collect ( new Archive () );
	    $article->title = '';
		$article->content = '';
		$article->reference = '';

	    // dataSet 생성
	    $dataSet = $this->createViewData ();
	    $dataSet ['article'] = $article;
	    $dataSet ['categories'] = $this->getSubCategories($categoryId);
	    $dataSet ['parameters']['categoryId'] = $categoryId;
	    $dataSet ['nav'] = $this->getDevMenus($categoryId);
	    return view ( self::VIEW_PATH . '.create', $dataSet );
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id) {
	    $request->session()->reflash();
	    $request->session()->keep(['devscrap-previousList']);
	    
	    //
	    $article = Archive::where ( 'id', $id )->firstOrFail ();
	    $categoryId = $article->category_id;
	    
	    // create dataSet
	    $dataSet = $this->createViewData ();
	    $dataSet ['article'] = $article;
	    $dataSet ['categories'] = $this->getCategories($categoryId);
	    $dataSet ['parameters']['categoryId'] = $categoryId;
	    $dataSet ['categoryPath'] = $this->getCategoryPath($categoryId);
	    $dataSet ['nav'] = $this->getDevMenus($categoryId);
	    return view ( self::VIEW_PATH . '.edit', $dataSet );
	    
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
		$title = $request->input ( 'title' );
		$content = $request->input ( 'content' );
		$category_id = $request->input ( 'category_id' , '1');
		$reference = $request->input ('reference');


		$dataSet = array ();
		$dataSet ['title'] = $title;
		$dataSet ['content'] = $content;
		$dataSet ['category_id'] = $category_id;
		$dataSet ['reference'] = $reference;

		
		$article = Archive::create ( $dataSet );
		$article->save ();
		
		$this->updateCategoryCountingAll();
		
		// result processing
		return redirect ()->route ( $this->rootRouteId . '.index' )->withSuccess ( 'New Post Successfully Created.' );
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		$this->seperateServiceRoot($request);
		$request->session()->reflash();
		$request->session()->keep(['devscrap-previousList']);
		
		// 있는 값인지 id 체크
		$article = Archive::findOrFail ( $id );
		
		$title = $request->input ( 'title' );
		$content = $request->input ( 'content' );
		$category_id = $request->input ( 'category_id' , '1');
		$reference = $request->input ('reference');
		
		// saving
		$article->title = $title;
		$article->content = $content;
		$article->category_id = $category_id;
		$article->reference = $reference;
		$article->save ();
		
		$this->updateCategoryCountingAll();
		// after processing
		if ($request->action === 'continue') {
			return redirect ()->back ()->withSuccess ( 'Post saved.' );
		}
		return redirect ()->route ( $this->rootRouteId . '.show', $id)->withSuccess ( 'Post saved.' );
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {

	    $article = Archive::findOrFail($id);
	    $article->delete();
		
	    $this->updateCategoryCountingAll();
	    
		return redirect()
		->route($this->rootRouteId.'.index')
		->withSuccess('Post deleted.');
	}
	
	/**
	 * 
	 * @return string[]
	 */
	protected function createViewData() {
	    $dataSet = array ();
		$dataSet ['ROUTE_ID'] = self::ROUTE_ID;
	    $dataSet ['VIEW_PATH'] = self::VIEW_PATH;
	    $dataSet ['parameters'] = array();
	    return $dataSet;
	}
	    
}
