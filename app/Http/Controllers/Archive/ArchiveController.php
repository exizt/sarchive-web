<?php

namespace App\Http\Controllers\Archive;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\SArchive\SAArchive;
use App\Models\SArchive\SAFolder;
use App\Models\SArchive\SADocument;
use App\Models\ArchiveBookmark;


class ArchiveController extends BaseController {
    protected const VIEW_PATH = 'app.archive';
    protected const ROUTE_ID = 'archives';


    /**
     * 현재 보고 있는 Archive 개체
     */
    protected $archive = null;


    /**
     * 생성자
     */
    public function __construct() {
        $this->middleware ( 'auth' );
    }


    /**
     * 첫 페이지
     */
    public function first(Request $request, $archiveId){
        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);

        // data 생성
        $data = $this->createViewData ();
        $data['archive'] = $archive;
        return view ( self::VIEW_PATH . '.first', $data );
    }



        /**
     * Archive 카테고리 목록을 출력한다.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $userId = Auth::id();

        $masterList = SAArchive::select(['id','name','comments','is_default','created_at'])
        ->where('user_id',$userId)
        ->orderBy('index','asc')
        ->orderBy('id','asc')
        ->paginate(20);


        $dataSet = $this->createViewData ();
        $dataSet['masterList'] = $masterList;
        
        return view ( self::VIEW_PATH . '.index', $dataSet );
    }
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $item = new SAArchive;
        $dataSet = $this->createViewData ();
        $dataSet ['item'] = $item;
        return view ( self::VIEW_PATH . '.create', $dataSet );
    }
    
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $userId = Auth::id();

        $item = SAArchive::where ( 'id', $id )->where('user_id',$userId)->firstOrFail ();

        // 삭제시 이동을 선택하기 위한 목록
        $archiveList = SAArchive::select(['id','name','is_default','created_at'])
        ->where('user_id',$userId)
        ->orderBy('created_at','asc')->get();

        $dataSet = $this->createViewData ();
        $dataSet ['item'] = $item;

        // 아카이브 삭제 때 필요한 다른 아카이브 목록
        $dataSet ['archiveList'] = $archiveList;
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
        // 유효성 체크
    	$request->validate([
    	    'name' => 'required|min:2',
    	]);
        
        // 파라미터 
        $name = $request->input ( 'name' );
        $comments = $request->input ( 'comments' , '');
        $is_default = (bool)$request->input( 'is_default' , false);
        $userId = Auth::id();
        
        // 처리 진행
        $item = new SAArchive;
        $item->name = $name;
        $item->comments = $comments;
        if($is_default){
            // 해당 아카이브 디폴트 지정시 다른 아카이브들의 is_default를 false 로 변경
            SAArchive::where('is_default',1)
            ->where('user_id', $userId)
            ->update(['is_default'=>0]);
            // is_default 값 지정
            $item->is_default = true;
        }
        $item->user_id = $userId;
        $item->save();

        return redirect ()->route ( self::ROUTE_ID . '.edit', $item->id )
           ->with('message', '카테고리를 생성하였습니다.');
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
        // 유효성 체크
        $rules = [
            'name' => 'required|min:2'
        ];
        $this->validate($request, $rules);
        
        // 파라미터
        $name = $request->input ( 'name' );
        $comments = $request->input ( 'comments' , '');
        $is_default = (bool)$request->input( 'is_default' , false);
        $userId = Auth::id();

    	// 있는 값인지 id 체크
        $item = SAArchive::findOrFail ( $id );
    	
    	// saving
        $item->name = $name;
        $item->comments = $comments;
        if($item->is_default == false && $is_default == true){
            //... 새롭게 기본 아카이브로 지정된 경우. 다른 아카이브는 is_default 값을 false 로 변경한다.
            SAArchive::where('is_default',1)
            ->where('user_id', $userId)
            ->update(['is_default'=>0]);
        }
        $item->is_default = $is_default;
    	$item->save ();
	
        return redirect ()->route ( self::ROUTE_ID . '.edit', $id )
        ->with ('message', '변경이 완료되었습니다.' );
    }

    /**
     * 아카이브 삭제
     * 
     * @todo 분류 이동
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // 확인하고 없으면 Fail
        $archive = SAArchive::findOrFail($id);

        // 파라미터
        $willMoveArchiveId = $request->input ( 'will_move' );

        // 권한 체크 필요.
        // 해당 아카이브에 대한 권한이 있는지 확인.
        // 1. 해당하는 문서를 삭제할지 옮길지 분기점.

        // 1-1. 그냥 삭제. (문서, 폴더, 아카이브를 삭제 처리함)

        // 1-2. 이동.
        // 1-2-1. 문서를 이동함.
        SADocument::where('archive_id',$id)->update([
            'archive_id'=>$willMoveArchiveId
        ]);

        // 1-2-2. 폴더 이동.
        SAFolder::where('archive_id',$id)->update([
            'archive_id'=>$willMoveArchiveId
        ]);

        // 1-2-3. 분류 를 이동해줘야 함. 기존에 없는 경우에만 이동.
        

        // 삭제 실행
        $archive->delete();
        
        return redirect()->route(self::ROUTE_ID.'.index')->with('message','삭제를 완료하였습니다.');
    }

    /**
     * 아카이브의 순서 변경 처리
     * 
     */
    public function updateSort(Request $request){
        $dataList = $request->input('dataList', array());

        foreach($dataList as $i => $item){
            
            $archive = SAArchive::findOrFail ( $item['id'] );
            $archive->index = $item['index'];
            $archive->save();
        }

        $request->session()->flash('message', '변경 완료되었습니다.');

        // 결과값
        $dataSet = array();
        $dataSet['success'] = '변경 완료되었습니다.';
        return response()->json($dataSet);
    }



    /**
     * id를 통한 archive 조회 및 권한 체크
     * @param int $id 아카이브 Id
     */
    private function retrieveAuthArchive($id){
        
        $this->archive = SAArchive::select(['id','name','route'])
            ->where ( [['user_id', Auth::id() ],['id',$id]])
            ->firstOrFail ();
        return $this->archive;
    }


    /**
     * 
     * @return string[]
     */
    protected function createViewData() {
        $data = array ();
        $data ['ROUTE_ID'] = self::ROUTE_ID;
        $data ['VIEW_PATH'] = self::VIEW_PATH;
        $data ['parameters'] = array();
        $data ['paginationParams'] = array();

        $layoutParams = array();
        $bodyParams = array();
        if(isset($this->archive) && $this->archive != null){
            $layoutParams['archiveId'] = $this->archive->id;
            $layoutParams['archiveName'] = $this->archive->name;
            $bodyParams['archive'] = $this->archive->id;
        }
        $data ['layoutParams'] = $layoutParams;
        $data ['bodyParams'] = $bodyParams;
        return $data;
    }


    /**
     * 이전 링크 주소.
     * 바로 이전 주소를 가지고 셋팅을 하는데, '새로고침' 을 하는 경우도 있기 때문에, 세션에 넣어두고 활용한다.
     * 뭔가 동작이 원하는 느낌이 아니다...살펴봐야 할 듯...
     * @param Request $rqeust
     * @return string
     * @deprecated
     */
    protected function makePreviousListLink(Request $request, $profileId)
    {
        $previous = url()->previous();
        
        $routeLink = route ( self::ROUTE_ID . '.index', ['profile'=> $profileId]);

        return (strtok($previous,'?') == $routeLink) ? $previous : $routeLink;
    }


    /**
     * '취소 링크' 생성.
     * @deprecated
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
     * @deprecated
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