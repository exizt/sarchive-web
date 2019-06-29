<?php
namespace App\Calculators;

/**
 * 관련 소스 참고는 https://github.com/true/php-punycode/ 주소에서 참고함.
 * @author Administrator
 *
 */
class HouseCommissionCalculator {
	/**
	 *
	 * @var HouseCommissionCalculator
	 */
	public static $_instance;
	private $options = array ();
	private $debugstring;
	
	// 리턴할 값
	private $result; // 결과
	private $commission; // 중개수수료
	private $commissionTotal;	                        
	private $VAT;
	
	/**
	 * 생성자
	 */
	public function __construct() {
		// 옵션 초기값
		$this->options ['deposit'] = '0';
		$this->options ['monthly_fee'] = '0';
		
		//초기값
		$this->commission = 0;
		$this->VAT = 0;
		$this->commissionTotal = 0;
		$this->result = false;
		
	}
		
	/**
	 * 연산 동작
	 *
	 * @return HouseCommissionCalculator
	 */
	public function execute() {
		$this->commission = $this->calculateCommission ();
		$this->VAT = $this->commission * 0.1;
		$this->commissionTotal = $this->commission + $this->VAT;
		$this->result = true;
		
		return $this;
	}
	
	/**
	 * 주택임대차 계산
	 */
	private function calculateCommission() {
		$deposit = $this->options['deposit'];
		$monthly_fee = $this->options['monthly_fee'];
		
		// 기본적으로 100 요율로 계산.
		$totalMoney = $deposit + $monthly_fee * 100;
		
		// 5천만원 미만 인 경우 70 요율로 재 계산.
		if ($totalMoney <= 50000000) {
			$totalMoney = $deposit + $monthly_fee * 70;
		}
		
		$commissionRate = 0;
		
		// 6억 이상 0.8% (한도 없음)
		if ($totalMoney >= 600000000) {
			$commissionRate = 0.8;
		} else if ($totalMoney >= 300000000) {
			// 3억~6억 구간 0.4% (한도 없음)
			$commissionRate = 0.4;
		} else if ($totalMoney >= 100000000) {
			// 1억~3억 구간 0.3% (한도 없음)
			$commissionRate = 0.3;
		} else if ($totalMoney >= 50000000) {
			// /5천~1억 구간 0.4% (한도 30만)
			$commissionRate = 0.4;
		} else {
			// 5천 미만 구간 0.5% (한도 20만)
			$commissionRate = 0.5;
		}
		$tempCommission = $this->rounddown ( $totalMoney * $commissionRate / 100 );
		return $tempCommission;
	}
	
	/**
	 * 옵션 값을 넘김
	 *
	 * @param array $options
	 */
	public function prepare($options = array()) {
		$this->options = array_merge ( $this->options, $options );
	}
	
	/**
	 * 결과 배열 리턴
	 * 
	 * @return string[]|boolean[]
	 */
	public function generateResult() {
		$resultSet = array ();
		$resultSet ['result'] = $this->result;
		$resultSet ['debug'] = $this->getDebug ();
		$resultSet ['dataSet'] = $this->getDataSet ();
		return $resultSet;
	}
	
	/**
	 *
	 * @return string
	 */
	private function getDataSet() {
		$data = array();
		$data ['commission'] = $this->commission;
		$data ['VAT'] = $this->VAT;
		$data ['commissionTotal'] = $this->commissionTotal;
		return $data;
	}
	
	/**
	 * 디버그 값 
	 * @return string
	 */
	private function getDebug()
	{
		return $this->debugstring;
	}
	
	/**
	 * 버림
	 * 원단위 절삭(십원이하 절삭) rounddown($num,1)
	 * 십원단위 절삭(백원이하 절삭) rounddown($num,2)
	 * 백원단위 절삭(천원이하 절삭) rounddown($num,3)
	 */
	public function rounddown($num, $d = 0)
	{
		return floor($num / pow(10, $d)) * pow(10, $d);
	}
}