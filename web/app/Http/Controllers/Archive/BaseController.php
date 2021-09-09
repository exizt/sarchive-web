<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;

class BaseController extends Controller {
    protected const VIEW_PATH = null;
    /**
     * 생성자
     */
    public function __construct() {
        $this->middleware ( 'auth' );
    }

    /**
     *
     * @return string[]
     */
    protected function createViewData() {
        $data = array ();
        $data ['ROUTE_ID'] = self::ROUTE_ID;
        $data ['VIEW_PATH'] = self::VIEW_PATH;
        $data ['parameters'] = array();
        $data ['paginationParams'] = array();
        $data ['bodyParams'] = array();
        $data ['layoutParams'] = array();
        return $data;
    }
}
