<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SAArchive;
use App\Models\SAFolder;

class ArchiveBoardMgmt extends Controller
{
	protected const VIEW_PATH = 'admin.archive_board';
	protected const ROUTE_ID = 'admin.archiveBoard';
	protected const CATEGORY_ROOT_DEV = 1;
	protected const CATEGORY_ROOT_GENERAL = 29;
	protected const UNIT_CD_DEV = 'D';
    protected const UNIT_CD_GENERAL = 'G';
    protected const CATEGORY_SEPERATE_CHAR = '―';
    protected $ArchiveProfile;
    
    /**
     * 생성자
     */
	public function __construct() {
		$this->middleware ( 'auth' );
	}
	
    /**
     * Archive 카테고리 목록을 출력한다.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        // 아카이브 프로필을 조회
        $userId = Auth::id();
        $archiveProfiles = SAArchive::select(['id','name','text','root_board_id','is_default','created_at'])
        ->where('user_id',$userId)
        ->orderBy('created_at','asc')->get();
        
        $dataSet = $this->createViewData ();
        $dataSet['ArchiveProfileList'] = $archiveProfiles;
        return view ( self::VIEW_PATH . '.index', $dataSet );
    }

    /**
     * Archive 카테고리 목록을 출력한다.
     * @return \Illuminate\Http\Response
     */
    public function index_ajax(Request $request)
    {
        $this->getArchiveProfile($request);
        
        /*
        $dataSet ['boards'] = DB::select("select cate.name as name,
                node.board_id as id,
                cate.count as count,
                cate.parent_id as parent
            from sa_board_tree as node,
				sa_board_tree as parent,
				sa_board_tree as sub_parent,
                sa_boards as cate
            where node.lft between parent.lft and parent.rgt
                and node.lft between sub_parent.lft and sub_parent.rgt
                and sub_parent.board_id = ?
                and cate.id = node.board_id
            group by node.board_id
            order by node.lft",[$this->ArchiveProfile->root_board_id]);
        */
        $dataSet['boards'] = SAFolder::select(['id','profile_id','name','parent_id as parent'])
        ->where('profile_id',$this->ArchiveProfile->id)
        ->orderBy('index','asc')->get();

        return response()->json($dataSet);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateList(Request $request)
    {
        
        //print_r($_POST);
        $profileId = $request->input('profileId');
        $listData = $request->input('listData');
        $deletedList = $request->input('deletedList', array());

        $changedIdList = array();
        $depthPathList = array();//depth 와 path 를 다루기 위한 배열

        // 트리에서 변경된 내용을 적용함
        // 새로운 항목에서는 id 가 'j1_1' 과 같은 형태이므로, 이 경우에는 insert_id 로 변경해주는 로직 추가함.
        foreach($listData as $i => $item){
            //$id = $item['id'];
            //$text = $item['text'];
            //$parent = $item['parent'];

            // parent_id 값이 j 로 시작되는 경우, changedIdList 에 있는 값으로 변경.
            if($item['parent'][0] == 'j'){
                if(array_key_exists($item['parent'],$changedIdList)){
                    $item['parent'] = $changedIdList[$item['parent']];
                }
            }

            // depth 를 생성하기 위한 구문.
            {
                if($item['parent']=='0'||$item['parent']==0){
                    ///... 최상위 루트의 경우
                    $item['depth'] = 1;
                    $item['path'][] = ['text'=>$item['text'],'id'=>$item['id']];
                
                } else {
                    ///... 루트 외의 경우
                    
                    $t_parent = $item['parent'];
                    $item['depth'] = $depthPathList[$t_parent]['depth'] + 1;
                    //$item['path'] = $depthPathList[$t_parent]['path'].' > '.$item['text'];
                    $item['path'] = $depthPathList[$t_parent]['path'];
                    $item['path'][] = ['text'=>$item['text'],'id'=>$item['id']];
                }
                /*
                $depthPathList[$item['id']] = [
                    'depth' => $item['depth'],
                    'path' => $item['path']
                ];
                */
            }


            if($item['id'][0] == 'j'){
                $archiveBoard = SAFolder::create([
                    'parent_id'=>$item['parent'],
                    'index' => $i,
                    'name' => $item['text'],
                    'depth' => $item['depth'],
                    'path' => json_encode($item['path']),
                    'profile_id' => $profileId
                ]);

                // 신규 생성된 id 를 changedIdList 에 추가함.
                $changedIdList[$item['id']] = $archiveBoard->id;
                $depthPathList[$archiveBoard->id] = [
                    'depth' => $item['depth'],
                    'path' => $item['path']
                ];
            } else {
                $archiveBoard = SAFolder::updateOrInsert(['id'=>$item['id']],
                    [
                        'parent_id'=>$item['parent'],
                        'index' => $i,
                        'name' => $item['text'],
                        'depth' => $item['depth'],
                        'path' => json_encode($item['path']),
                        'profile_id' => $profileId
                    ]);
                    $depthPathList[$item['id']] = [
                        'depth' => $item['depth'],
                        'path' => $item['path']
                    ];
            }

            //print_r($sql);
        }

        // 삭제된 카테고리 부분을 처리하는 루틴.
        if(is_array($deletedList)){
            foreach($deletedList as $deletedId){
                if($deletedId[0]=='j'){
                    continue;
                }
                $archiveBoard = SAFolder::find($deletedId);
                $archiveBoard->delete();
            }
        }

        // tree table 작성. (프로시저 호출)
        $this->updateListTreeTable();

        $dataSet = array();
        $dataSet['success'] = '변경 완료되었습니다.';


        // 게시글이 존재하는 게시판인 경우 삭제를 가능하게 할지.. 게시물의 board_id 를 이동만 시킬지...
        // select count(*) from sa_archives where board_id in (39,70)

        return response()->json($dataSet);
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
			//$this->ArchiveProfile = SAArchive::select(['name','root_board_id','route'])->where ( [['user_id', $userId ],['route',$ArchiveRouteId]])->firstOrFail ();
	
			// profileId 를 이용한 접근
			$this->ArchiveProfile = SAArchive::select(['id','name','root_board_id','route'])
				->where ( [['user_id', $userId ],['id',$profileId]])
				->firstOrFail ();
			
		} else {
			$this->ArchiveProfile = SAArchive::select(['id','name','root_board_id','route'])
			->where ( [['user_id', $userId ],['is_default','1']])
			->firstOrFail ();
		}
	}

    /**
     * 프로시저 실행
     */
    private function updateListTreeTable(){
        DB::statement('call procedure_insert_menus()');
    }
}