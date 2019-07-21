<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\ArchiveBoard;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller {
	protected const VIEW_PATH = 'app.archive';
	protected const ROUTE_ID = 'archives';
	protected $ArchiveProfile;
	protected const CATEGORY_SEPERATE_CHAR = '―';

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
	public function index(Request $request) {
		$this->getArchiveProfile($request);

	    $boardId = $request->input('board', $this->ArchiveProfile->root_board_id);

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
	    $masterList = Archive::select(['sa_archives.id', 'sa_archives.title','sa_archives.summary_var','sa_archives.reference','sa_archives.board_id','sa_archives.created_at',
	        'sa_archives.updated_at',DB::raw('(select name from sa_boards as s where s.id = sa_archives.board_id) as category_name')])
	      ->join(DB::raw("(
            SELECT node.board_id, parent.board_id as parent_id
            FROM sa_board_tree AS node ,
				sa_board_tree AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
            ) as cate"),'cate.board_id','=','sa_archives.board_id')
            ->where ( 'cate.parent_id',$boardId )->orderBy ( 'created_at', 'desc' )->paginate(20);
        //$queries = DB::getQueryLog();
        //print_r($queries);
		
		$archiveBoard = ArchiveBoard::select(['name', 'comment'])->where ( 'id', $boardId )->firstOrFail ();

        //$categoryName = ArchiveBoard::select('name')->where ( 'id', $boardId )->firstOrFail ()->name;
		
		// dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['posts'] = $masterList;
		$dataSet ['archiveBoard'] = $archiveBoard;
        //$dataSet ['categoryPath'] = $this->getCategoryPath($boardId);
        //$dataSet ['subcategories'] = $this->getSubCategories($boardId);
		$dataSet ['parameters']['boardId'] = $boardId;
        //$dataSet ['nav'] = $this->getDevMenus($boardId);
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
			$masterList = Archive::select('sa_archives.*',DB::raw('(select name from sa_boards as s where s.id = sa_archives.board_id) as category_name'))
			->join(DB::raw("(
				SELECT node.board_id, parent.board_id as parent_id
				FROM sa_board_tree AS node ,
					sa_board_tree AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
				) as cate"),'cate.board_id','=','sa_archives.board_id')
			->search($word)->where ( 'cate.parent_id', $boardId)->orderBy('sa_archives.created_at','desc')->paginate(30);

    	    // dataSet 생성
    	    $dataSet = $this->createViewData ();
    	    $dataSet ['articles'] = $masterList;
    	    $dataSet ['parameters']['boardId'] = $boardId;
    	    $dataSet ['parameters']['q'] = $word;
    	    $dataSet ['categoryPath'] = $this->getCategoryPath($boardId);
    	    $dataSet ['subcategories'] = $this->getSubCategories($boardId);
    	    //$dataSet ['nav'] = $this->getDevMenus($boardId);
    	    return view ( self::VIEW_PATH . '.search', $dataSet );
	        
	    }
	}
	
	/**
	 * 아카이브 화면에서 nav 메뉴를 가져오는 Ajax 부분.
	 */
	public function ajaxGetBoardList(Request $request){
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

	/**
	 * 글 본문 읽기
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id) {
		$this->getArchiveProfile($request);
	    $article = Archive::where ( 'id', $id )->firstOrFail ();
	    $boardId = $article->board_id;
		
	    // create dataSet
	    $dataSet = $this->createViewData ();
	    $dataSet ['article'] = $article;
	    $dataSet ['previousList'] = $this->getPreviousLink($request);
	    $dataSet ['parameters']['boardId'] = $boardId;
	    $dataSet ['categoryPath'] = $this->getCategoryPath($boardId);
	    //$dataSet ['nav'] = $this->getDevMenus($boardId);
	    return view ( self::VIEW_PATH . '.show', $dataSet );
	}
	
	
	/**
	 * 글 작성
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request) {
		$this->getArchiveProfile($request);
	    $boardId = $request->input('board', $this->ArchiveProfile->root_board_id);

		$article = new Archive();

	    // dataSet 생성
	    $dataSet = $this->createViewData ();
	    $dataSet ['article'] = $article;
	    $dataSet ['categories'] = $this->getSubCategories($boardId);
	    $dataSet ['parameters']['boardId'] = $boardId;
	    //$dataSet ['nav'] = $this->getDevMenus($boardId);
	    return view ( self::VIEW_PATH . '.create', $dataSet );
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id) {
		$this->getArchiveProfile($request);
	    $request->session()->reflash();
	    $request->session()->keep(['devscrap-previousList']);
		
		
	    //
	    $article = Archive::where ( 'id', $id )->firstOrFail ();
	    $boardId = $article->board_id;

	    // create dataSet
	    $dataSet = $this->createViewData ();
	    $dataSet ['article'] = $article;
	    $dataSet ['categories'] = $this->getCategories($boardId);
	    $dataSet ['parameters']['boardId'] = $boardId;
	    $dataSet ['categoryPath'] = $this->getCategoryPath($boardId);
	    //$dataSet ['nav'] = $this->getDevMenus($boardId);
	    return view ( self::VIEW_PATH . '.edit', $dataSet );
	    
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$this->getArchiveProfile($request);
		//
		$title = $request->input ( 'title' );
		$content = $request->input ( 'content' );
		$board_id = $request->input ( 'board_id' , '1');
		$reference = $request->input ('reference');


		$dataSet = array ();
		$dataSet ['title'] = $title;
		$dataSet ['content'] = $content;
		$dataSet ['board_id'] = $board_id;
		$dataSet ['reference'] = $reference;

		
		$article = Archive::create ( $dataSet );
		$article->save ();
		
		$this->updateCategoryCountingAll();
		
		// result processing
		return redirect ()->route ( self::ROUTE_ID . '.index' )->withSuccess ( 'New Post Successfully Created.' );
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		$this->getArchiveProfile($request);
		$request->session()->reflash();
		$request->session()->keep(['devscrap-previousList']);
		
		// 있는 값인지 id 체크
		$article = Archive::findOrFail ( $id );
		
		$title = $request->input ( 'title' );
		$content = $request->input ( 'content' );
		$board_id = $request->input ( 'board_id' , '1');
		$reference = $request->input ('reference');
		
		// saving
		$article->title = $title;
		$article->content = $content;
		$article->board_id = $board_id;
		$article->reference = $reference;
		$article->category = $request->input ('category');
		$article->save ();
		
		$this->updateCategoryCountingAll();
		// after processing
		if ($request->action === 'continue') {
			return redirect ()->back ()->withSuccess ( 'Post saved.' );
		}
		return redirect ()->route ( self::ROUTE_ID . '.show', $id)->withSuccess ( 'Post saved.' );
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
		->route(self::ROUTE_ID.'.index')
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
	    $dataSet ['parameters'] = ['profile'=>$this->ArchiveProfile->id];
	    //$dataSet ['nav'] = $this->getDevMenus();
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
	 * 개발 아카이브 의 목록 조회
	 * 최상위 메뉴를 조회함.
	 */
	protected function getDevMenus($boardId='1'){
	    $list = $this->getCategoryPath($boardId);
	    $id = 1;
	    if(count($list)>=1){
	        $id = $list[0]->id;
	    }
	    return ArchiveBoard::where('parent_id',$id)->get();
	}
	
	/**
	 * 이전 링크 주소.
	 * 바로 이전 주소를 가지고 셋팅을 하는데, '새로고침' 을 하는 경우도 있기 때문에, 세션에 넣어두고 활용한다.
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
		if($previous_identifier == route ( self::ROUTE_ID . '.index')){
			$request->session()->flash($session_previousName, $previous);
		}
		
		//session 에 해당 값이 있으면 세션 값 사용. 없으면 목록 주소로 대체.
		return ($request->session()->get($session_previousName,'') != '') ?
		$request->session()->get($session_previousName,'') : route ( self::ROUTE_ID . '.index');
	}
	
	
	public function getCategoryPath($boardId = 0)
	{
	    $list = DB::select('select parent.board_id as id, cate.name as name
            from sa_board_tree as node,
			sa_board_tree as parent,
                sa_boards as cate
            where node.lft between parent.lft and parent.rgt
              and parent.board_id = cate.id
              and node.board_id = ?
            order by parent.lft',[$boardId]);
	    //Log::debug('ArchiveBoard::getCategoryPath');
	    
	    return $list;
	}
	
	/**
	 * boardId 를 기준으로 상위 노드를 탐색하고, 해당되는 분류의 카테고리들을 출력한다.
	 * @param number $boardId
	 * @return
	 */
	public function getCategories($boardId=1)
	{
	    $list = $this->getCategoryPath($boardId);
	    $id = 1;
	    if(count($list)>=1){
	        $id = $list[0]->id;
	    }
	    return $this->getSubCategories($id);
	}
	
	/**
	 * 하위 카테고리 Tree 를 조회.
	 * 최상위 depth 는 0 이라고 볼 때, 이 메서드는 depth 를 최소 1 이상 에서 사용하게 됨.
	 * @param number $boardId
	 * @param number $depth
	 */
	public function getSubCategories($boardId = 0, $depth = 1)
	{
	    $list = DB::select("select concat( repeat(?, count(parent.board_id) - 1 - ?), cate.name) as name,
                node.board_id as id,
                cate.count as count
            from sa_board_tree as node,
				sa_board_tree as parent,
				sa_board_tree as sub_parent,
                sa_boards as cate
            where node.lft between parent.lft and parent.rgt
                and node.lft between sub_parent.lft and sub_parent.rgt
                and sub_parent.board_id = ?
                and cate.id = node.board_id
            group by node.board_id
            order by node.lft",[self::CATEGORY_SEPERATE_CHAR, $depth, $boardId]);
	    return $list;
	}
	
	/**
	 * 카테고리의 게시글 카운팅을 전부 새로고침
	 */
	private function updateCategoryCountingAll()
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
	 * 카테고리의 게시글 카운팅을 하나씩 변경하려고...
	 */
	private function updateCategoryCounting(){}
	    
}
