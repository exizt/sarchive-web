<?php
namespace App\Http\Controllers\MyServices;
use App\Http\Controllers\Controller;

class HomeController extends Controller {
	public function __construct() {
		$this->middleware('auth');
	}
	public function index()
	{
		//echo Auth::user()->getAuthIdentifier();
		$today = date('l jS F Y h:i:s A');
		
		$data = array();
		$data['today'] = $today;
		return view ( 'myservices/dashboard-myservice',$data );
	}
}
