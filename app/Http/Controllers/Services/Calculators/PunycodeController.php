<?php
namespace App\Http\Controllers\Services\Calculators;
use App\Http\Controllers\Controller;
use App\Calculators\PunycodeCalculator;
use Illuminate\Http\Request;

/**
 * @todo 입력 글자 수 제한 걸기
 * @author Adminn
 *
 */
class PunycodeController extends Controller {
	protected const VIEW_PATH = 'site.services.calculators';
	
	/**
	 * 
	 * @var PunycodeCalculator
	 */
	public $punycode_calculator;
	/**
	 * 첫 페이지
	 */
	public function index() {
		$data = array ();
		return view(self::VIEW_PATH.'.punycode',$data);
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
				'domain' => $request->input ( 'domain' ),
		);
		$punycodeCalculator = new PunycodeCalculator();
		
		$punycodeCalculator->prepare($options);
		$resultSet = $punycodeCalculator->execute()->generateResult();
		
		echo json_encode($resultSet);
		// exit();
	}
}
