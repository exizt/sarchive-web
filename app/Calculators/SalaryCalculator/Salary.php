<?php
namespace App\Calculators\SalaryCalculator;

/**
 * 연봉 <-> 월봉 계산을 위한 클래스
 * 
 * 4대보험, 세금 계산의 근거가 되는 금액 을 계산합니다.
 * 
 * getter, setter 중에서 getter 만 존재합니다. 연산으로 값을 계산해서 대입하기 때문.
 * @author Adminn
 *
 */
class Salary
{
	private $inputMoney;
	private $grossSalary;
	private $grossAnnualSalary;
	private $basicSalary;
	private $netSalary;
	
	/**
	 * 생성자
	 */
	public function __construct()
	{
	}
	public function assign($options=array())
	{
		$this->inputMoney = $options['inputMoney'];

		// 연봉 기준 입력인 경우, 월급을 환산
		if ($options['annualBasis'])
		{
			/*
			 * 퇴직금 포함의 연봉인 경우는, 13개월로 나눠야 월소득이 나온다.
			 */
			if ($options['includedSeverance'])
			{
				$this->grossSalary = $this->inputMoney / 13;
			}
			else
			{
				$this->grossSalary = $this->inputMoney / 12;
			}
		}
		else
		{
			$this->grossSalary = $this->inputMoney;
		}
		// 총월급 기준으로 실연봉을 다시 계산한다.
		$this->grossAnnualSalary = $this->grossSalary * 12;
		
		// 세금을 계산할 기준의 월급을 구한다.
		$this->basicSalary = $this->grossSalary - $options['taxExemption'];

	}
	/**
	 * 계산 근거 월봉
	 * @return number
	 */
	public function getBasicSalary()
	{
		return $this->basicSalary;		
	}
	/**
	 * 세전 월봉
	 * @return number
	 */
	public function getGrossSalary()
	{
		return $this->grossSalary;
	}
	/**
	 * 세전 연봉
	 * @return number
	 */
	public function getGrossAnnualSalary()
	{
		return $this->grossAnnualSalary;
	}
	public function setNetSalary($salary)
	{
		$this->netSalary = $salary;
	}
	public function getNetSalary(){
	    return $this->netSalary;
	}
	public function get()
	{
		return $this->netSalary;
	}
}