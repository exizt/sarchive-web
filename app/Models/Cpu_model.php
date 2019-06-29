<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cpu_model extends Model {
	protected $table = 'sv_content_cpu';

	public function count_posts() {
		//$this->db->select ( 'count(*) as count' );
		return DB::table($this->table)->count();
		//return $this->db->count_all_results ( $this->tbl_master );
	}
	/**
	 *
	 * @param number $page        	
	 * @param number $per_page        	
	 * @return 
	 */
	public function get_list($page = 0, $per_page = 0) {
		$page = $page - 1;
		if ($page < 0) {
			$page = 0;
		}
		$from = $page * $per_page;
		//$this->db->order_by ( "id", "desc" ); // or date, etc
		//DB::table($this->table)
		
		//$this->db->limit ( $per_page, $from );
		//$query = $this->db->get ( $this->tbl_master );
		//$result = $query->result_array ();
		return DB::table($this->table)->limit($per_page)->offset($from)->get();
		//return $query->result_array ();
	}
}