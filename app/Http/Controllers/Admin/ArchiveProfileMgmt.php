<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ArchiveBoard;
use App\Models\Archive;
use App\Models\Profile;

class ArchiveProfileMgmt extends Controller
{
	protected const VIEW_PATH = 'admin.archive_profile';
	protected const ROUTE_ID = 'admin.archiveProfile';
    
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

        $userId = Auth::id();

        $masterList = Profile::select(['id','name','text','root_board_id','is_default','created_at'])
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
        $item = new Profile;
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
    public function edit($id)
    {
        $userId = Auth::id();

        $item = Profile::where ( 'id', $id )->firstOrFail ();

        $ArchiveProfileList = Profile::select(['id','name','is_default','created_at'])
        ->where('user_id',$userId)
        ->orderBy('created_at','asc')->get();

        $dataSet = $this->createViewData ();
        $dataSet ['item'] = $item;
        $dataSet ['ArchiveProfileList'] = $ArchiveProfileList;
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
    	]);
        
        $name = $request->input ( 'name' );
        $text = $request->input ( 'text' , '');
        $is_default = (bool)$request->input( 'is_default' , false);
    	
        $item = new Profile;
        $item->name = $name;
        $item->text = $text;
        if($is_default){
            Profile::where('is_default',1)->update(['is_default'=>0]);
            $item->is_default = true;
        }
        $item->user_id = Auth::id();
        $item->save();

        // 최상단 카테고리를 생성해야함.
        $archiveBoard = ArchiveBoard::create([
            'profile_id' => $item->id,
            'parent_id'=> '0',
            'name' => $item->name,
            'depth' => '1'
        ]);

        $item->root_board_id = $archiveBoard->id;
        $item->save();

    	return redirect ()->route ( self::ROUTE_ID . '.edit', ['id'=>$item->id] )->with('message', '카테고리를 생성하였습니다.');
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
        
        $is_default = (bool)$request->input( 'is_default' , false);

    	// 있는 값인지 id 체크
        $item = Profile::findOrFail ( $id );
    	
    	// saving
        $item->name = $request->input ( 'name' );
        $item->text = $request->input ( 'text' );
        if($item->is_default == false && $is_default == true){
            //... 새롭게 기본 아카이브로 지정된 경우. 다른 아카이브는 is_default 값을 false 로 변경한다.
            Profile::where('is_default',1)->update(['is_default'=>0]);
        }
        $item->is_default = $is_default;
    	$item->save ();
	
    	return redirect ()->route ( self::ROUTE_ID . '.edit', ['id'=>$id] )->with ('message', '변경이 완료되었습니다.' );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // 확인하고 없으면 Fail
        $item = Profile::findOrFail($id);

        // 해당되는 게시물이 있을 때에는, 옮길 아카이브를 선택하는 화면으로 이동시킨다. 
        // 아카이브에 해당되는 게시물은 다른 아카이브로 이동시킨다. 
        // 해당되는 게시물이 없을 때에 삭제를 진행한다.
        // 해당되는 카테고리 는 삭제하도록 한다.
        $willMoveProfileId = $request->input ( 'will_move_profile' );
        $willMoveBoardId = Profile::where('id',$willMoveProfileId)->value('root_board_id');

        $applicableBoardIds = ArchiveBoard::where('profile_id',$id)->pluck('id');

        if(ArchiveBoard::select(['id'])->where('profile_id',$id)->exists()){
            // 해당하는 Archive 를 이동시키기. (profile_id 와 board_id 를 변경.)
            Archive::whereIn('board_id',$applicableBoardIds)->update([
                'board_id'=>$willMoveBoardId,
                'profile_id'=>$willMoveProfileId
            ]);

            // 해당하는 Board 는 삭제하기.
            ArchiveBoard::where('profile_id',$id)->delete();
        }

        // 삭제 실행
        $item->delete();
        
        return redirect()->route(self::ROUTE_ID.'.index')->with('message','삭제를 완료하였습니다.');
    }

    /**
     * 
     */
    public function updateSort(Request $request){
        $listData = $request->input('listData', array());

        foreach($listData as $i => $item){
            
            $profile = Profile::findOrFail ( $item['profileId'] );
            $profile->index = $item['index'];
            $profile->save();
        }

        $dataSet = array();
        $dataSet['success'] = '변경 완료되었습니다.';

        $request->session()->flash('message', '변경 완료되었습니다.');

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
}
