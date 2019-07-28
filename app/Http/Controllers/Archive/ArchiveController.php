<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\ArchiveBoard;
use App\Models\ArchiveCategoryRel;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller {
	protected const VIEW_PATH = 'app.archive';
	protected const ROUTE_ID = 'archives';
	protected $ArchiveProfile;

	/**
	 * 생성자
	 */
	public function __construct() {
		$this->middleware ( 'auth' );
	}


	/**
	 * Display a listing of the resource.
	 * category 값이 넘어오면, 해당되는 카테고리의 목록이 출력된다.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request, $profileId) {
		$this->getArchiveProfileFromID($profileId);

	    $boardId = $request->input('board', $this->ArchiveProfile->root_board_id);
		$is_only = (bool)$request->input( 'only' , false);

		if($is_only){
			///... board_id 에만 해당되는 게시물 목록
			$masterList = Archive::select(['id', 'title','summary_var','reference','board_id','created_at','updated_at','category'
			,DB::raw('(select name from sa_boards as s where s.id = board_id) as board')])
			->where ( 'board_id',$boardId )
			->orderBy ( 'created_at', 'desc' )
			->paginate(20);

		} else {
			///... 하위 board 까지 포함되는 게시물 목록

			/*
			|------------------------------------------------
			| 게시판에 해당되는 게시글 목록을 위한 쿼리. 하위 게시판의 글까지 보여준다.
			|------------------------------------------------
			*/
			/*
			// 첫번째 쿼리. 
			$posts = Archive::select('sa_archives.*',DB::raw('(select name from sa_boards as s where s.id = board_id) as category_name'))
			->whereRaw('board_id in 
				(
				SELECT node.board_id
				FROM sa_board_tree AS node ,
					sa_board_tree AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
				and parent.board_id = ?
				)',[$categoryId])->orderBy ( 'created_at', 'desc' )->paginate(30);
			*/
			// 속도를 조금 더 손본 쿼리. 전에는 where in 구문을 활용했는데, 속도가 너무 느려서 join 방식으로 변경.
			//DB::enableQueryLog();
			$masterList = Archive::select(['id', 'title','summary_var','reference','board_id','created_at','updated_at','category'
				,DB::raw('(select name from sa_boards as s where s.id = board_id) as board')])
				->join(DB::raw("(
				select node.board_id as t_board_id, parent.board_id as t_parent_id
				from sa_board_tree AS node ,
					sa_board_tree AS parent
				where node.lft between parent.lft and parent.rgt
				) as t"),'t.t_board_id','=','board_id')
				->where ( 't.t_parent_id',$boardId )
				->orderBy ( 'created_at', 'desc' )
				->paginate(20);
				//print_r(DB::getQueryLog());
		}

		$archiveBoard = ArchiveBoard::select(['name', 'comment'])->where ( 'id', $boardId )->firstOrFail ();

        //$categoryName = ArchiveBoard::select('name')->where ( 'id', $boardId )->firstOrFail ()->name;
		
		// dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['masterList'] = $masterList;
		$dataSet ['archiveBoard'] = $archiveBoard;
		$dataSet ['mPaginationParams']['board'] = $boardId;
		if($is_only) $dataSet ['mPaginationParams']['only'] = true;
		$dataSet ['parameters']['board'] = $boardId;
		$dataSet ['parameters']['profile'] = $profileId;
        return view ( self::VIEW_PATH . '.index', $dataSet );

	}
	
	/**
	 * 
	 * @param Request $request
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function search(Request $request) {
		$this->getArchiveProfile($request);

	    $boardId = $request->input('board', $this->ArchiveProfile->root_board_id);
	    $word = $request->input('q','');
	    
	    if(mb_strlen($word) < 2){
	        echo '검색어가 너무 짧음.';
	    } else {
			$masterList = Archive::select(['id', 'title','summary_var','reference','board_id','created_at','updated_at','category'
				,DB::raw('(select name from sa_boards as s where s.id = board_id) as category_name')])
				->join(DB::raw("(
				select node.board_id as t_board_id, parent.board_id as t_parent_id
				from sa_board_tree AS node ,
					sa_board_tree AS parent
				where node.lft between parent.lft and parent.rgt
				) as t"),'t.t_board_id','=','board_id')
				->where ( 't.t_parent_id',$boardId )
				->orderBy ( 'created_at', 'desc' )
				->search($word)
				->paginate(30);

    	    // dataSet 생성
    	    $dataSet = $this->createViewData ();
    	    $dataSet ['articles'] = $masterList;
    	    $dataSet ['parameters']['board'] = $boardId;
    	    $dataSet ['parameters']['q'] = $word;
    	    return view ( self::VIEW_PATH . '.search', $dataSet );
	        
	    }
	}
	
	/**
	 * 아카이브 화면에서 nav 메뉴를 가져오는 Ajax 부분.
	 */
	public function doAjax_getBoardList(Request $request){
		// 프로필 아이디 도 확인해봐야 함...
		// user_id 로 profile id 와 같이 조회해서 있는지를 체크하면 됨.

		$boardId = $request->input('board_id',1);

		$currentNode = ArchiveBoard::select(['id','name','parent_id','path','depth'])->where ( 'id', $boardId )->firstOrFail ();

		$masterList = DB::select("select id, name, parent_id, count, depth
		from `sa_boards`
		inner join (
		 select node.board_id as node_id, parent.board_id as parent_node_id
		 from sa_board_tree as node, sa_board_tree as parent 
		 where node.lft between parent.lft and parent.rgt 
		   and parent.board_id = ?
		   ) as tree 
	   on `tree`.`node_id` = `sa_boards`.`id` 
	   order by `index` asc",[$boardId]);

		$dataSet['list'] = $masterList;
		$dataSet['current'] = $currentNode;
		return response()->json($dataSet);
	}

	public function doAjax_getHeaderNav(Request $request){
		$this->getArchiveProfile($request);
		//$archiveBoardList = ArchiveBoard::find($article->board_id);
		$masterList = $archiveBoardList = ArchiveBoard::select(['id','name','parent_id','depth'])
		->where([['profile_id',$this->ArchiveProfile->id],['depth','2']])
		->orderBy('index','asc')->get();

		$dataSet['list'] = $masterList;
		return response()->json($dataSet);
	}

	/**
	 * 글 본문 읽기
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $profileId, $archiveId) {
		$this->getArchiveProfileFromID($profileId);
	    $article = Archive::where ( 'id', $archiveId )->firstOrFail ();
		$archiveBoard = ArchiveBoard::find($article->board_id);
		
		if($article->profile_id != $profileId){
			// fail 처리 하기. (사실 안 해도 되지만..)
			abort(404);
		}

	    // create dataSet
	    $dataSet = $this->createViewData ();
		$dataSet ['article'] = $article;
		$dataSet ['boardPath'] = json_decode($archiveBoard->path);
	    $dataSet ['previousList'] = $this->makePreviousListLink($request, $profileId);
		//$dataSet ['parameters']['board'] = $article->board_id;
		$dataSet ['parameters']['profile'] = $profileId;
	    return view ( self::VIEW_PATH . '.show', $dataSet );
	}
	
	
	/**
	 * 글 작성
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request, $profileId) {
		$this->getArchiveProfileFromID($profileId);
	    $boardId = $request->input('board', $this->ArchiveProfile->root_board_id);

		$article = new Archive;
		$archiveBoardList = ArchiveBoard::select(['id','name','parent_id','depth'])
		->where ( 'profile_id', $profileId )
		->orderBy('index','asc')->get();

	    // dataSet 생성
	    $dataSet = $this->createViewData ();
	    $dataSet ['article'] = $article;
		//$dataSet ['parameters']['board'] = $boardId;
		$dataSet ['parameters']['profile'] = $profileId;
		$dataSet ['cancelButtonLink'] = $this->makePreviousListLink($request,$profileId);
		$dataSet ['selectedBoard'] = $boardId;
		$dataSet ['boardList'] = $archiveBoardList;
	    return view ( self::VIEW_PATH . '.create', $dataSet );
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $profileId, $archiveId) {
		$this->getArchiveProfileFromID($profileId);
	    $request->session()->reflash();
	    $request->session()->keep(['devscrap-previousList']);
		
	    //
	    $article = Archive::where ( 'id', $archiveId )->firstOrFail ();
	    $boardId = $article->board_id;

		// 게시판 목록을 조회. 셀렉트박스 를 만들기 위함.
		$archiveBoardList = ArchiveBoard::select(['id','name','parent_id','depth'])
		->where ( 'profile_id', $profileId )
		->orderBy('index','asc')->get();

	    // create dataSet
	    $dataSet = $this->createViewData ();
		$dataSet ['article'] = $article;
		$dataSet ['parameters']['profile'] = $profileId;
		$dataSet ['cancelButtonLink'] = $this->makePreviousShowLink($request,$profileId, $archiveId);
	    $dataSet ['selectedBoard'] = $boardId;
		$dataSet ['boardList'] = $archiveBoardList;
	    return view ( self::VIEW_PATH . '.edit', $dataSet );
	    
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, $profileId) {
		$this->getArchiveProfileFromID($profileId);
		//
		$title = $request->input ( 'title' );
		$content = $request->input ( 'content' );
		$board_id = $request->input ( 'board_id' , '1');
		$reference = $request->input ('reference');
		$category = $request->input ('category');

		// insert row		
		$article = new Archive;
		$article->title = $title;
		$article->content = $content;
		$article->board_id = $board_id;
		$article->reference = $reference;
		$article->category = $category;
		$article->profile_id = $profileId;
		$article->save ();
		
		$this->updateBoardCount();
		
		foreach($article->category_array as $item){
			ArchiveCategoryRel::create(['profile_id'=>$profileId,'archive_id'=>$archiveId,'category'=>$item]);
		}

		// result processing
		return redirect ()->route ( self::ROUTE_ID . '.show',['profile'=>$profileId, 'archive'=>$article->id] )->withSuccess ( 'New Post Successfully Created.' );
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $profileId, $archiveId) {
		$this->getArchiveProfile($request);
		$request->session()->reflash();
		$request->session()->keep(['devscrap-previousList']);
		
		
		$title = $request->input ( 'title' );
		$content = $request->input ( 'content' );
		$board_id = $request->input ( 'board_id' , '1');
		$reference = $request->input ('reference');
		$category = $request->input ('category');

		// saving
		// 있는 값인지 id 체크
		$article = Archive::findOrFail ( $archiveId );
		$beforeCategory = $article->category;
		$article->title = $title;
		$article->content = $content;
		$article->board_id = $board_id;
		$article->reference = $reference;
		$article->category = $category;
		$article->save ();

		$this->updateBoardCount();

		// category 관련 처리
		if($beforeCategory != $category){
			ArchiveCategoryRel::where('archive_id',$archiveId)->delete();

			foreach($article->category_array as $item){
				ArchiveCategoryRel::create(['profile_id'=>$profileId,'archive_id'=>$archiveId,'category'=>$item]);
			}
		}

		// after processing
		if ($request->action === 'continue') {
			return redirect ()->back ()->withSuccess ( 'Post saved.' );
		}
		return redirect ()->route ( self::ROUTE_ID . '.show',['profile'=>$profileId, 'archive'=>$article->id])->withSuccess ( 'Post saved.' );
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($profileId, $archiveId) {
		$this->getArchiveProfileFromID($profileId);

	    $item = Archive::findOrFail($archiveId);
	    $item->delete();
		
	    $this->updateBoardCount();
	    
		return redirect()
		->route(self::ROUTE_ID.'.index', ['profile'=>$profileId])
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

	
	/**
	 * 분기에 따른 처리.
	 * '개발 전용' 과 '일반 전용' 으로 구분. 향후에 더 나뉘어질 수 있음. 귀찮으니 하드코딩한다. 
	 */
	private function getArchiveProfile(Request $request)
	{
		$userId = Auth::id();

		if($request->has('profile')){
			$profileId = $request->input('profile');
			// routeId 를 이용한 접근
			//$this->ArchiveProfile = Profile::select(['name','root_board_id','route'])->where ( [['user_id', $userId ],['route',$ArchiveRouteId]])->firstOrFail ();
	
			// profileId 를 이용한 접근
			$this->ArchiveProfile = Profile::select(['id','name','root_board_id','route'])
				->where ( [['user_id', $userId ],['id',$profileId]])
				->firstOrFail ();
			
		} else {
			$this->ArchiveProfile = Profile::select(['id','name','root_board_id','route'])
			->where ( [['user_id', $userId ],['is_default','1']])
			->firstOrFail ();
		}
	}

		
	/**
	 * Archive Profile 의 정보를 조회. 
	 * this->ArchiveProfile 에 내용이 담긴다.
	 * profileId 를 인수로 받고, userID 를 통하여 일차적으로 인증을 한다.
	 * 해당 profile Id 에 접근 권한이 있는지 체크하게 됨. 권한이 없으면 Fail
	 */
	private function getArchiveProfileFromID($profileId)
	{
		$userId = Auth::id();
		// profileId 를 이용한 접근
		$this->ArchiveProfile = Profile::select(['id','name','root_board_id','route'])
			->where ( [['user_id', Auth::id() ],['id',$profileId]])
			->firstOrFail ();
	}


		
	/**
	 * boardList 테이블의 count 값을 갱신
	 */
	private function updateBoardCount()
	{
	    /*
	    $affected = DB::update('update sa_boards 
            set count = (select count(id) from archives
            where archives.board_id = sa_boards.id
            group by board_id)');
        */
	    
	    // 좀 더 세밀화 된 쿼리
	    DB::update('update sa_boards
            set count = (select count(sa_archives.id)
                from 
                sa_archives,
                (SELECT node.board_id, parent.board_id as parent_id
                	FROM sa_board_tree AS node ,
                			 sa_board_tree AS parent
                	WHERE node.lft BETWEEN parent.lft AND parent.rgt
                ) as cate
                WHERE 
                cate.board_id = sa_archives.board_id
                and cate.parent_id = sa_boards.id
                group by cate.parent_id)');
	    
	}
	
	/**
	 * 이전 링크 주소.
	 * 바로 이전 주소를 가지고 셋팅을 하는데, '새로고침' 을 하는 경우도 있기 때문에, 세션에 넣어두고 활용한다.
	 * 뭔가 동작이 원하는 느낌이 아니다...살펴봐야 할 듯...
	 * @param Request $rqeust
	 * @return string
	 */
	protected function makePreviousListLink(Request $request, $profileId)
	{
		$previous = url()->previous();
		
		$routeLink = route ( self::ROUTE_ID . '.index', ['profile'=> $profileId]);

		return (strtok($previous,'?') == $routeLink) ? $previous : $routeLink;
	}

	/**
	 * '취소 링크' 생성.
	 */
	protected function makePreviousShowLink(Request $request, $profileId, $archiveId)
	{
		$previous = url()->previous();
		
		$routeLink = route ( self::ROUTE_ID . '.show', ['profile'=> $profileId, 'archive'=>$archiveId]);

		return (strtok($previous,'?') == $routeLink) ? $previous : $routeLink;
	}

	/**
	 * 이전 링크 주소.
	 * 바로 이전 주소를 가지고 셋팅을 하는데, '새로고침' 을 하는 경우도 있기 때문에, 세션에 넣어두고 활용한다.
	 * 뭔가 동작이 원하는 느낌이 아니다...살펴봐야 할 듯...
	 * @param Request $rqeust
	 * @return string
	 */
	protected function getPreviousLink(Request $request)
	{
		$session_previousName = 'devscrap-previousList';
		// 바로 이전 주소가 list 형태의 index 경로라면, flash session 에 저장.
		$request->session()->reflash();
		$request->session()->keep([$session_previousName]);
		
		$previous = url()->previous();
		$previous_identifier = strtok($previous,'?');
		
		// 해당 패턴과 일치하거나 index 의 주소인 경우에 previous 세션에 저장
		if($previous_identifier == route ( self::ROUTE_ID . '.index', ['profile'=>1])){
			$request->session()->flash($session_previousName, $previous);
		}
		
		//session 에 해당 값이 있으면 세션 값 사용. 없으면 목록 주소로 대체.
		return ($request->session()->get($session_previousName,'') != '') ?
		$request->session()->get($session_previousName,'') : route ( self::ROUTE_ID . '.index', ['profile'=>1]);
	}
}