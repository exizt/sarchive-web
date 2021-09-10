<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SArchive\SAArchive;
use App\Models\SArchive\SAFolder;

class ArchiveFolderMgmt extends Controller
{
	protected const VIEW_PATH = 'admin.folder-control';

    /**
     * 생성자
     */
	public function __construct() {
		$this->middleware ( 'auth' );
	}

    /**
     * 폴더 목록 조회
     */
    public function index(Request $request)
    {
        // 아카이브 프로필을 조회
        $userId = Auth::id();
        $archiveList = SAArchive::select(['id','name','comments','is_default','created_at'])
        ->where('user_id',$userId)
        ->orderBy('created_at','asc')->get();

        $dataSet = $this->createViewData ();
        $dataSet['archiveList'] = $archiveList;
        return view ( self::VIEW_PATH . '.index', $dataSet );
    }

    /**
     * 폴더 목록 Ajax
     */
    public function index_ajax(Request $request)
    {
        // 파라미터
        $archiveId = $request->input('archive_id');
        $userId = Auth::id();

        // Archive 정보 조회 및 권한 체크
        $archive = SAArchive::select(['id','name'])
            ->where ( [['user_id', $userId ],['id',$archiveId]])
            ->firstOrFail ();

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
            order by node.lft",[$this->archive->root_board_id]);
        */
        // 폴더 목록 조회
        $dataSet['folders'] = SAFolder::select(['id','archive_id','name','parent_id as parent', 'depth'])
        ->where('archive_id',$archiveId)
        ->orderBy('index','asc')->get();

        return response()->json($dataSet);
    }

    /**
     * 폴더 목록 > 저장 프로세스
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateList(Request $request)
    {
        //print_r($_POST);
        $archiveId = $request->input('archive_id');
        $listData = $request->input('list_data');
        $deletedList = $request->input('deleted_list', array());

        //신규 노드들의 목록. (key='임시id(j로시작)', value=새로사용될id)
        $changedIdList = array();

        //depth 와 path 를 다루기 위한 배열
        $depthPathList = array();

        // 트리에서 변경된 내용을 적용함
        // 새로운 항목에서는 id 가 'j1_1' 과 같은 형태이므로, 이 경우에는 insert_id 로 변경해주는 로직 추가함.
        foreach($listData as $i => $item){
            //$id = $item['id'];
            //$text = $item['text'];
            //$parent = $item['parent'];

            // parent_id 값이 j 로 시작되는 경우. (즉, 부모값이 새로운 노드인 경우)
            if($item['parent'][0] == 'j'){
                // changedIdList 에 있는 키 값으로 parentId 를 교체한다.
                // array_key_exists($item['parent'],$changedIdList) 는 만약을 대비한 체크이며,
                // 체크를 하지 않아도 로직 상 이상이 없어야 함.
                if(array_key_exists($item['parent'],$changedIdList)){
                    $item['parent'] = $changedIdList[$item['parent']];
                }
            }

            // depth 를 생성하기 위한 구문.
            {
                if($item['parent']=='0'||$item['parent']==0||$item['parent']=='#'){
                    ///... 최상위 루트의 경우
                    $item['parent'] = '0';
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


            /**
             * insert 또는 update 하는 부분
             */
            $folderId = null;
            if($item['id'][0] == 'j'){
                /**
                 * 신규 노드의 경우.
                 *
                 * insert 구문을 실행한다 (laravel의 create메서드)
                 */
                $archiveFolder = SAFolder::create([
                    'parent_id'=>$item['parent'],
                    'index' => $i,
                    'name' => $item['text'],
                    'depth' => $item['depth'],
                    //'path' => json_encode($item['path']),
                    'archive_id' => $archiveId
                ]);

                // 신규 생성된 id 를 changedIdList 에 추가함.
                $folderId = $archiveFolder->id;
                $changedIdList[$item['id']] = $folderId;

            } else {
                /**
                 * 신규 노드가 아닌 경우
                 */
                // 변경된 정보를 update
                $archiveFolder = SAFolder::updateOrInsert(['id'=>$item['id']],
                [
                    'parent_id'=>$item['parent'],
                    'index' => $i,
                    'name' => $item['text'],
                    'depth' => $item['depth'],
                    //'path' => json_encode($item['path']),
                    'archive_id' => $archiveId
                ]);

                $folderId = $item['id'];

            }

            // paths 를 저장.
            $depthPathList[$folderId] = [
                'depth' => $item['depth'],
                'path' => $item['path']
            ];
            //print_r($sql);
        }

        // 삭제된 카테고리 부분을 처리하는 루틴.
        if(is_array($deletedList)){
            foreach($deletedList as $deletedId){
                if($deletedId[0]=='j'){
                    continue;
                }
                $archiveFolder = SAFolder::find($deletedId);
                $archiveFolder->delete();
            }
        }

        // tree table 작성. (프로시저 호출)
        $this->updateListTreeTable($archiveId);

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
    	//$dataSet ['ROUTE_ID'] = self::ROUTE_ID;
    	$dataSet ['VIEW_PATH'] = self::VIEW_PATH;
    	$dataSet ['parameters'] = array();
    	return $dataSet;
    }


    /**
     * 프로시저 실행
     *
     * system_path 를 생성하는 프로시저 호출.
     *
     * 예전 방식은 left, right 로 트리형태를 구현하는 방식이었었음.. 현재는 변경함.
     */
    private function updateListTreeTable($archiveId){
        //DB::statement('call procedure_insert_menus()');
        DB::statement("call proc_build_folders_systempath_in_archive('{$archiveId}')");
    }
}
