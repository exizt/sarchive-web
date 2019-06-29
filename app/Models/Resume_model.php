<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 
 * @author Adminn
 *
 */
class Resume_model extends Model {
	/**
	 * 테이블명
	 */
	private $tbl_resumes = 'sv_resumes';
	private $tbl_resume_details = 'sv_resume_details';
	private $tbl_resume_answers = 'sv_resume_answers';
	private $tbl_resume_schools = 'sv_resume_schools';
	private $tbl_resume_skills = 'sv_resume_skills';
	private $tbl_resume_projects = 'sv_resume_projects';
	private $tbl_resume_certificates = 'sv_resume_certificates';
	private $tbl_resume_careers = 'sv_resume_careers';
	private $tbl_resume_language_skills = 'sv_resume_language_skills';
	
	private $user_key;
	
	/**
	 * 생성자
	 */
	public function __construct() {
		parent::__construct ();
	}
	

	
	/**
	 * 이력서 아이템 데이터베이스 조회
	 * @param string $resume_srl
	 */
	public function get_item($resume_srl) {
		//$db = $this->db;
		
		$resultSet = array();
		$resultSet['resume_id'] = $resume_srl;

		// 이력서 마스터 테이블 조회
		//$db->select ( '*' );
		//$db->from ( $this->tbl_resumes );
		//$db->where ( 'id', $resume_srl );
		$query = DB::table($this->tbl_resumes);
		$query->where('id', $resume_srl);
		//$query = $db->get ();
		foreach($query->first() as $k => $v)
		{
			$resultSet['view_'.$k] = $v;
		}
		
		// 디테일 정보
		$query = DB::table($this->tbl_resume_details);
		$query->where('resume_id', $resume_srl);
		foreach($query->first() as $k => $v)
		{
			$resultSet['view_details_'.$k] = $v;
		}
		
		// 자기소개서 항목 및 질문 답변 항목
		$query = DB::table($this->tbl_resume_answers);
		$query->where('resume_id', $resume_srl);
		foreach($query->first() as $k => $v)
		{
			$resultSet['view_answers_'.$k] = $v;
		}
		
		// 학력 사항
		$query = DB::table($this->tbl_resume_schools);
		$query->where('resume_id', $resume_srl);
		$resultSet ['view_schools'] = $query->get ();
			
		// 자격증 사항
		$query = DB::table($this->tbl_resume_certificates);
		$query->where('resume_id', $resume_srl);
		$resultSet ['view_certificates'] = $query->get ();
		
		// 경력 사항
		$query = DB::table($this->tbl_resume_careers);
		$query->where('resume_id', $resume_srl);
		$resultSet ['view_careers'] = $query->get ();
		
		// 기술 사항
		$query = DB::table($this->tbl_resume_skills);
		$query->where('resume_id', $resume_srl);
		$resultSet ['view_skills'] = $query->get ();
		
		// 프로젝트 사항
		$query = DB::table($this->tbl_resume_projects);
		$query->where('resume_id', $resume_srl);
		$resultSet ['view_projects'] = $query->get ();
		
		// 어학정보
		$query = DB::table($this->tbl_resume_language_skills);
		$query->where('resume_id', $resume_srl);
		$resultSet ['view_language_skills'] = $query->get ();
		
		return $resultSet;
	}
	
	/**
	 * 이력서 아이템 데이터베이스 조회
	 * @param string $user_key
	 */
	public function getData_forUser($user_key) {
		/*
		$db = $this->db;
	
		// 이력서 마스터 테이블 조회
		$db->select ( 'id' );
		$db->from ( $this->tbl_resumes );
		$db->where ( 'user_srl', $user_key );
		$query = $db->get ();
		
		$resultSet = $query->row_array ();
		
		$resume_srl = $resultSet['id'];
		*/
		
		$resume_srl = DB::table($this->tbl_resumes)->where('user_srl',$user_key)->value('id');
		
		return $this->get_item($resume_srl);
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
		$query = DB::table($this->tbl_resumes);
	
		if(isset($where)){
			$query->where ( $where );
		}
		return $query->count ();
	}
	
	/**
	 * 첫번째 데이터 입력
	 *
	 * resumes , resume_details, resume_answers 데이터 생성.
	 * 다중 로우 인 테이블은 생성 안 함. 1:1 관계의 것들만 데이터 생성
	 */
	public function insert_new_data($data = array()) {
		/*
		 * 마스터 데이터 생성
		 */
		$insert_data = array (
				'subject' => $data ['subject'],
				'is_master_record' => $data ['is_master_record'],
				'user_srl' => $data ['user_srl'] ,
				'created_at' => DB::raw ( 'NOW()' ),
				'created_ip' => DB::raw ( 'INET_ATON(\'' . $data ['ip'] . '\')' )
		);
		$query = DB::table ( $this->tbl_resumes );
		$master_id = $query->insertGetId ( $insert_data );
	
		/*
		 * 세부 데이터 생성
		 */
		$insert_data = array (
				'resume_id' => $master_id
		);
		DB::table( $this->tbl_resume_details)->insert($insert_data);
		DB::table( $this->tbl_resume_details)->insert($insert_data);
		DB::table( $this->tbl_resume_answers)->insert($insert_data);
		DB::table( $this->tbl_resume_schools)->insert($insert_data);
		DB::table( $this->tbl_resume_skills)->insert($insert_data);
		DB::table( $this->tbl_resume_projects)->insert($insert_data);
		DB::table( $this->tbl_resume_certificates)->insert($insert_data);
		DB::table( $this->tbl_resume_careers)->insert($insert_data);
		DB::table( $this->tbl_resume_language_skills)->insert($insert_data);
	}
	
	/**
	 * 수정 건의 처리
	 *
	 * @return boolean
	 */
	public function saveUpdate($key,$data = array()) {

		/*
		 * 디테일 데이터
		 *
		 * 데이터가 없으면 insert. 있으면 update
		 */
		if (isset ( $data ['details'] )) {
			$v = $data ['details'];
			$_data = array (
					'name' => $v ['name'],
					'name_english' => $v ['name_english'],
					'name_hanja' => $v ['name_hanja'],
					'birthday' => $v ['birthday'],
					'married' => $v ['married'],
					'family' => $v ['family'],
					'expected_salary' => $v ['expected_salary'],
					'mil_category' => $v ['mil_category'],
					'mil_kind' => $v ['mil_kind'],
					'mil_grade' => $v ['mil_grade'],
					'mil_date_start' => $v ['mil_date_start'],
					'mil_date_End' => $v ['mil_date_End'],
					'mil_branch' => $v ['mil_branch'],
					'mil_note' => $v ['mil_note'],
					'disability_category' => $v ['disability_category'],
					'disability_date' => $v ['disability_date'],
					'disability_grade' => $v ['disability_grade'],
					'veteran_category' => $v ['veteran_category'],
					'veteran_number' => $v ['veteran_number'],
					'veteran_org' => $v ['veteran_org'],
			);
			$query = DB::table ( $this->tbl_resume_details );
			$query->where ( 'resume_id', $key );
			$query->update($_data);
		}
	
		/*
		 * 소개 서 및 질문답변
		 *
		 * 데이터가 없으면 insert. 있으면 update
		 */
		if (isset ( $data ['answers'] )) {
			$v = $data ['answers'];
			$_data = array (
					'pr_common_1' => $v ['pr_common_1'],
					'pr_common_2' => $v ['pr_common_2'],
					'pr_common_3' => $v ['pr_common_3'],
					'pr_common_4' => $v ['pr_common_4'],
					'pr_common_5' => $v ['pr_common_5'],
					'pr_answer_1' => $v ['pr_answer_1'],
					'pr_answer_2' => $v ['pr_answer_2'],
					'pr_answer_3' => $v ['pr_answer_3'],
					'pr_answer_4' => $v ['pr_answer_4'],
					'pr_answer_5' => $v ['pr_answer_5'],
					'pr_answer_6' => $v ['pr_answer_6']
			);
			$query = DB::table ( $this->tbl_resume_answers );
			$query->where ( 'resume_id', $key );
			$query->update($_data);
		}
	
		/*
		 * 학력사항 처리
		 */
		foreach ( $data ['school'] as $k => $v ) {
			$_data = array (
					'type' => $v ['type'],
					'name' => $v ['name'],
					'date_admission' => $v ['dateAdmission'],
					'date_graduation' => $v ['dateGraduation'],
					'degree' => $v ['degree'],
					'major' => $v ['major'],
					'minor' => $v ['minor'],
					'graduation' => $v ['graduation'],
					'gpa' => $v ['gpa'],
					'scorescale' => $v ['scoreScale']
			);
			
			
			$query = DB::table ( $this->tbl_resume_schools );
			$query->where ( 'resume_id', $key );
			$query->where ( 'school_idx', $k );
			if ($query->count() > 0) {
				// 있으면 update
				$query = DB::table ( $this->tbl_resume_schools );
				$query->where ( 'resume_id', $key );
				$query->where ( 'school_idx', $k );
				$query->update($_data);
			} else {
				// 없으면 insert
				$_data ['resume_id'] = $key;
				$_data ['school_idx'] = $k;
				$query = DB::table ( $this->tbl_resume_schools );
				$query->insert($_data);
			}
		}
	
		/*
		 * 경력사항 처리
		 */
		foreach ( $data ['career'] as $k => $v ) {
			$_data = array (
					'company' => $v ['company'],
					'date_start' => $v ['dateStart'],
					'date_end' => $v ['dateEnd'],
					'type' => $v ['type'],
					'department' => $v ['department'],
					'position' => $v ['position'],
					'role' => $v ['role'],
					'salary' => $v ['salary'],
					'reason' => $v ['reason']
			);
			$query = DB::table ( $this->tbl_resume_careers );
			$query->where ( 'resume_id', $key );
			$query->where ( 'career_idx', $k );
			if ($query->count() > 0) {
				// 있으면 update
				$query = DB::table ( $this->tbl_resume_careers );
				$query->where ( 'resume_id', $key );
				$query->where ( 'career_idx', $k );
				$query->update($_data);
			} else {
				// 없으면 insert
				$_data ['resume_id'] = $key;
				$_data ['career_idx'] = $k;
				$query = DB::table ( $this->tbl_resume_careers );
				$query->insert($_data);
			}
		}
	
		/*
		 * 자격증 정보
		 */
		foreach ( $data ['cert'] as $k => $v ) {
			$_data = array (
					'name' => $v ['name'],
					'date' => $v ['date'],
					'number' => $v ['number'],
					'institute' => $v ['institute']
			);
			
			$query = DB::table ( $this->tbl_resume_certificates );
			$query->where ( 'resume_id', $key );
			$query->where ( 'cert_idx', $k );
			if ($query->count() > 0) {
				// 있으면 update
				$query = DB::table ( $this->tbl_resume_certificates );
				$query->where ( 'resume_id', $key );
				$query->where ( 'cert_idx', $k );
				$query->update($_data);
			} else {
				// 없으면 insert
				$_data ['resume_id'] = $key;
				$_data ['cert_idx'] = $k;
				$query = DB::table ( $this->tbl_resume_certificates );
				$query->insert($_data);
			}
			
		}
	
		/*
		 * 어학 정보
		 */
		foreach ( $data ['language'] as $k => $v ) {
			$_data = array (
					'category' => $v ['category'],
					'examination' => $v ['examination'],
					'date' => $v ['date'],
					'institute' => $v ['institute'],
					'score' => $v ['score'],
					'grade' => $v ['grade'],
					'note' => $v ['note']
			);
			
			$query = DB::table ( $this->tbl_resume_language_skills );
			$query->where ( 'resume_id', $key );
			$query->where ( 'lang_idx', $k );
			if ($query->count() > 0) {
				// 있으면 update
				$query = DB::table ( $this->tbl_resume_language_skills );
				$query->where ( 'resume_id', $key );
				$query->where ( 'lang_idx', $k );
				$query->update($_data);
			} else {
				// 없으면 insert
				$_data ['resume_id'] = $key;
				$_data ['lang_idx'] = $k;
				$query = DB::table ( $this->tbl_resume_language_skills );
				$query->insert($_data);
			}
			
		}
	
		/*
		 * 스킬 정보
		 */
		foreach ( $data ['skill'] as $k => $v ) {
			$_data = array (
					'category' => $v ['category'],
					'detail' => $v ['detail'],
					'grade' => $v ['grade'],
					'note' => $v ['note']
			);

			
			$query = DB::table ( $this->tbl_resume_skills );
			$query->where ( 'resume_id', $key );
			$query->where ( 'skill_idx', $k );
			if ($query->count() > 0) {
				// 있으면 update
				$query = DB::table ( $this->tbl_resume_skills );
				$query->where ( 'resume_id', $key );
				$query->where ( 'skill_idx', $k );
				$query->update($_data);
			} else {
				// 없으면 insert
				$_data ['resume_id'] = $key;
				$_data ['skill_idx'] = $k;
				$query = DB::table ( $this->tbl_resume_skills );
				$query->insert($_data);
			}
		}
	
		/*
		 * 프로젝트 이력
		 */
		foreach ( $data ['project'] as $k => $v ) {
			$_data = array (
					'subject' => $v ['subject'],
					'date_start' => $v ['dateStart'],
					'date_end' => $v ['dateEnd'],
					'company' => $v ['company'],
					'company2' => $v ['company2'],
					'role' => $v ['role'],
					'note' => $v ['note']
			);

			
			$query = DB::table ( $this->tbl_resume_projects );
			$query->where ( 'resume_id', $key );
			$query->where ( 'project_idx', $k );
			if ($query->count() > 0) {
				// 있으면 update
				$query = DB::table ( $this->tbl_resume_projects );
				$query->where ( 'resume_id', $key );
				$query->where ( 'project_idx', $k );
				$query->update($_data);
			} else {
				// 없으면 insert
				$_data ['resume_id'] = $key;
				$_data ['project_idx'] = $k;
				$query = DB::table ( $this->tbl_resume_projects );
				$query->insert($_data);
			}
			
		}
		return true;
	}
}