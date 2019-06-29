<?php

namespace App\Http\Controllers\Services\Calculators;

use App\Http\Controllers\Controller;
use App\Calculators\LoanCalculator;
use Illuminate\Http\Request;

class LoanCalculatorController extends Controller {
	protected const VIEW_PATH = 'site.services.calculators';
	
	/**
	 *
	 * @var LoanCalculator
	 */
	public $loan_calculator;
	
	/**
	 * 첫 페이지
	 */
	public function index() {
		$data = array ();
		return view ( self::VIEW_PATH. '.loan_calculator', $data );
	}
	public function store(Request $request) {
		$mode = $request->input ( 'mode' );
		if ($mode == 'run') {
			$this->run ( $request );
		} else if ($mode == 'description') {
		}
	}
	
	/**
	 * 계산
	 */
	public function run(Request $request) {
		// 라이브러리 호출
	    $loanCalculator = new LoanCalculator ();
		
		// 파라미터
		$_amortizationPeriod = $request->input ( 'amortizationPeriod' );
		$_interestRate = $request->input ( 'interestRate' );
		$_principal = $request->input ( 'principal' );
		$_typeOfPayment = $request->input ( 'typeOfPayment' );
		
		if ($_principal != '') {
			$loanCalculator->setPrincipal ( $_principal );
		}
		
		if ($_interestRate != '') {
			$loanCalculator->setInterestRate ( $_interestRate );
		}
		
		if ($_amortizationPeriod != '') {
			$loanCalculator->setAmortizationPeriod ( $_amortizationPeriod );
		}
		
		if ($_typeOfPayment != '') {
			$loanCalculator->setTypeOfPayment ( $_typeOfPayment );
		}
		
		$resultSet = $loanCalculator->execute ()->generateResult ();
		
		echo json_encode ( $resultSet );
		// exit();
	}
	
	/**
	 * 이 페이지의 URL 조회
	 *
	 * 컨트롤러 의 Url 을 가져온다. $data 로 넘겨서 view 나 parse 등에서 사용할 목적.
	 *
	 * @return string
	 */
	private function getServicePath() {
		return $this->controller_dir . '/' . strtolower ( get_called_class () );
	}
}
