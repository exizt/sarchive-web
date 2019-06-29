<?php
namespace App\Http\Controllers\Services\Simple;
use App\Http\Controllers\Controller;

class ExcelToDokuwiki_Controller extends Controller {

	public function index() {
		$data = array ();
		return view ( 'site.services.simple.excel_to_dokuwiki',$data );
	}
}
