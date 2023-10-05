<?php
namespace App\Cheiron;

use Illuminate\Support\Facades\DB;

class VersionInfo {
	
    /**
     * 라라벨 버전 조회
     */
    public static function getLaravelVersion(){
        return app()->version();
    }

    /**
     * PHP 버전 조회
     */
    public static function getPHPVersion(){
        if(function_exists('phpversion')){
            return phpversion();
        }
        return '?';
    }

    /**
     * Apache, Nginx 버전 조회
     */
    public static function getWebServerSoftwareVersion(){
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
     * 웹서버 종류 조회
     */
    public static function getWSSLabel(string $version_str){
        if(stripos($version_str, 'apache') !== false){
            return 'Apache';
        }

        if(stripos($version_str, 'nginx') !== false){
            return 'Nginx';
        }

        //$sSoftware = strtolower( $_SERVER["SERVER_SOFTWARE"] );
        //if ( strpos($sSoftware, "microsoft-iis") !== false ){
        //    return 'iis';
        //}

        return '?';
    }

    /**
     * MySQL/MariaDB 버전 조회
     * 
     * 데이터베이스에서 직접 버전 정보를 가져오는 방식.
     */
    public static function getDBVersion(){
        $laravel_version = self::getLaravelVersion();
        if (version_compare($laravel_version, '10.0.0', '>=')){
            // 라라벨 10 버전 이후
            $version = DB::select('select version()')[0]->{'version()'};
            return (string) $version;
        } else if(version_compare($laravel_version, '10.0.0', '<')){
            // 라라벨 9 버전까지
            $results = DB::select( DB::raw("select version() as version") ); // 이 방식은 라라벨 10에서 오류가 생김.
            $version =  $results[0]->version;
            return (string) $version;
        }
        return '?';
    }

    /**
     * 데이터베이스 종류
     */
    public static function getDBLabel(string $version_str){
        $config_db_default = config('database.default');
        $config_db_driver_name = config('database.connections.'.$config_db_default.'.driver');
        if($config_db_driver_name == 'mysql'){
            $db_label = (stripos($version_str, 'mariadb') !== false) ? 'MariaDB' : 'MySQL';
            return $db_label;
        } else {
            return $config_db_driver_name;
        }
        return '?';
    }

	/**
     * get MySQL version
     * 
     * @deprecated
     */
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
}
