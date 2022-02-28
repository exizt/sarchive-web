<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
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
     * 폴더 목록 조회
     */
    public function view_version(Request $request)
    {
        $data = array();
        $data['source_ver'] = config('_app.version');
        $data['laravel_ver'] = app()->version();
        $data['php_ver'] = $this->getPHPversion();
        $data['db_ver'] = $this->getDBVersion();
        $data['wss_ver'] = $this->getWebServerSoftwareVersion();
        return view ( 'admin.versions',$data );
    }


    /**
     * PHP 버전 조회
     */
    private function getPHPversion(){
        if(function_exists('phpversion')){
            return phpversion();
        }
        return '?';
    }

    /**
     * Apache, Nginx 버전 조회
     */
    private function getWebServerSoftwareVersion(){
        $version = '';

        if(function_exists('apache_get_version')){
            $version = apache_get_version();
            //preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $strings);
            //$version = $strings[0];

            //$_SERVER['SERVER_SOFTWARE'];

            // 만족할 만한 값일 때에 반환
            if(strlen($version) > 5 && strtolower($version) != 'server' && strtolower($version) != 'apache'){
                return $version;
            }
        }

        // 위에서 만족할 만한 값이 아닌 경우 httpd나 apache2 명령어를 통해서 버전값을 조회
        $output = shell_exec('httpd -v');

        if(is_null($output)){
            $output = shell_exec('apache2 -v');
        }
        if(is_null($output)){
            $output = shell_exec('nginx -v 2>&1');
        }
        if(!is_null($output)){
            $strings = '';
            // preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $strings);
            preg_match('/[a-zA-Z\/]+[0-9]+\.[0-9]+\.[0-9]+[a-zA-Z\s\(\)]*$/m', $output, $strings);
            return $strings[0];
        }
        return '?';
    }

    /**
     * MySQL/MariaDB 버전 조회
     */
    private function getDBVersion(){
        $results = DB::select( DB::raw("select version() as version") );
        $version =  $results[0]->version;
        
        return $version;
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
