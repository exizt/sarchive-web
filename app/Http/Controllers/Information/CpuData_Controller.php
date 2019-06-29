<?php
namespace App\Http\Controllers\Information;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cpu_model;

class CpuData_Controller extends Controller {
	protected const VIEW_PATH = 'site.document_simple';
	
	/**
	 *
	 * @var Cpu_model
	 */
	public $cpu_model;

	/**
	 * 목록 조회
	 */
	public function index(Request $request) {
		//$cur_page = $this->uri->segment ( 4 );
		$cur_page = $request->segment(4);
		$this->cpu_model = new Cpu_model();
		
		$total_rows = $this->cpu_model->count_posts ();
		
		$paginator = $this->cpu_model->paginate($cur_page);
		$paginator->setPath('/information/cpu/');
		$data = array (
				'dataset' => $paginator,
				'total_rows' => $total_rows
		);
		return view(self::VIEW_PATH.'.cpu_list',$data);
	}
}
