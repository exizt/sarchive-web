<?php

namespace App\Http\Controllers\MyServices;

use App\Http\Controllers\Controller;
use App\Models\Todo_model;
use Illuminate\Http\Request;
use PHPUnit\Util\Json;

/**
 * to-do 일정관리 서브시스템
 *
 * @author e2xist
 * @todo 데이터베이스 에 맞닿는 부분을 model 로 리팩토링 해야함.
 * @todo 시간 관리 부분에서, 서버 기준/디비 기준 의 이슈가 있음.
 */
class TodoManager_Controller extends Controller {
	protected const VIEW_PATH = 'myservices.todo';
	protected const ROUTE_ID = 'myservice.todo';
	/**
	 *
	 * @var Todo_model
	 */
	public $todo_model;
	public function __construct()
	{
		$this->middleware('auth');
		$this->todo_model = new Todo_model ();
	}
	/**
	 * 첫 페이지 호출
	 *
	 * 로그인 상태일 경우에, 페이지를 호출한다.
	 * 페이지를 호출하고, 페이지 에서 ajax 로 데이터를 호출하게 된다.
	 *
	 */
	public function index() {
		$data = array ();
		return view ( self::VIEW_PATH .'.plan_list', $data );
	}
	/**
	 * 
	 * @param string $mode
	 */
	public function show($mode) {
		if ($mode == 'JSONInBox') {
			$this->JSONInBox ();
		} else if ($mode == 'JSONCompletePlans') {
			$this->JSONCompletePlans ();
		} else if ($mode == 'JSONCompleteToday') {
			$this->JSONCompleteToday ();
		}
	}
	/**
	 * 
	 * @param Request $request
	 */
	public function store(Request $request)
	{
		$mode = $request->input( 'mode' );
		if ($mode == 'toggleCompletedPlan') {
			$this->toggleCompletedPlan($request);
		} else if ($mode == 'removePlan') {
			$this->removePlan($request);
		} else if ($mode == 'addPlan') {
			$this->addPlan($request);
		}
	}
	/**
	 * 계획 일정 의 완료처리
	 */
	public function toggleCompletedPlan(Request $request) {
		$data = array();
		$data ['id'] = $request->input( 'data_srl' );
		$data ['completed_yn'] = $request->input ( 'completed_yn' );
	
		if ($data ['completed_yn'] == 'N') {
			$returnMsg = '완료처리 되었습니다.';
		} else {
			$returnMsg = '완료처리 취소되었습니다.';
		}
	
		if ($this->todo_model->toggle_completed ( $data )) {
			echo $returnMsg;
			return;
		} else {
			echo '오류가 발생하였습니다.';
			return;
		}
	}
	/**
	 * 계획 일정 의 추가
	 */
	public function addPlan(Request $request) {
		$data = array (
				'user_id' => $this->getUserId (),
				'ip' => $request->ip(),
				'task_description' => $request->input ( 'task_description', true ) 
		);
		$this->todo_model->add_plan ( $data );
		echo '추가되었습니다.';
	}
	
	/**
	 * 계획 일정 의 제거
	 */
	public function removePlan(Request $request) {
		$data = array (
				'id' => $request->input ( 'data_srl' ) 
		);
		$this->todo_model->remove_plan ( $data );
		echo '삭제되었습니다.';
	}
	

	
	/**
	 * 완료된 항목들을 볼 수 있는 페이지
	 */
	public function view_list_completed() {
		$this->viewLayout ( self::VIEW_PATH .'.plan_list_completed' );
	}
	
	/**
	 * 할일 목록 조회
	 *
	 * 할일 목록 을 JSON 타입 으로 반환합니다.
	 *
	 * @return json
	 *
	 */
	public function JSONInBox() {
		$this->getInboxPlans ();
	}
	
	/**
	 * 완료 목록 조회
	 *
	 * 완료된 목록을 조회해서 JSON 타입으로 반환합니다.
	 *
	 * @return json
	 *
	 */
	public function JSONCompletePlans() {
		$this->getCompletePlans ();
	}
	
	/**
	 * 완료 목록 조회 (24시간 이내)
	 *
	 * @return Json 24시간 이내 완료된 항목을 조회하게 된다.
	 */
	public function JSONCompleteToday() {
		$this->getCompletePlans ( true );
	}
	
	/**
	 * InBox 아이템 을 조회한다.
	 * 정렬순서는 기본적으로 시간 역순으로 최신순으로 한다.
	 */
	private function getInboxPlans() {
		$args = array (
				'user_id' => $this->getUserId (),
				'completed' => false 
		);
		$master_list = $this->todo_model->get_inbox_plans ( $args );
		
		// --결과 리턴
		$this->returnJSON ( $master_list );
		return;
	}
	
	/**
	 * 완료된 데이터를 조회하는 메서드
	 *
	 * @param string $today
	 *        	오늘것만 가져올 것인지 여부[기본값 false]
	 */
	private function getCompletePlans($is_today = false) {
		// 데이터
		$args = array (
				'user_id' => $this->getUserId (),
				'is_today' => $is_today,
				'completed' => true 
		);
		
		$master_list = $this->todo_model->get_inbox_plans ( $args );
		
		$this->returnJSON ( $master_list );
		return;
	}
	
	/**
	 * JSON
	 *
	 * @param array|object $object        	
	 */
	private function returnJSON($object) {
		echo json_encode ( $object );
		return;
	}
	/**
	 * 유저 의 key 값
	 *
	 * 유저 모델 을 통해서 User 의 key 를 가져온다. 명확하게는 유저의 key 값일 필요는 없고,
	 * 이 서브시스템 에서 유저 를 구분할 수 있는 키 이면 된다.
	 *
	 * @return string
	 */
	private function getUserId() {
		return '1';
	}
}