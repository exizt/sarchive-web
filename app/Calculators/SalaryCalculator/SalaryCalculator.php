<?php
namespace App\Calculators\SalaryCalculator;
/**
 * 연봉 계산기
 * 
 * 핵심 구분을 하자면,<br>
 * Salary : 입력값 과 netSalary (세후 연봉) 등의 연산<br>
 * Insurance : 4대 보험 연산<br>
 * IncomeTax : 주민세, 지방세 연산<br>
 * 
 * @author e2xist
 *
 */
class SalaryCalculator {
	private $options = array ();
	/**
	 * 연봉/월급 계산을 위한 클래스
	 * @var Salary
	 */
	private $salary;
	/**
	 * 보험을 계산하는 클래스
	 * @var Insurance
	 */
	private $insurance;
	/**
	 * 소득세, 지방세를 계산하는 클래스
	 * @var IncomeTax
	 */
	private $incomeTax;
	
	private $netSalary;
	
	/**
	 * 생성자
	 */
	public function __construct() {
		$this->setDefaultOptions();
		
		$this->salary = new Salary ();
		$this->insurance = new Insurance ();
		$this->incomeTax = new IncomeTax();
	}

	/**
	 * 연산 동작
	 * @return SalaryCalculator
	 */
	public function execute() {
	
		// --1. 기준 연봉 연산 (월급인 경우 연봉으로 환산)
		//$salary = new Salary ();
		$this->salary->assign ( $this->options );
		$basicSalary = $this->salary->getBasicSalary ();
		
		// --2. 4대 보험 연산
		//$insurance = new Insurance ();
		$this->insurance->assign ( $basicSalary );
		
		// --3. 세금 연산 (주민세 등)
		// 국민연금 값 대입
		// incometax 계산. 가족수, 아이 수 대입.
		//$incomeTax = new IncomeTax ();
		// $insurance->get('nationalPension');
		$this->incomeTax->assign ( $basicSalary, $this->options ['family'], $this->options ['child'], $this->insurance->get ( 'nationalPension' ) );
		
		// --4. 실수령액 연산
		$netSalary = $basicSalary - $this->insurance->get() - $this->incomeTax->get() + $this->options ['taxExemption'];
		
		$this->salary->setNetSalary($netSalary);
		
		//json_encode($this->generateResult($netSalary));
		return $this;
	}

	/**
	 * 옵션 값을 넘김
	 * @param array $options
	 */
	public function prepare($options= array()) {
		$this->options = array_merge($this->options,$options);
	}
	
	public function prepareOptions($options= array())
	{
		$this->options = array_merge($this->options,$options);
		return $this;
	}

	public function prepareInsuranceRates(InsuranceRates $rates)
	{
		$this->insurance->changeRates($rates);
		return $this;
	}

	
	/**
	 * 결과 배열 리턴
	 * @return array
	 */
	public function generateResult()
	{
		$dataSet = array (
				'netSalary' => $this->salary->getNetSalary(),
				'summary_annualSalary' => $this->salary->getGrossAnnualSalary(),
				'summary_incomeTax' => $this->incomeTax->get(),
				'summary_insurance' => $this->insurance->get(),
				'summary_salary' => $this->salary->getGrossSalary(),
				'summary_taxExemption' => $this->options['taxExemption'],
				'insurance_employmentCare' => $this->insurance->get('employmentCare'),
				'insurance_healthCare' => $this->insurance->get('healthCare'),
				'insurance_longTermCare' => $this->insurance->get('longTermCare'),
				'insurance_nationalPension' => $this->insurance->get('nationalPension'),
				'tax_earned' => $this->incomeTax->get('incomeTax'),
				'tax_local' => $this->incomeTax->get('localTax')
		);
		return $dataSet;
	}

	/**
	 * @var Salary
	 */
	public function getSalary()
	{
		return $this->salary;
	}

	public function getInsurance()
	{
		return $this->insurance;
	}

	public function getIncomeTax()
	{
		return $this->incomeTax;
	}

	/**
	 * 기본 옵션값
	 */
	public function setDefaultOptions()
	{
		$this->options ['inputMoney'] = 2000000; //입력값
		$this->options ['taxExemption'] = 100000; //비과세
		$this->options ['family'] = 1;
		$this->options ['child'] = 0;
		$this->options ['annualBasis'] = false; //입력값이 연봉인지 여부. (false 일 때는 월급으로 가정)
		$this->options ['includedSeverance'] = false; // 퇴직금 포함 여부
		$this->options ['debug'] = false;
	}
}