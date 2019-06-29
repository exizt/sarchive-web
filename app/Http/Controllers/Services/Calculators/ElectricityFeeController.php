<?php
namespace App\Http\Controllers\Services\Calculators;
use App\Http\Controllers\Controller;

class ElectricityFeeController extends Controller {
	protected const VIEW_PATH = 'site.services.calculators';
	
	/**
	 * 첫 페이지
	 */
	public function index() {
		$data = array ();
		return view(self::VIEW_PATH. '.electricity_fee',$data);
	}
}
