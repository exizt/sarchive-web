<?php
namespace App\Http\Controllers\MyServices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Room_furniture extends Controller {
	protected const VIEW_PATH = 'myservices.furniture';
	
	public function __construct()
	{
		$this->middleware('auth');
	}
	/**
	 * 첫 페이지
	 */
	public function index(Request $request) {
		$data = array ();
		return view ( self::VIEW_PATH .'.room_furniture_2018', $data );
	}
}