<?php
namespace App\Http\Controllers\Services\Calculators;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Calculators\LandUnitCalculator;

class LandUnitCalculatorController extends Controller {
	protected const VIEW_PATH = 'site.services.calculators';
	
	/**
	 * 
	 * @var LandUnitCalculator
	 */
	public $landcalculator;
	/**
	 * 첫 페이지
	 */
	public function index() {
		$data = array ();
		return view(self::VIEW_PATH.'.landconvert',$data);
	}
	
	public function store(Request $request)
	{
		$mode = $request->input ( 'mode' );
		if ($mode == 'run') {
			$this->run ( $request );
		}
	}
	/**
	 * 계산
	 */
	public function run(Request $request) {
		$options = array (
				'pyeong' => $request->input ( 'pyeong' ),
				'squareMeter' => $request->input( 'squareMeter' )
		);
		$landCalculator = new LandUnitCalculator();
		
		$landCalculator->prepare($options);
		$resultSet = $landCalculator->execute()->generateResult();
		
		echo json_encode($resultSet);
		// exit();
	}
}
