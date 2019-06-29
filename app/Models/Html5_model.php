<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Html5_model extends Model {
	protected $table = 'sv_content_html5';

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
		return DB::table($this->table)->limit($per_page)->offset($from)->get();
	}
}