<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

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
     * 폴더 목록 조회
     */
    public function view_version(Request $request)
    {
        $data = array();
        $data['source_ver'] = config('_app.version');
        $data['php_ver'] = $this->getPHPversion();
        $data['mysql_ver'] = $this->getMySQLVersion();
        $data['apache_ver'] = $this->getApacheVersion();
        $data['new_password_26'] = $this->generateNewPassword(26);
        $data['new_password_31'] = $this->generateNewPassword(31);
        $data['new_password_hash'] = Hash::make(Str::random(40));
        return view ( 'admin.versions',$data );
    }


    /**
     * get PHP version
     */
    private function getPHPversion(){
        if(function_exists('phpversion')){
            return phpversion();
        }
        return '?';
    }

    /**
     * get MySQL version
     */
    private function getMySQLVersion() {
        $results = DB::select( DB::raw("select version()") );
        $mysql_version =  $results[0]->{'version()'};

        if(strlen($mysql_version)<=2){
            // 기대치 이하의 결과인 경우에, 다음을 수행
            $output = shell_exec('mysql -V');
            if(!is_null($output)){
                $strings = '';
                preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $strings);
                return $strings[0];
            } else {
                return '?';
            }
        }
        return $mysql_version;
    }


    /**
     * get Apache Version
     */
    private function getApacheVersion(){
        if(function_exists('apache_get_version')){
            $version = apache_get_version();
            //preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $strings);
            //$version = $strings[0];

            //$_SERVER['SERVER_SOFTWARE'];

            //별로 기대치 이하의 값을 가져오면 2번째 방법을 시도.
            if(strlen($version)<=2 || strtolower($version) == 'server'){
                $output = shell_exec('httpd -v');
                if(!is_null($output)){
                    $strings = '';
                    preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $strings);
                    return $strings[0];
                }
            }
            return $version;
        }
        return '?';
    }

    /**
     * 무작위 암호를 생성하기 위한 함수.
     * MySQL 등의 암호를 생성하기에 유용함.
     * MySQL 5.7 버전 기준으로 MaxLength 는 32 char
     * @param number $length
     * @return string|mixed
     */
    private function generateNewPassword($length=5){
        // $rand = Str::random($length);
        // return $rand;
        // 위 방식은 특수문자가 없어서...

        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
            .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
            .'0123456789!@#$%^&*()'); // and any other characters
        shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $rand = '';
        foreach (array_rand($seed, $length) as $k) $rand .= $seed[$k];

        return $rand;
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
