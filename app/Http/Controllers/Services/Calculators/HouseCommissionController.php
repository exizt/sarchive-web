<?php
namespace App\Http\Controllers\Services\Calculators;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Calculators\HouseCommissionCalculator;

class HouseCommissionController extends Controller {
	protected const VIEW_PATH = 'site.services.calculators';
	/**
	 *
	 * @var HouseCommissionCalculator
	 */
	public $houseCommissionCalculator;
	
	/**
	 * 첫 페이지
	 */
	public function index() {
		$data = array ();
		return view(self::VIEW_PATH.'.house_commission',$data);
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
	    $houseCommissionCalculator = new HouseCommissionCalculator();
		$options = array (
				'deposit' => $request->input( 'deposit' ),
				'monthly_fee' => $request->input ( 'monthly_fee' ) 
		);

		$houseCommissionCalculator->prepare($options);
		$resultSet = $houseCommissionCalculator->execute()->generateResult();
		
		echo json_encode($resultSet);
	}

}
