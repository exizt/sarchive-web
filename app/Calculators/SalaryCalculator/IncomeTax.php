<?php
namespace App\Calculators\SalaryCalculator;

/**
 * 소득세, 지방세 에 대한 계산 클래스
 */
class IncomeTax {
    
	/**
	 * 소득세
	 *
	 * @var integer
	 */
	private $incomeTax;
	
	/**
	 * 지방세
	 *
	 * @var integer
	 */
	private $localTax;
	
	/**
	 * 생성자
	 */
	public function __construct() {
	    $this->incomeTax = 0;
	    $this->localTax = 0;
	}

	/**
	 * 수동으로 계산하기.
	 * incomeTax 계산과 동시에 localTax 가 되도록 변경함.
	 */
	public function assign($salary, $family, $child, $np) {
		$this->assignIncomeTax ( $salary, $family, $child, $np );
		//$this->assignLocalTax ();
	}

	/**
	 * 소득세 대입하기
	 * 소득세를 대입하면서, 지방세 도 같이 계산함.
	 */
	public function setIncomeTax($incomeTax)
	{
		$this->incomeTax = $incomeTax;
		$this->localTax = $this->calculateLocalTax($incomeTax);
	}

	/**
	 * 지방세 계산하기 (소득세를 통한 계산)
	 *
	 * 근로소득세의 10%
	 * 참고) 원단위 절사
	 *
	 * @return number
	 */
	public function calculateLocalTax($incomeTax)
	{
		$res = $incomeTax * 0.1;
		
		// 절사 처리 (원단위 절사)
		return floor ( $res / 10 ) * 10;
	}

	/**
	 * 소득세 (근로소득세) 수동 계산.
	 *
	 * 산출 순서
	 * 1. 연산 기준에 맞춰서 절삭 및 보정
	 * 2. 연간 근로소득금액 산출 (소득공제)
	 * (연간 근로소득금액 = 연간급여 - 비과세 소득(0) - 근로소득공제)
	 * 3. 종합소득공제 산출
	 * (인적공제, 연금보험료공제, 특별소득공제 등)
	 * 4. 과세표준 산출
	 * (근로소득과세표준 = 근로소득금액 - 종합소득공제 )
	 * 5. 결정세액 산출
	 * 6. 간이세액 산출
	 * (월간 세액 및 원단위 절사)
	 *
	 * @todo 국민연금 금액 갖고 올 방법 고민
	 * @param number $salary
	 */
	private function assignIncomeTax($salary, $family, $child, $np) {
		// 1. 연산기준에 맞춰서 조정
		$baseSalary = $this->ict_adjustSalary ( $salary );
		
		// 2.연간 근로소득금액 산출
		$earned1 = $this->calculateBasicDeduction ( $baseSalary );
		
		// 3. 종합소득공제 산출(인적공제, 연금보험료공제, 특별소득공제 등)
		$deduction = $this->calculateIntegratedDeduction ( $baseSalary, $family, $child, $np );
		
		// 4. 과세표준 산출
		$taxBaseEarned = $earned1 - $deduction;
		
		// 5. 결정세액 산출
		$tax = $this->calcTaxEarnedTotal ( $baseSalary, $taxBaseEarned );
		
		// 6. 간이세액 산출
		// (산출세액 - 세액공제) / 12 = 간이세액
		$simplified_tax = $tax / 12;
		// 원단위 이하 절사
		$simplified_tax = floor ( $simplified_tax / 10 ) * 10;
		// 마이너스 방지
		if ($simplified_tax < 0)
			$simplified_tax = 0;
		
		$this->debug ( "연산 기준에 맞춰서 조정 :" .$baseSalary );
		$this->debug ( "연간근로소득금액(기초공제 후):" .$earned1 );
		$this->debug ( "종합소득공제액:" .$deduction );
		$this->debug ( "과세표준:" .$taxBaseEarned );
		$this->debug ( "결정세액:" .$tax );
		$this->debug ( "간이세액:" .$simplified_tax );
		
		//$this->incomeTax = $simplified_tax;
		$this->setIncomeTax($simplified_tax);
	}
	
	/**
	 * 연산기준 산출
	 *
	 *
	 * 150만원 까지는 5000원 단위 간격
	 * 300만원 까지는 10000원 단위 간격
	 * 1000만원 까지는 20000원 단위 간격
	 *
	 * @param integer $salary        	
	 * @return number
	 */
	private function ict_adjustSalary($salary) {
		if ($salary <= 1500 * 1000) {
			if ($salary % 10000 > 5000) {
				$salary = floor ( $salary / 10000 ) * 10000 + 7500;
			} else {
				$salary = floor ( $salary / 10000 ) * 10000 + 2500;
			}
		} else if ($salary <= 3000 * 1000) {
			$salary = floor ( $salary / 10000 ) * 10000 + 5000;
		} else if ($salary <= 10000 * 1000) {
			if (($salary + 1) % 20000 > 0) {
				$salary = floor ( $salary / 20000 ) * 20000 + 10000;
			} else {
				$salary = floor ( $salary / 20000 ) * 20000;
			}
		}
		return $salary;
	}
	
	/**
	 * [소득세 계산 : 근로소득 금액 산출(근로소득 기초공제 후 남는 근로소득금액(연간)]
	 * 근로소득공제를 제한 후의 연간근로소득금액을 구합니다.
	 *
	 * @return
	 *
	 */
	private function calculateBasicDeduction($salary = 0) {
		/*
		 * 1)기준 근로소득 공제 산출
		 * 연간의 기준 근로소득을 계산한 후, 그 금액에 따른 차등적인 소득공제를 한다.
		 */
		$earnedIncomeBefore = $salary * 12; // 연간 기준 금액 산출
		$this->debug ( "소득기준금액-공제전(연기준)". $earnedIncomeBefore );
		$deduction = 0;
		if ($earnedIncomeBefore <= 500 * 10000) {
			$deduction = $earnedIncomeBefore * 0.7;
		} else if ($earnedIncomeBefore <= 1500 * 10000) {
			$deduction = 350 * 10000 + ($earnedIncomeBefore - 500 * 10000) * 0.4;
		} else if ($earnedIncomeBefore <= 4500 * 10000) {
			$deduction = 750 * 10000 + ($earnedIncomeBefore - 1500 * 10000) * 0.15;
		} else if ($earnedIncomeBefore <= 1 * 10000 * 10000) {
			$deduction = 1200 * 10000 + ($earnedIncomeBefore - 4500 * 10000) * 0.05;
		} else {
			$deduction = 1475 * 10000 + ($earnedIncomeBefore - 1 * 10000 * 10000) * 0.02;
		}
		
		// 2)줄어든 근로소득금액 산출
		// 근로소득 금액(연간) = 기존의 기준 근로소득 금액 - 근로소득공제
		$adjustedIncomeYearly = $earnedIncomeBefore - $deduction; // 근로소득금액
		
		$this->debug ( "근로소득공제액(연기준):" . $deduction );
		$this->debug ( "소득기준금액-근로소득공제 후(연기준):" . $adjustedIncomeYearly );
		
		return $adjustedIncomeYearly;
	}
	
	/**
	 * 종합소득공제 산출
	 *
	 * 인적공제, 연금보험료공제, 특별소득공제 등 의 합계를 반환
	 * 이 공제 계산식이 틀리면, 전체적으로 틀어지므로. 간이세액표 를 활용하는 것이 좋을 수도 있음.
	 *
	 * @return
	 *
	 */
	private function calculateIntegratedDeduction($salary, $family, $child, $np) {
		// 1) 인적공제
		$familyDeduction = 150 * 10000 * ($family + $child);
		
		// 2) 연금보험 공제
		$pensionDeduction = $np * 12;
		
		// 3) 특별소득공제
		$deductionEarnedETC = $this->calculateOtherDeduction ( $salary, $family, $child );
		
		$this->debug ( "인적공제:" . $familyDeduction );
		$this->debug ( "연금보험료공제:" . $pensionDeduction );
		$this->debug ( "특별소득공제:" . $deductionEarnedETC );
		
		return $familyDeduction + $pensionDeduction + $deductionEarnedETC;
	}
	
	/**
	 * 소득세 중 산출세액
	 *
	 * @return
	 *
	 */
	private function calcTaxEarnedTotal($baseSalary, $taxBase) {
		$tax = 0;
		
		// 세금구간에 따라서, 소득세의 비율 차등 조정
		if ($taxBase <= 1200 * 10000) {
			$tax = $taxBase * 0.06;
		} else if ($taxBase <= 4600 * 10000) {
			$tax = 72 * 10000 + ($taxBase - 1200 * 10000) * 0.15;
		} else if ($taxBase <= 8800 * 10000) {
			$tax = 582 * 10000 + ($taxBase - 4600 * 10000) * 0.24;
		} else if ($taxBase <= 15000 * 10000) {
			$tax = 1590 * 10000 + ($taxBase - 8800 * 10000) * 0.35;
		} else {
			$tax = 3760 * 10000 + ($taxBase - 15000 * 10000) * 0.38;
		}
		$tax = floor ( $tax / 10 ) * 10; // 원단위 이하 절사
		                                 
		// 근로소득세액공제 처리
		$incomeTaxCredit = $this->calculateTaxCredit ( $baseSalary, $tax );
		
		$this->debug ( "산출세액:" . $tax );
		$this->debug ( "근로소득세액공제:" . $incomeTaxCredit );
		$tax = $tax - $incomeTaxCredit;
		return $tax;
	}
	
	/**
	 * 소득세 중 특별소득공제등
	 *
	 * @return
	 *
	 */
	private function calculateOtherDeduction($baseSalary, $family, $child) {
		$salaryY = $baseSalary * 12;
		$calcFamily = $family + $child;
		
		$deduct = 0;
		if ($calcFamily >= 3) {
			// 공제대상자 3명 이상인 경우
			if ($salaryY <= 3000 * 10000) {
				$deduct = 500 * 10000 + $salaryY * 0.07;
			} else if ($salaryY <= 4500 * 10000) {
				$deduct = 500 * 10000 + $salaryY * 0.07 - ($salaryY - 3000 * 10000) * 0.05;
			} else if ($salaryY <= 7000 * 10000) {
				$deduct = 500 * 10000 + $salaryY * 0.05;
			} else if ($salaryY <= 12000 * 10000) {
				$deduct = 500 * 10000 + $salaryY * 0.03;
			} else {
				$deduct = 0;
			}
			// 추가공제
			if ($salaryY >= 4000) {
				$deduct += ($salaryY - 4000 * 10000) * 0.04;
			}
		} else {
			// 공제대상자 2명 이하인 경우
			if ($salaryY <= 3000 * 10000) {
				$deduct = 360 * 10000 + $salaryY * 0.04;
			} else if ($salaryY <= 4500 * 10000) {
				$deduct = 360 * 10000 + $salaryY * 0.04 - ($salaryY - 3000 * 10000) * 0.05;
			} else if ($salaryY <= 7000 * 10000) {
				$deduct = 360 * 10000 + $salaryY * 0.02;
			} else if ($salaryY <= 12000 * 10000) {
				$deduct = 360 * 10000 + $salaryY * 0.01;
			} else {
				$deduct = 0;
			}
		}
		return $deduct;
	}
	
	/**
	 * 근로 소득 세액공제 계산식
	 *
	 * @return
	 *
	 */
	private function calculateTaxCredit($baseSalary, $tax) {
		$salaryY = $baseSalary * 12;
		$taxCredit = 0;
		
		// 근로소득세액공제 처리
		if ($tax <= 50 * 10000) {
			$taxCredit = $tax * 0.55;
		} else {
			$taxCredit = 275 * 1000 + ($tax - 50 * 10000) * 0.30;
		}
		
		// 근로소득세액공제 한도 지정
		$creditMax = 0;
		if ($salaryY <= 5500 * 10000) {
			$creditMax = 660000;
		} else if ($salaryY <= 7000 * 10000) {
			$creditMax = 630000;
		} else if ($salaryY > 7000 * 10000) {
			$creditMax = 500000;
		}
		// 한도를 넘었을 시 한도 내로 재 지정
		if ($taxCredit >= $creditMax) {
			$taxCredit = $creditMax;
		}
		
		// 원단위 이하 절사
		$taxCredit = floor ( $taxCredit / 10 ) * 10;
		
		return $taxCredit;
	}
	
	/**
	 * 디버그 용 메서드
	 *
	 * 로그 가 가능하다면 로그로 바꿀 것
	 *
	 * @param string $msg        	
	 */
	private function debug($msg) {
		$debug_mode = false;
		if ($debug_mode) {
			echo $msg;
		}
	}
	
	/**
	 * 값 가져올 때 쓰는 함수
	 *
	 * @param string $id        	
	 * @return number|NULL
	 */
	public function get($id = null) {
		switch ($id) {
			case 'incomeTax' :
				return $this->incomeTax;
				break;
			case 'localTax' :
				return $this->localTax;
				break;
			case null :
				return $this->incomeTax + $this->localTax;
				break;
			default :
				return null;
				;
				break;
		}
	}
}