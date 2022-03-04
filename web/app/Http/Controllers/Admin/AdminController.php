<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
	/**
	 * view 의 경로.
	 */
	protected const VIEW_PATH = null;
	/**
	 * routes/web 에 지정된 값
	 */
	protected const ROUTE_ID = null;

	/**
	 * 생성자
	 * 관리자용 Controller 의 생성자이므로, 기본적으로 auth 권한을 필요로 한다.
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}


	/**
	 * view 를 생성할 때 필수적인 값을 생성해서 리턴한다.
	 * 보통 Route_id (routes/web 에서 설정된 값) 이나, view 경로 등을 필요로 하는 경우가 생긴다.
	 * @return string[]
	 */
	protected function createViewData()
	{
		// view 관련 설정
		$viewConfig = (object)[];
		$viewConfig->layout = '';
		$viewConfig->route = $this::ROUTE_ID;
		$viewConfig->path = $this::VIEW_PATH;
		$viewConfig->html_title = '';
		$viewConfig->display_title = '';

		// 변수 셋팅
		$data = array();
		$data['ROUTE_ID'] = $this::ROUTE_ID;
		$data['VIEW_PATH'] = $this::VIEW_PATH;
		$data['view'] = $viewConfig;
		return $data;
	}
}
