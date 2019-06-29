<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Todo_model extends Model {
	/**
	 * 테이블명
	 *
	 * @var string
	 */
	private $tbl_master = 'sv_todo_lists';
	private $tbl_detail = 'sv_todo_details';
	
	/**
	 * InBox 아이템 을 조회한다.
	 * 정렬순서는 기본적으로 시간 역순으로 최신순으로 한다.
	 */
	public function get_inbox_plans($data = array()) {
		$query = DB::table ( $this->tbl_master . ' as mst' );
		$query->leftJoin ( $this->tbl_detail . ' as dtl', 'mst.id', '=', 'dtl.todo_id' );
		$query->select ( 'mst.id as key', 'mst.created_at', 'mst.created_ip', 'dtl.subject', 'dtl.modified_at', 'dtl.modified_ip', 'dtl.completed_at', 'dtl.completed_yn' );
		$query->where ( 'mst.user_id', $data ['user_id'] );
		$query->orderBy ( 'mst.created_at', 'desc' );
		$query->orderBy ( 'mst.id', 'asc' );
		
		if (isset ( $data ['completed'] ) && $data ['completed']) {
			$query->where ( 'dtl.completed_yn', 'Y' );
		} else if (isset ( $data ['completed'] ) && ! $data ['completed']) {
			$query->where ( 'dtl.completed_yn', 'N' );
		}
		if (isset ( $data ['is_today'] ) && $data ['is_today']) {
			$query->whereRaw ( 'dtl.completed_at > date_add(now(), interval -1 day)' );
		}
		
		// --결과 처리
		$master_list = array ();
		foreach ( $query->get () as $row ) {
			$item = new \stdClass ();
			$item->subject = $row->subject;
			$item->todo_key = $row->key;
			
			$detail = new \stdClass ();
			$detail->key = $row->key;
			$detail->completed_at = $row->completed_at;
			$detail->completed_yn = $row->completed_yn;
			
			$item->details = $detail;
			$master_list [] = $item;
		}
		return $master_list;
	}
	
	/**
	 * 계획 일정 의 완료처리
	 *
	 * 일정 의 완료 를 처리하는 프로세스 담당이다.
	 * 뷰에서 호출된 요청 을 처리하는 프로세스 이다. 일정 의 상태를 완료로 변경한다.
	 * 완료된 일정은 '완료 일정' 으로 보여지게 된다.
	 */
	public function toggle_completed($data = array()) {
		$query = DB::table ( $this->tbl_detail );
		
		if (! $data ['id']) {
			return false;
		}
		$query->where ( 'todo_id', $data ['id'] );
		
		if ($data ['completed_yn'] == 'N') {
			// 원래의 완료여부가 N 일 때 에는 Y 로 전환
			$query->update ( [ 
					'completed_yn' => 'Y',
					'completed_at' => DB::raw ( 'NOW()' ) 
			] );
		} else {
			// 원래의 완료여부가 Y 일 때에는 N 으로 전환
			$query->update ( [ 
					'completed_yn' => 'N',
					'completed_at' => null 
			] );
		}
		return true;
	}
	
	/**
	 * 계획 일정 의 추가
	 *
	 * 일정을 추가한다. 'indox' 에 추가가 된다.
	 * 테이블에는 master 와 detail 에 데이터가 추가가 된다.
	 * master 는 user 와 todo 를 연결하는 테이블이다. (릴레이션 용 테이블)
	 * 데이터는 detail 에 기입하도록 한다.
	 */
	public function add_plan($data = array()) {
		/**
		 * 마스터 정보 입력
		 *
		 * @var array $_data
		 */
		$_data = array (
				'user_id' => $data ['user_id'],
				'created_at' => DB::raw ( 'NOW()' ),
				'created_ip' => DB::raw ( 'INET_ATON(\'' . $data ['ip'] . '\')' ) 
		);
		$query = DB::table ( $this->tbl_master );
		$insert_id = $query->insertGetId ( $_data );
		
		/**
		 * 디테일 정보 입력
		 */
		$_data = array (
				'todo_id' => $insert_id,
				'subject' => $data ['task_description'],
				'created_at' => DB::raw ( 'NOW()' ),
				'created_ip' => DB::raw ( 'INET_ATON(\'' . $data ['ip'] . '\')' ) 
		);
		$query = DB::table ( $this->tbl_detail );
		$query->insert ( $_data );
		return true;
	}
	
	/**
	 * 계획 일정 의 제거
	 *
	 * 일정 자체 를 잘못 기입 등의 이유로 삭제 하고 싶을 때에 사용된다.
	 */
	public function remove_plan($data = array()) {
		// detail 삭제
		$query = DB::table ( $this->tbl_detail );
		$query->where('todo_id' ,$data ['id'] )->delete();
		
		// master 삭제
		$query = DB::table ( $this->tbl_master );
		$query->where('id' ,$data ['id'] )->delete();
		return true;
	}
	
	/**
	 * 결과의 레코드 수 를 구함.
	 *
	 * 보통 페이지네이션 등에서 활용됨.
	 *
	 * @param string $where        	
	 * @return number
	 */
	public function count($where = null) {
		if (isset ( $where )) {
			$this->db->where ( $where );
		}
		$this->db->from ( $this->tbl_master );
		return $this->db->count_all_results ();
	}
}