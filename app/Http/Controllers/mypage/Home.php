<?php
namespace App\Http\Controllers\mypage;
use App\Http\Controllers\Controller;
class Home extends Controller {

	public function index()
	{
		$data = array();
		//echo Auth::user()->getAuthIdentifier();
		return view ( 'mypage/home',$data );
	}
}
