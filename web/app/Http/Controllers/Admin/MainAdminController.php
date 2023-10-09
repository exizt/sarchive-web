<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Cheiron\VersionInfo;

class MainAdminController extends AdminController
{
    /**
     * 생성자
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 관리자 첫 페이지
     */
    public function index(Request $request)
    {
        $data = array();
        $data['source_ver'] = config('_app.version');
        $data['php_ver'] = VersionInfo::getPHPVersion();
        return view('admin.home', $data);
    }


    /**
     * 버전 정보 조회
     */
    public function viewVersion(Request $request)
    {
        // 데이터베이스 버전 및 레이블
        $db_version = VersionInfo::getDBVersion();
        $db_label = VersionInfo::getDBLabel($db_version);
        
        // 웹 서버 소프트웨어 버전 및 레이블
        $wss_version = VersionInfo::getWebServerSoftwareVersion();
        $wss_label = VersionInfo::getWSSLabel($wss_version);

        $data = array();
        $data['source_ver'] = config('_app.version');
        $data['laravel_ver'] = VersionInfo::getLaravelVersion();
        $data['php_ver'] = VersionInfo::getPHPVersion();
        $data['db_ver'] = $db_version;
        $data['db_label'] = $db_label;
        $data['wss_ver'] = $wss_version;
        $data['wss_label'] = $wss_label;
        return view('admin.version', $data);
    }
}
