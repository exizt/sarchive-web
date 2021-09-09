<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
	//protected const VIEW_PATH = 'admin.folder-control';

    /**
     * 생성자
     */
	public function __construct() {
		$this->middleware ( 'auth' );
	}

    /**
     * 폴더 목록 조회
     */
    public function index(Request $request)
    {
        $data = array();
        $data['source_ver'] = config('_app.version');
        $data['php_ver'] = $this->getPHPversion();
        return view ( 'admin.index',$data );
    }


    /**
     * PHP version
     */
    private function getPHPversion(){
        return phpversion();
    }


    /**
     *
     * @return string[]
     */
    protected function createViewData() {
        $dataSet = array ();
    	//$dataSet ['ROUTE_ID'] = self::ROUTE_ID;
    	//$dataSet ['VIEW_PATH'] = self::VIEW_PATH;
    	$dataSet ['parameters'] = array();
    	return $dataSet;
    }
}
