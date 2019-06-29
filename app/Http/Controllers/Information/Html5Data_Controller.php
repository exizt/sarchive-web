<?php
namespace App\Http\Controllers\Information;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Html5_model;
class Html5Data_Controller extends Controller {
	protected const VIEW_PATH = 'site.document_simple';
	
	/**
	 * 목록 조회
	 */
	public function index(Request $request) {
		$cur_page = $request->segment(4);
		$this->html5_model = new Html5_model();
		
		$total_rows = $this->html5_model->count_posts ();
		
		$paginator = $this->html5_model->paginate(21);
		$paginator->setPath('/information/html5/');
		$data = array (
				'dataset' => $paginator,
				'total_rows' => $total_rows
		);
		return view(self::VIEW_PATH.'.html5_list',$data);
		
	}
}
