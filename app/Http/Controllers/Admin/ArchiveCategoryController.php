<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ArchiveCategory;

class ArchiveCategoryController extends Controller
{
	protected const VIEW_PATH = 'admin.archive_category';
	protected const ROUTE_ID = 'admin.archiveCategory';
	protected const CATEGORY_ROOT_DEV = 1;
	protected const CATEGORY_ROOT_GENERAL = 29;
	protected const UNIT_CD_DEV = 'D';
	protected const UNIT_CD_GENERAL = 'G';
	public function __construct() {
		$this->middleware ( 'auth' );
	}
	
    /**
     * Archive 카테고리 목록을 출력한다.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $unitCd = $request->input('unit',self::UNIT_CD_DEV);
        $categoryId = (!$request->has('category')) ? $this->getCategoryRootId($unitCd) : $categoryId = $request->input('category');
        /*
        if(!$request->has('category')){
            $categoryId = $this->getCategoryRootId($unitCd);
        } else {
            $categoryId = $request->input('category');
        }
        */
        
        $dataSet = $this->createViewData ();
        $dataSet ['categories'] = $this->getCategories($categoryId);
        $dataSet ['parameters']['unit'] = $unitCd;
        return view ( self::VIEW_PATH . '.index', $dataSet );
    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $unitCd = $request->input('unit',self::UNIT_CD_DEV);
        $categoryRootId = $this->getCategoryRootId($unitCd);
        
        $item = new ArchiveCategory;
        $dataSet = $this->createViewData ();
        $dataSet ['item'] = $item;
        $dataSet ['categories'] = $this->getCategories($categoryRootId);
        return view ( self::VIEW_PATH . '.create', $dataSet );
    }
    
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = ArchiveCategory::where ( 'id', $id )->firstOrFail ();
        $dataSet = $this->createViewData ();
        $dataSet ['item'] = $item;
        if($item->parent_id != 0){
            $dataSet ['categories'] = $this->getCategories($item->id);
        }
        return view ( self::VIEW_PATH . '.edit', $dataSet );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$request->validate([
    	    'name' => 'required|min:2',
    	    'parent_id' => 'required'
    	]);
    	//$validator = Validator::make($request->all(), $rules)->validate();

    	$parentId = $request->input ( 'parent_id','0' );
    	
    	
    	$dataSet = array ();
        $dataSet ['name'] = $request->input ( 'name' );
        $dataSet ['comment'] = $request->input ( 'comment' );
    	$dataSet ['parent_id'] = $parentId;
    	
    	$item = ArchiveCategory::create ( $dataSet );
    	$item->save ();
    	
    	$this->updateCategoryTree();
    	
    	
    	
    	$list = $this->getCategoryPath($parentId);
    	if(count($list)>=1){
    	    $categoryId = $list[0]->id;
    	}
    	if($categoryId==1){
    	    $unit = 'D';
    	} else {
    	    $unit = 'G';
    	}
    	
    	return redirect ()->route ( self::ROUTE_ID . '.index', ['unit'=>$unit] )->with('message', '카테고리를 생성하였습니다.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|min:2'
        ];
        $this->validate($request, $rules);
        
    	// 있는 값인지 id 체크
        $item = ArchiveCategory::findOrFail ( $id );
    	
    	// saving
        $item->name = $request->input ( 'name' );
        $item->comment = $request->input ( 'comment' );
    	
    	// parent_id 값이 넘어온 경우는 변경함.
    	if($request->has('parent_id')){
    	    $item->parent_id = $request->input ( 'parent_id' );
    	}
    	$item->save ();
    	$this->updateCategoryTree();
    	
    	$list = $this->getCategoryPath($item->parent_id);
    	if(count($list)>=1){
    	    $categoryId = $list[0]->id;
    	}
    	if($categoryId==1){
    	    $unit = 'D';
    	} else {
    	    $unit = 'G';
    	}
    	
    	return redirect ()->route ( self::ROUTE_ID . '.edit', ['id'=>$id, 'unit'=>$unit])->with ('message', '변경이 완료되었습니다.' );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = ArchiveCategory::findOrFail($id);
        
        // 해당 카테고리의 게시글이 1개라도 있을 시에는 삭제하지 않도록 한다.
        if($item->count>=1){
            return redirect()->route(self::ROUTE_ID.'.edit',$id)->withErrors(array('message'=>'게시글이 하나라도 있는 카테고리는 삭제할 수 없습니다.'));            
        } else {
            $item->delete();
            $this->updateCategoryTree();
            return redirect()
            ->route(self::ROUTE_ID.'.index')
            ->with('message','카테고리를 삭제하였습니다.');
        }
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
     * 
     * @param number $categoryId
     * @return 
     */
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
     * 
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
    public function getSubCategories($categoryId = 1)
    {
        $list = DB::select("select concat( repeat('#', count(parent.category_id) - 1), cate.name) as name,
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
            order by node.lft",[$categoryId]);
        return $list;
    }
    
    private function updateCategoryTree(){
        DB::statement('call procedure_insert_menus()');
    }
    
    /**
     * Unit Code 를 기준으로 카테고리 Root Id 를 가져오는 메서드
     * @param $unitCd
     * @return string
     */
    private function getCategoryRootId($unitCd=self::UNIT_CD_DEV){
        if($unitCd==self::UNIT_CD_DEV){
            $categoryId = self::CATEGORY_ROOT_DEV;
        } else {
            $categoryId = self::CATEGORY_ROOT_GENERAL;
        }
        return $categoryId;
    }
}
