<?php
namespace App\Calculators\SalaryCalculator;

class InsuranceRates
{
	public $nationalPension; //< 국민연금율
	public $healthCare; //< 건강보험료율
	public $longTermCare; //<장기요양료율
	public $employmentCare; //< 고용보험율
}
class Insurance {
	private $Rates;
	/**
	 * 국민연금
	 *
	 * @var integer
	 */
	private $nationalPension;
	/**
	 * 건강보험
	 *
	 * @var integer
	 */
	private $healthCare;
	/**
	 * 장기요양보험
	 *
	 * @var integer
	 */
	private $longTermCare;
	/**
	 * 고용보험
	 *
	 * @var integer
	 */
	private $employmentCare;
	
	/**
	 * 생성자
	 */
	public function __construct() {
		$this->Rates = new InsuranceRates();

		//기본 세율값
		$this->Rates->nationalPension = 4.5; //< 국민연금율
		$this->Rates->healthCare = 3.23; //< 건강보험료율
		$this->Rates->longTermCare = 8.51; //<장기요양료율
		$this->Rates->employmentCare = 0.65; //< 고용보험율

	}
	
	/**
	 * 세율 변경
	 */
	public function changeRates(InsuranceRates $rates)
	{
		$this->Rates->nationalPension = $rates->nationalPension;
		$this->Rates->healthCare = $rates->healthCare;
		$this->Rates->longTermCare = $rates->longTermCare;
		$this->Rates->employmentCare = $rates->employmentCare;
	}

	/**
	 * 값 계산
	 *
	 * @param number $salary        	
	 */
	public function assign($salary = 0) {
		$this->assignNationalPension ( $salary );
		$this->assignHealthCareWithLongTermCare ( $salary );
		$this->assignEmploymentCare ( $salary );
	}
	/**
	 * 국민연금 계산
	 *
	 * @param number $salary
	 *        	세금기준액
	 */
	private function assignNationalPension($salary = 0) {
		
		// 최소값 보정
		if ($salary < 300000) {
			$salary = 300000;
		}
		// 최대값 보정
		if ($salary > 4680000) {
			$salary = 4680000;
		}
		
		// 소득월액 천원미만 절사
		$adjustedSalary = floor ( $salary / 1000 ) * 1000;
		
		// 연산식
		$result = $adjustedSalary * 0.01 * $this->Rates->nationalPension;
		
		// 보험료값 십원 미만 단위 절사
		$this->nationalPension = floor ( $result / 10 ) * 10;
	}
	
	/**
	 * 건강보험 및 장기요양보험 계산
	 *
	 * 장기요양보험 계산시에 건강보험이 미리 계산되어 있어야 하므로,
	 * 같이 처리하는 것이 편함.
	 *
	 * @param number $salary        	
	 */
	private function assignHealthCareWithLongTermCare($salary = 0) {
		$this->assignHealthCare ( $salary );
		$this->assignLongTermCare ();
	}

	/**
	 * 건강보험 계산
	 *
	 * @param number $salary
	 *        	세금기준액
	 */
	private function assignHealthCare($salary = 0) {
		$result = $salary * 0.01 * $this->Rates->healthCare;
		
		if($result >= 3182760){
			$result = 3182760;
		}
		// 보험료값 십원 미만 단위 절사
		$this->healthCare = floor ( $result / 10 ) * 10;
	}
	
	/**
	 * 장기요양보험 계산식
	 *
	 * 반드시, 건강보험 이 먼저 계산되어 있어야 함
	 *
	 * @return
	 *
	 */
	private function assignLongTermCare() {
		$result = $this->healthCare * 0.01 * $this->Rates->longTermCare;
		
		// 십원 미만 단위 절사
		$this->longTermCare = floor ( $result / 10 ) * 10;
	}

	/**
	 * 고용보험 계산
	 *
	 * @return
	 *
	 */
	private function assignEmploymentCare($salary = 0) {
		$result = $salary * 0.01 * $this->Rates->employmentCare;
		
		// 십원 미만 단위 절사
		$this->employmentCare = floor ( $result / 10 ) * 10;
	}

	/**
	 * 값 가져올 때 쓰는 함수
	 * @param string $id
	 * @return number|NULL
	 */
	public function get($id=null) {
		switch ($id) {
			case 'nationalPension' :
				return $this->nationalPension;
				break;
			case 'employmentCare' :
				return $this->employmentCare;
				break;
			case 'healthCare' :
				return $this->healthCare;
				break;
			case 'longTermCare' :
				return $this->longTermCare;
				break;
			case null :
				return $this->nationalPension + $this->employmentCare + $this->healthCare + $this->longTermCare;
				break;
			default :
				return null;
				;
				break;
		}
	}
}