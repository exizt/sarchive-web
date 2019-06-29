<?php

namespace App\Http\Controllers\Services\Simple;

use App\Http\Controllers\Controller;

class IpView_Controller extends Controller {
	public function index() {
		$data = array ();
		$data ['ip'] = request()->ip();
		return view ( 'site.services.simple.yourip', $data );
	}
}
