<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Home extends Controller {
	/**
	 *
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function index(Request $request) {
		if ($this->isMobile ()) {
			session ( [ 
					'is_mobile' => true 
			] );
			if($request->get('toggle_deskmode','N')=='Y'){
				$this->toggleDesktopMode();
				return redirect ( '/' );
			} else {
				return view ( 'home' );
			}
		} else {
			session ( [
					'is_mobile' => false
			] );
			return view ( 'home' );
		}
	}
	/**
	 */
	public function toggleDesktopMode() {
		// 모바일 장치 인지 체크를 먼저 해본다. 모바일 장치 일 경우에.
		// 세션에 있는 정보를 토글한다.
		if ($this->isMobile ()) {
			if (session('is_desktop_mode',false) === TRUE) {
				session ( [ 
						'is_desktop_mode' => false 
				] );
			} else {
				session ( [ 
						'is_desktop_mode' => true 
				] );
			}
		}
		//return redirect ( '/' );
	}
	/**
	 *
	 * @return number
	 */
	private function isMobile() {
		//ipad 제외
		if(preg_match ( "/(ipad)/i", $_SERVER ["HTTP_USER_AGENT"] )){
			return false;
		}
		return preg_match ( "/(android|tablet|phone|avantgo|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|pie|up\.browser|up\.link|webos|wos)/i", $_SERVER ["HTTP_USER_AGENT"] );
		
	}
}
