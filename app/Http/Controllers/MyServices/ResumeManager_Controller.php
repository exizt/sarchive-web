<?php
namespace App\Http\Controllers\MyServices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resume_model;
/**
 * 이력서 관리 서브시스템
 *
 * @author e2xist
 *        
 */
class ResumeManager_Controller extends Controller {
	protected const VIEW_PATH = 'myservices.resume';
	private $controller_dir = '/service';
	
	/**
	 *
	 * @var Resume_model
	 */
	public $resume_model;
	/**
	 * 생성자
	 */
	public function __construct() {
		$this->middleware('auth');
		$this->resume_model = new Resume_model();
	}
	
	/**
	 * 첫 페이지 호출
	 *
	 * 페이지를 호출하고, 페이지 에서 ajax 로 데이터를 호출하게 된다.
	 *
	 */
	public function index() {
		// user key 로 데이터 를 체크해서 없을 경우, 하나 를 insert
		$data = $this->initWithGetData ();
		//print_r($data);
		
		//$data = array ();
		return view ( self::VIEW_PATH .'.resume_list', $data );
	}
	
	/**
	 *
	 * @param Request $request
	 */
	public function store(Request $request)
	{
		$mode = $request->input( 'mode' );
		if ($mode == 'save') {
			return $this->save($request);
		}
	}
	
	/**
	 * 저장
	 */
	public function save(Request $request) {
		/*
		 * 디테일 테이블 데이터 변경
		 */
		$resume_srl = $request->input ( 'resume_id' );
	

		// 향후 다양한 테이블 및 컬럼에 대응해야 함.
		$data = $this->getDetailDataFromRequest ($request);
		
		// 업데이트 처리
		$result = $this->resume_model->saveUpdate($resume_srl,$data);
	
		if ($result) {
			return redirect('service/resume')->with('status', '이력서 수정이 완료되었습니다.');
		} else {
			return redirect('service/resume')->with('status', '이력서 수정을 실패하였습니다.');
		}
	}
	
	/**
	 * 첫번째 데이터 입력
	 * 첫번째 접속 시에 user_key 를 기준으로 데이터를 생성
	 */
	private function initWithGetData() {
		$where = array (
				'user_srl' => $this->getUserKey ()
		);
		$count = $this->resume_model->count ( $where );
	
		if ($count == 0) {
			$data = array (
					'subject' => '기본 이력서',
					'is_master_record' => 'Y',
					'user_srl' => $this->getUserKey () ,
					'ip' => $this->input->ip_address ()
			);
			$this->resume_model->insert_new_data ( $data );
		}
	
		return $this->resume_model->getData_forUser ( $this->getUserKey () );
	}
	
	/**
	 *
	 * @return multitype:mixed
	 */
	private function getDetailDataFromRequest(Request $request) {
		$data = array ();
		$data ['details'] = $request->input ( 'details' );
		$data ['answers'] = $request->input ( 'answers' );
		$data ['school'] = $request->input ( 'school' );
		$data ['career'] = $request->input ( 'career' );
		$data ['cert'] = $request->input ( 'cert' );
		$data ['language'] = $request->input ( 'language' );
		$data ['skill'] = $request->input ( 'skill' );
		$data ['project'] = $request->input ( 'project' );
		return $data;
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
	private function getUserKey() {
		return '1';
	}
	
	/**
	 * 이 페이지의 URL 조회
	 *
	 * 컨트롤러 의 Url 을 가져온다. $data 로 넘겨서 view 나 parse 등에서 사용할 목적.
	 *
	 * @return string
	 */
	private function getServicePath() {
		return $this->controller_dir . '/' . strtolower ( get_class () );
	}
	
	/**
	 * 로그인 중 발생하는 에러 표시
	 * flashdata 를 사용. (세션 사용)
	 * @param string $message
	 */
	private function setMessages($message='',$status='error')
	{
		if($status=='success'){
			$class = 'alert-success';
		} else if($status=='info'){
			$class = 'alert-info';
		} else if($status=='warning'){
			$class = 'alert-warning';
		} else if($status=='danger'||$status=='error'){
			$class = 'alert-danger';
		} else {
			$class = 'alert-warning';
		}
		
		$this->session->set_flashdata('msg', '<div class="alert '.$class.' alert-dismissible text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span>&nbsp;'.$message.'</div>');
	}
}