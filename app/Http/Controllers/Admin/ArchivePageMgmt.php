<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Page;


class ArchivePageMgmt extends Controller {
	protected const VIEW_PATH = 'admin.archive_page';
	protected const ROUTE_ID = 'admin.archivePage';

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
		$userId = Auth::id();

	    $masterList = Page::where('user_id',$userId)->paginate(20);

		// dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['masterListSet'] = $masterList;
        return view ( self::VIEW_PATH . '.index', $dataSet );

	}
	
	/**
	 * 글 작성
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request) {
	    $item = new Page;

		// dataSet 생성
	    $dataSet = $this->createViewData ();
	    $dataSet ['item'] = $item;
	    return view ( self::VIEW_PATH . '.create', $dataSet );
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id) {
	    //
	    $item = Page::findOrFail ( $id );
		
		// 잘못된 접근일 때에.
		if($item->user_id != Auth::id()){
			abort(404);
		}

	    // create dataSet
	    $dataSet = $this->createViewData ();
	    $dataSet ['item'] = $item;
	    return view ( self::VIEW_PATH . '.edit', $dataSet );
	    
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//title의 중복체크를 해야함.
		$title = $request->input ( 'title' );
		$content = $request->input ( 'content' );
		
		//title 값의 중복 체크를 필요로 함. user_id, title 로 중복체크.
		if(Page::where([['user_id',Auth::id()],['title',$title]])->exists()){
			//abort(404);
			return redirect ()->back ()->withErrors ( ['페이지명이 중복되었습니다. (유일한 페이지명으로 입력해주세요)'] );
		}
		
		$item = new Page;
		$item->title = $title;
		$item->content = $content;
		$item->user_id = Auth::id();
		$item->save();

		// result processing
		return redirect ()->route ( self::ROUTE_ID . '.edit', ['id'=>$item->id] )->with('message', '페이지를 생성하였습니다.');
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		// 있는 값인지 id 체크
		$item = Page::findOrFail ( $id );
		
		$title = $request->input ( 'title' );
		$content = $request->input ( 'content' );

		// 잘못된 접근일 때에.
		if($item->user_id != Auth::id()){
			abort(404);
		}
		
		if($item->title != $title){
			///... 타이틀이 변경될 때
			//title 값의 중복 체크를 필요로 함. user_id, title 로 중복체크.
			if(Page::where([['user_id',Auth::id()],['title',$title]])->exists()){
				//abort(404);
				return redirect ()->back ()->withErrors ( ['페이지명이 중복되었습니다. (유일한 페이지명으로 입력해주세요)'] );
			}
			
		}
		// saving
		$item->title = $title;
		$item->content = $content;
		$item->save ();
		
		// after processing
		//if ($request->action === 'continue') {
			//return redirect ()->back ()->withSuccess ( 'Post saved.' );
		//}
		//return redirect ()->route ( self::ROUTE_ID . '.show', $id)->withSuccess ( 'Post saved.' );
		return redirect ()->back ()->with ('message', '저장이 완료되었습니다.' );
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id) {

		$item = Page::findOrFail($id);
		
		// 잘못된 접근일 때에.
		if($item->user_id != Auth::id()){
			abort(404);
		}

	    $item->delete();
	    
		return redirect()
		->route(self::ROUTE_ID.'.index')
		->with('message','Post deleted.');
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
