<?php
namespace App\Calculators;

class LoanCalculator {
	// --기본변수
	private $result = false; // 결과
	private $debugstring; // 디버그 문자열
	const MAX_TERMS = 360;
	// --입력값
	public $principal = 1000000; // 대출원금(기본 100만)
	public $interestRate = 3.5; // 대출이자율
	public $amortizationPeriod = 60; // 대출기간
	public $typeOfPayment = 'evenPayment';
	
	// --변수
	public $schedule = array ();
	
	/**
	 */
	public function __construct() {
	}
	
	/**
	 * 연산
	 * 
	 * @return LoanCalculator
	 */
	public function execute() {
		if ($this->principal <= 100000) {
			$this->result = false;
			return $this;
		}
		if ($this->amortizationPeriod <= 5) {
			$this->result = false;
			return $this;
		}
		
		if ($this->interestRate <= 1) {
			$this->result = false;
			return $this;
		}
		
		// 백분율이므로 1/100 처리
		$this->interestRate = $this->interestRate / 100;
		
		if ($this->typeOfPayment == 'evenPayment') {
			// 원리금 균등 분할 방식
			$this->schedule = $this->calcLoanScheduleOnType1 ();
		} else {
			// 원금 균등 분할 방식
			$this->schedule = $this->calcLoanScheduleOnType2 ();
		}
		
		$this->result = true;
		return $this;
	}
	
	/**
	 * data get
	 */
	public function generateResult() {
		$resultSet = array ();
		$resultSet ['result'] = $this->result;
		$resultSet ['schedule'] = $this->schedule;
		$resultSet ['debug'] = $this->getDebug ();
		return $resultSet;
	}
	
	/**
	 * 원리금 균등 분할 방식.
	 * 전체 상환 스케쥴 계산.
	 *
	 * @return multitype:multitype:string number
	 */
	public function calcLoanScheduleOnType1() {
		$paymentSchedule = array ();
		$payment = $this->calcPaymentOnType1 (); // 월별 지불액. 원리금
		$loanBalance = $this->principal; // 원금잔액. 초기값은 원금.
		
		$monthly = array ();
		for($i = 0; $i < $this->amortizationPeriod; $i ++) {
			$interestPaid = $this->roundup ( $loanBalance * $this->interestRate / 12 ); // 이자지불액
			$principalPaid = $payment - $interestPaid; // 원금지불액
			$loanBalance -= $principalPaid; // 원금 잔액
			
			$monthly ['payment'] = $this->format ( $payment );
			$monthly ['principalPaid'] = $this->format ( $principalPaid );
			$monthly ['interestPaid'] = $this->format ( $interestPaid );
			$monthly ['loanBalance'] = $this->format ( $loanBalance );
			$monthly ['index'] = ($i + 1) . ' 회차';
			
			$paymentSchedule [] = $monthly;
		}
		return $paymentSchedule;
	}
	/**
	 * 원리금 균등 분할 방식.
	 * 지불금(원리금) 계산.
	 *
	 * @return number 원리금
	 */
	public function calcPaymentOnType1() {
		// C2*C3/12*(1+C3/12)^C4/((1+C3/12)^C4-1)
		// 원금*금리/12*(1+금리/12)^개월/(1+금리/12)^개월-1
		// 대출원금 × 이자율 ÷ 12 × (1 + 이자율 ÷ 12)^기간 ÷((1 + 이자율 ÷ 12)^기간 -1)
		
		// (1 + 이자율 ÷ 12)^기간
		// echo pow((1+ $this->interestRate/12),$this->amortizationPeriod);
		$calc1 = pow ( (1 + $this->interestRate / 12), $this->amortizationPeriod );
		// echo $calc1;
		$paymentPI = ($this->principal * $this->interestRate / 12) * $calc1 / ($calc1 - 1);
		$paymentPI = $this->roundup ( $paymentPI, 0 );
		return $paymentPI;
	}
	/**
	 * 원금 균등 분할 방식.
	 * 스케쥴 계산.
	 */
	public function calcLoanScheduleOnType2() {
		$loanBalance = $this->principal;
		
		// --원금월 지불액 계산. 원금균등방식에서는 이 금액이 일정합니다.
		$principalPaid = $this->principal / $this->amortizationPeriod;
		$principalPaid = $this->rounddown ( $principalPaid, 1 );
		
		$paymentSchedule = array();
		$monthly = array ();
		for($i = 0; $i < $this->amortizationPeriod; $i ++) {
			/*
			 * 이전 잔액 * 연이자율 = 연 이자 금액
			 * 연이자 금액 / 12 = 월 이자 금액 (좀 더 정확하게 할 때에는 31/365 와 같은 식으로 계산)
			 * 이자금액이 월마다 변경이 됨.
			 */
			$interestPaid = $loanBalance * $this->interestRate / 12; // 이자지불액 (변동)
			$interestPaid = $this->roundup ( $interestPaid );
			$principalPaid = $principalPaid; // 원금지불액 (동일)
			$payment = $interestPaid + $principalPaid; // 월지불액 (변동)
			$loanBalance -= $principalPaid; // 원금 잔액
			
			$monthly ['payment'] = $this->format ( $payment );
			$monthly ['principalPaid'] = $this->format ( $principalPaid );
			$monthly ['interestPaid'] = $this->format ( $interestPaid );
			$monthly ['loanBalance'] = $this->format ( $loanBalance );
			$monthly ['index'] = ($i + 1) . ' 회차';
			
			$paymentSchedule [] = $monthly;
		}
		return $paymentSchedule;
	}
	
	/**
	 * 숫자포멧.
	 * 10,000 형식으로 반환한다.
	 * 다른 방식으로 하고 싶다면, override 해서 쓰자.
	 *
	 * @param number $v
	 *        	default 0
	 * @return string
	 */
	public function format($v = 0) {
		return number_format ( $v );
	}
	
	/**
	 * 디버그 값
	 * 
	 * @return string
	 */
	private function getDebug() {
		return $this->debugstring;
	}
	
	/**
	 * 버림
	 * 원단위 절삭(십원이하 절삭) round_down($num,1)
	 * 십원단위 절삭(백원이하 절삭) round_down($num,2)
	 * 백원단위 절삭(천원이하 절삭) round_down($num,3)
	 */
	public function rounddown($num, $d = 0)
	{
		return floor(abs($num) / pow(10, $d)) * pow(10, $d);
	}
	/**
	 * 버림
	 * 원단위 올림(십원이하 올림) round_up($num,1)
	 * 십원단위 올림(백원이하 올림) round_up($num,2)
	 * 백원단위 올림(천원이하 올림) round_up($num,3)
	 */
	public function roundup($num, $d = 0)
	{
		return ceil($num / pow(10, $d)) * pow(10, $d);
	}
	
	/**
	 * 대출원금 지정
	 *
	 * @param integer $_principal        	
	 */
	public function setPrincipal($_principal) {
		$this->principal = $_principal;
		return $this;
	}
	/**
	 * 할부이자
	 *
	 * @param float $_interestRate        	
	 */
	public function setInterestRate($_interestRate) {
		$this->interestRate = $_interestRate;
		return $this;
	}
	/**
	 * 할부기간
	 *
	 * @param float $_amortizationPeriod        	
	 */
	public function setAmortizationPeriod($_amortizationPeriod) {
		if ($_amortizationPeriod >= $this::MAX_TERMS) {
			$_amortizationPeriod = $this::MAX_TERMS;
		}
		$this->amortizationPeriod = $_amortizationPeriod;
		return $this;
	}
	/**
	 * 상환방식
	 * 
	 * @param float $_typeOfPayment      
	 * @return LoanCalculator  	
	 */
	public function setTypeOfPayment($_typeOfPayment) {
		$this->typeOfPayment = $_typeOfPayment;
		return $this;
	}
}
