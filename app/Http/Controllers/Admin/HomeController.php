<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HomeController extends Controller {
	public function __construct() {
		$this->middleware('auth');
	}
	public function index()
	{
	    $dataSet = array();
		//echo Auth::user()->getAuthIdentifier();
		return view ( 'admin/home',$dataSet );
	}
	
	
	public function versionInfo()
	{
	    $newPassword = $this->generateNewPassword(25);
	    //$newPassword = bcrypt('$2y$10$90Sz9v2/3AdTFda9sLl5DuFKYRRYd/dNBc1d82bK7vDQJwTEHkH0S');
	    
	    
	    $dataSet = array();
	    //echo Auth::user()->getAuthIdentifier();
	    $dataSet['PHP_VERSION'] = $this->getPHPversion();
	    $dataSet['MYSQL_VERSION'] = $this->getMySQLVersion();
	    $dataSet['APACHE_VERSION'] = $this->getApacheVersion();
	    $dataSet['newPassword'] = $newPassword;
	    $dataSet['latestModified'] = date('Y-m-d H:i:s',$this->getLatestModifiedData());
	    
	    return view ( 'admin/version',$dataSet );
	}
	
	private function getApacheVersion(){
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
	private function getPHPversion(){
	    return phpversion();
	}
	private function getMySQLVersion() {
	    $output = shell_exec('mysql -V');
	    if(!is_null($output)){
	    	$strings = '';
	        preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $strings);
	        return $strings[0];
	    } else {
	        return '?';
	    }
	}
	
	/**
	 * 무작위 암호를 생성하기 위한 함수.
	 * MySQL 등의 암호를 생성하기에 유용함. 
	 * MySQL 5.7 버전 기준으로 MaxLength 는 32 char
	 * @param number $length
	 * @return string|mixed
	 */
	private function generateNewPassword($length=5){
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
	 * @return number
	 */
	private function getLatestModifiedData(){
	    //echo realpath( dirname( __FILE__ ).'/../../../../' );
	    $historyFilePath = realpath( dirname( __FILE__ ).'/../../../../history.md' );
	    //echo $historyFilePath;
	    if(file_exists($historyFilePath)){
    	    $filemtime = filemtime($historyFilePath);
	    } else {
	        $filemtime = getlastmod();
	    }
	    return $filemtime;
	}
}
