<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\ArchiveCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArchiveController extends Controller {
	protected const VIEW_PATH = 'services.archive';
	protected const CATEGORY_ROOT_DEV = 1;
	protected const CATEGORY_ROOT_GENERAL = 29;
	protected $rootCategoryId = self::CATEGORY_ROOT_DEV;
	protected $rootRouteId = 'archives';
	protected const CATEGORY_SEPERATE_CHAR = '―';

	/**
	 * 생성자
	 */
	public function __construct() {
		$this->middleware ( 'auth' );
	}

	/**
	 * 분기에 따른 처리.
	 * '개발 전용' 과 '일반 전용' 으로 구분. 향후에 더 나뉘어질 수 있음. 귀찮으니 하드코딩한다. 
	 */
	private function seperateServiceRoot(Request $request)
	{
		if($request->segment(1)=='NormalArchives'){
			$this->rootRouteId = 'NormalArchives';
			$this->rootCategoryId = self::CATEGORY_ROOT_GENERAL;
		} else {
			$this->rootRouteId = 'archives';
			$this->rootCategoryId = self::CATEGORY_ROOT_DEV;
		}
	}

	/**
	 * Display a listing of the resource.
	 * category 값이 넘어오면, 해당되는 카테고리의 목록이 출력된다.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$this->seperateServiceRoot($request);

	    $categoryId = $request->input('category', $this->rootCategoryId);

		/*
	    $posts = Archive::select('archives.*',DB::raw('(select name from archive_categories as s where s.id = category_id) as category_name'))
	      ->where ( 'unit_code', 'D')
	      ->whereRaw('category_id in 
            (
            SELECT node.category_id
            FROM archive_category_tree AS node ,
                 archive_category_tree AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
            and parent.category_id = ?
            )',[$categoryId])->orderBy ( 'created_at', 'desc' )->paginate(30);
        */
	    // 속도를 조금 더 손본 쿼리. 전에는 where in 구문을 활용했는데, 속도가 너무 느려서 join 방식으로 변경.
	    //DB::enableQueryLog();
	    $masterList = Archive::select(['archives.id', 'archives.title','archives.summary_var','archives.reference','archives.unit_code','archives.category_id','archives.created_at',
	        'archives.updated_at',DB::raw('(select name from archive_categories as s where s.id = archives.category_id) as category_name')])
	      ->join(DB::raw("(
            SELECT node.category_id, parent.category_id as parent_id
            FROM archive_category_tree AS node ,
                 archive_category_tree AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
            ) as cate"),'cate.category_id','=','archives.category_id')
            ->where ( 'cate.parent_id',$categoryId )->orderBy ( 'created_at', 'desc' )->paginate(20);
        //$queries = DB::getQueryLog();
        //print_r($queries);
		
		$archiveCategory = ArchiveCategory::select(['name', 'comment'])->where ( 'id', $categoryId )->firstOrFail ();

        //$categoryName = ArchiveCategory::select('name')->where ( 'id', $categoryId )->firstOrFail ()->name;
		
		// dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['posts'] = $masterList;
		$dataSet ['parameters']['categoryId'] = $categoryId;
		$dataSet ['archiveCategory'] = $archiveCategory;
        $dataSet ['categoryName'] = $archiveCategory->name;
        $dataSet ['categoryPath'] = $this->getCategoryPath($categoryId);
        $dataSet ['subcategories'] = $this->getSubCategories($categoryId);
        $dataSet ['nav'] = $this->getDevMenus($categoryId);
        return view ( self::VIEW_PATH . '.index', $dataSet );

	}
	
	/**
	 * 
	 * @param Request $request
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function search(Request $request) {
		$this->seperateServiceRoot($request);

	    $categoryId = $request->input('category', $this->rootCategoryId);
	    $word = $request->input('q','');
	    
	    if(mb_strlen($word) < 2){
	        echo '검색어가 너무 짧음.';
	    } else {
			$masterList = Archive::select('archives.*',DB::raw('(select name from archive_categories as s where s.id = archives.category_id) as category_name'))
			->join(DB::raw("(
				SELECT node.category_id, parent.category_id as parent_id
				FROM archive_category_tree AS node ,
					 archive_category_tree AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
				) as cate"),'cate.category_id','=','archives.category_id')
			->search($word)->where ( 'cate.parent_id', $categoryId)->paginate(30);

    	    // dataSet 생성
    	    $dataSet = $this->createViewData ();
    	    $dataSet ['articles'] = $masterList;
    	    $dataSet ['parameters']['categoryId'] = $categoryId;
    	    $dataSet ['parameters']['q'] = $word;
    	    $dataSet ['categoryPath'] = $this->getCategoryPath($categoryId);
    	    $dataSet ['subcategories'] = $this->getSubCategories($categoryId);
    	    $dataSet ['nav'] = $this->getDevMenus($categoryId);
    	    return view ( self::VIEW_PATH . '.search', $dataSet );
	        
	    }
	}
	
	/**
	 * 글 본문 읽기
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id) {
		$this->seperateServiceRoot($request);
	    $article = Archive::where ( 'id', $id )->firstOrFail ();
	    $categoryId = $article->category_id;
	    
	    // create dataSet
	    $dataSet = $this->createViewData ();
	    $dataSet ['article'] = $article;
	    $dataSet ['previousList'] = $this->getPreviousLink($request);
	    $dataSet ['parameters']['categoryId'] = $categoryId;
	    $dataSet ['categoryPath'] = $this->getCategoryPath($categoryId);
	    $dataSet ['nav'] = $this->getDevMenus($categoryId);
	    return view ( self::VIEW_PATH . '.show', $dataSet );
	}
	
	
	/**
	 * 글 작성
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request) {
		$this->seperateServiceRoot($request);
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
		$this->seperateServiceRoot($request);
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
		$this->seperateServiceRoot($request);
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
		$dataSet ['ROUTE_ID'] = $this->rootRouteId;
	    $dataSet ['VIEW_PATH'] = self::VIEW_PATH;
	    $dataSet ['parameters'] = array();
	    $dataSet ['nav'] = $this->getDevMenus();
	    return $dataSet;
	}

	/**
	 * 개발 아카이브 의 목록 조회
	 */
	protected function getDevMenus($categoryId='1'){
	    $list = $this->getCategoryPath($categoryId);
	    $id = 1;
	    if(count($list)>=1){
	        $id = $list[0]->id;
	    }
	    return ArchiveCategory::where('parent_id',$id)->get();
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
		if($previous_identifier == route ( $this->rootRouteId . '.index')){
			$request->session()->flash($session_previousName, $previous);
		}
		
		//session 에 해당 값이 있으면 세션 값 사용. 없으면 목록 주소로 대체.
		return ($request->session()->get($session_previousName,'') != '') ?
		$request->session()->get($session_previousName,'') : route ( $this->rootRouteId . '.index');
	}
	
	
	public function getCategoryPath($categoryId = 0)
	{
	    $list = DB::select('select parent.category_id as id, cate.name as name
            from archive_category_tree as node,
                archive_category_tree as parent,
                archive_categories as cate
            where node.lft between parent.lft and parent.rgt
              and parent.category_id = cate.id
              and node.category_id = ?
            order by parent.lft',[$categoryId]);
	    //Log::debug('ArchiveCategory::getCategoryPath');
	    
	    return $list;
	}
	
	/**
	 * categoryId 를 기준으로 상위 노드를 탐색하고, 해당되는 분류의 카테고리들을 출력한다.
	 * @param number $categoryId
	 * @return
	 */
	public function getCategories($categoryId=1)
	{
	    $list = $this->getCategoryPath($categoryId);
	    $id = 1;
	    if(count($list)>=1){
	        $id = $list[0]->id;
	    }
	    return $this->getSubCategories($id);
	}
	
	/**
	 * 하위 카테고리 Tree 를 조회.
	 * 최상위 depth 는 0 이라고 볼 때, 이 메서드는 depth 를 최소 1 이상 에서 사용하게 됨.
	 * @param number $categoryId
	 * @param number $depth
	 */
	public function getSubCategories($categoryId = 0, $depth = 1)
	{
	    $list = DB::select("select concat( repeat(?, count(parent.category_id) - 1 - ?), cate.name) as name,
                node.category_id as id,
                cate.count as count
            from archive_category_tree as node,
                archive_category_tree as parent,
                archive_category_tree as sub_parent,
                archive_categories as cate
            where node.lft between parent.lft and parent.rgt
                and node.lft between sub_parent.lft and sub_parent.rgt
                and sub_parent.category_id = ?
                and cate.id = node.category_id
            group by node.category_id
            order by node.lft",[self::CATEGORY_SEPERATE_CHAR, $depth, $categoryId]);
	    return $list;
	}
	
	/**
	 * 카테고리의 게시글 카운팅을 전부 새로고침
	 */
	private function updateCategoryCountingAll()
	{
	    /*
	    $affected = DB::update('update archive_categories 
            set count = (select count(id) from archives
            where archives.category_id = archive_categories.id
            group by category_id)');
        */
	    
	    // 좀 더 세밀화 된 쿼리
	    DB::update('update archive_categories
            set count = (select count(archives.id)
                from 
                archives,
                (SELECT node.category_id, parent.category_id as parent_id
                	FROM archive_category_tree AS node ,
                			 archive_category_tree AS parent
                	WHERE node.lft BETWEEN parent.lft AND parent.rgt
                ) as cate
                WHERE 
                cate.category_id = archives.category_id
                and cate.parent_id = archive_categories.id
                group by cate.parent_id)');
	    
	}
	
	/**
	 * 카테고리의 게시글 카운팅을 하나씩 변경하려고...
	 */
	private function updateCategoryCounting(){}
	    
}
