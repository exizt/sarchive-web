<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SArchive\SAArchive;


class Home extends Controller {
	protected const VIEW_PATH = 'archive_profile';
	protected const ROUTE_ID = '';

	/**
	 *
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function index(Request $request) {
		if (Auth::check()) {
			//... 로그인 상태일 때
			// 아카이브 프로필 선택 화면이 나오도록 한다.

			$userId = Auth::id();

			$masterList = SAArchive::select(['id','name','comments','is_default','created_at'])
			->where('user_id',$userId)
			->orderBy('index','asc')
			->orderBy('id','asc')
			->get();

			$dataSet = $this->createViewData ();
			$dataSet['masterList'] = $masterList;

			return view ( 'home', $dataSet );

		} else {
			//... 로그인 상태가 아닐 때.
			// 로그인 화면으로 이동
			// return view ( 'welcome' );
			return redirect ( '/login' );
		}
	}

	/**
	 * 글 본문 읽기
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function staticPage(Request $request, $uri) {

	    // create dataSet
	    $dataSet = $this->createViewData ();
		if($request->has('archiveId')){
			$dataSet ['parameters']['archiveId'] = $request->input('archiveId');
		}

		switch ($uri) {
			case 'shortcut':
				$blade = 'shortcut';
				break;

			default:
				abort(404);
				break;
		}
	    return view ( 'app.static_pages.'.$blade, $dataSet );
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
