<?php
namespace App\Http\Controllers\Services\Calculators;

use App\Calculators\SalaryCalculator\SalaryCalculator;
use App\Http\Controllers\Controller;
use App\Models\IncomeSalaryCalculator\ISC_IncomeTaxTable;
use App\Models\IncomeSalaryCalculator\ISC_InsuranceTaxRates;
use App\Models\IncomeSalaryCalculator\ISC_TerminologyInformation;
use Illuminate\Http\Request;
use \Validator;

/**
 * 연봉 계산기
 *
 * @author Adminn
 *        
 */
class SalaryCalculatorController extends Controller {

	protected const VIEW_PATH = 'site.services.calculators';

	private $calculator;

	/**
	 * 생성자
	 */
	public function __construct() {
		$this->calculator = new SalaryCalculator();
	}

	/**
	 * 첫 페이지
	 */
	public function index(Request $request) {
		$data = array();

		return view(self::VIEW_PATH . '.salary_calculator', $data);
	}

	/**
	 * 계산 결과, 설명글 등을 위한 Ajax 메서드
	 */
	public function store(Request $request) {
		$mode = $request->input('mode');
		if ($mode == 'run') {
			return $this->calcuate($request);
		} else if ($mode == 'ajax_description') {
			$this->ajax_description($request);
		}
	}

	/**
	 * 실수령액 계산하는 메서드
	 * 
	 * Ajax 요청이 들어오면 Json 으로 결과값을 리턴한다.
	 */
	private function calcuate(Request $request) {
		/*
		$request->validate([
			'inputMoney' => 'required',
			'taxExemption' => 'required',
			'family' => 'required|max:2',
			'child' => 'required|max:2'
		]);
		*/
		
		$attributes = [
			'inputMoney' => '입력 금액',
			'taxExemption' => '비과세',
			'family' => '부양가족수',
			'child' => '20세이하자녀수'
		];
		
		$validator = Validator::make($request->all(), [
			'inputMoney' => 'required',
			'taxExemption' => 'required',
			'family' => 'required|max:2',
			'child' => 'required|max:2'
		], [], $attributes);
		
		if ($validator->fails()){
			//return;
			
			return response()->json([
				'success' => 'false',
				'errors'  => $validator->errors()->all(),
			],422);
			
		} else {
		}
		$message = '';

		// 비과세
		$taxExemption = $request->input('taxExemption',0);
		$family = $request->input('family',1);

		$options = array(
			'inputMoney' => $request->input('inputMoney',0),
			'taxExemption' => $taxExemption,
			'family' => $family,
			'child' => $request->input('child',0),
			'annualBasis' => filter_var($request->input('annualBasis', false), FILTER_VALIDATE_BOOLEAN),
			'includedSeverance' => filter_var($request->input('includedSeverance', false), FILTER_VALIDATE_BOOLEAN)
		);
		$Salary = $this->calculator->getSalary();
		$Insurance = $this->calculator->getInsurance();
		$IncomeTax = $this->calculator->getIncomeTax();

		// 준비
		$insuranceRates = new \App\Calculators\SalaryCalculator\InsuranceRates();
		$dbRates = ISC_InsuranceTaxRates::getNowRates();
		
		// 세율을 데이터베이스 에서 조회할 수 있게 변경할 필요가 있음.
		//$insuranceRates->nationalPension = 4.5; // < 국민연금율
		//$insuranceRates->healthCare = 3.23; // < 건강보험료율
		//$insuranceRates->longTermCare = 8.51; // <장기요양료율
		//$insuranceRates->employmentCare = 0.65; // < 고용보험율
		
		$insuranceRates->nationalPension = $dbRates->national_pension; // < 국민연금율
		$insuranceRates->healthCare = $dbRates->health_care; // < 건강보험료율
		$insuranceRates->longTermCare = $dbRates->long_term_care; // <장기요양료율
		$insuranceRates->employmentCare = $dbRates->employment_care; // < 고용보험율
		
		$this->calculator->prepareOptions($options)->prepareInsuranceRates($insuranceRates);

		// 계산 실행
		// $resultSet = $this->calculator->execute ()->generateResult ();

		// --1. 기준 연봉 연산 (월급인 경우 연봉으로 환산)
		// $salary = new Salary ();
		$Salary->assign($options);
		$basicSalary = $Salary->getBasicSalary();

		// --2. 4대 보험 연산
		// $insurance = new Insurance ();
		$Insurance->assign($basicSalary);

		// --3. 세금 연산 (주민세 등)
		// 국민연금 값 대입
		// incometax 계산. 가족수, 아이수 대입.
		// $incomeTax = new IncomeTax ();
		// $insurance->get('nationalPension');
		// $this->incomeTax->assign ( $basicSalary, $this->options ['family'], $this->options ['child'], $this->insurance->get ( 'nationalPension' ) );
		// 여기서 데이터베이스 커넥션이 필요함.
		$basicSalaryT = $basicSalary / 1000;
		$taxRecords = ISC_IncomeTaxTable::where('money_start', '<=', $basicSalaryT)->where('money_end', '>', $basicSalaryT)->first();

		if($family > 11) $family = 11;
		if($family < 1) $family = 1;
		
		$iTax = 0;

		if ($taxRecords) {
			// 결과값이 있을 때
			$iTax = $taxRecords->{'tax_' . $family};
			// $iTax = $dataRecord['tax_'.$family];
		
		} else {
			// 결과값이 없을 때
			// 기준표 보다 큰 금액일 경우가 있음. 이 경우는 계산식을 이용한다.
			$tRecord = ISC_IncomeTaxTable::where('money_end', 0)->first()->toArray();
			if ($basicSalaryT >= $tRecord['money_start']) {
				// 특정 금액을 넘어서는 경우인지 확인. 보통 1천만원을 의미. 디비에서는 money_end 값에 0 을 넣어서 구분.
				// $tRecord['tax_'.$family]; //최소 소득세 금액이 됨.
				$iTax = $this->calculateLargeIncomeTax($basicSalaryT, $tRecord['tax_' . $family]);
			}
		}
		// echo $iTax;
		$IncomeTax->setIncomeTax($iTax);

		// --4. 실수령액 연산
		$netSalary = $basicSalary - $Insurance->get() - $IncomeTax->get() + $taxExemption;
		$Salary->setNetSalary($netSalary);

		$resultSet = array(
			'netSalary' => $Salary->getNetSalary(),
			'summary_annualSalary' => $Salary->getGrossAnnualSalary(),
			'summary_incomeTax' => $IncomeTax->get(),
			'summary_insurance' => $Insurance->get(),
			'summary_salary' => $Salary->getGrossSalary(),
			'summary_taxExemption' => $taxExemption,
			'insurance_employmentCare' => $Insurance->get('employmentCare'),
			'insurance_healthCare' => $Insurance->get('healthCare'),
			'insurance_longTermCare' => $Insurance->get('longTermCare'),
			'insurance_nationalPension' => $Insurance->get('nationalPension'),
			'tax_earned' => $IncomeTax->get('incomeTax'),
			'tax_local' => $IncomeTax->get('localTax'),
			'summary' => array(
				'annualSalary' => $Salary->getGrossAnnualSalary(),
				'incomeTax' => $IncomeTax->get(),
				'insurance' => $Insurance->get(),
				'salary' => $Salary->getGrossSalary(),
				'taxExemption' => $taxExemption
			),
			'message' => $message
		);

		//echo json_encode($resultSet);
		return response()->json($resultSet);
	}

	/**
	 * 간이 세액표 에 없는 초과금액에 대한 계산
	 * @param number $iBasicSalary
	 *        	과세 표준 (의미 : 세금 기준 금액)(단위 1000)
	 * @param number $minTax
	 *        	(최소 세금)
	 */
	public function calculateLargeIncomeTax($iBasicSalary, $minTax) {
		$yearmonth = 201802;
		$basicSalary = $iBasicSalary * 1000;
		$resultTax = 0;
		switch ($yearmonth) {
			case 201802:
				if ($basicSalary <= 14000 * 1000) {
					$resultTax = $minTax + ($basicSalary - 10000 * 1000) * 0.98 * 0.35;
				} else if ($basicSalary <= 28000 * 1000) {
					$resultTax = $minTax + 1372000 + ($basicSalary - 14000 * 1000) * 0.98 * 0.38;
				} else if ($basicSalary <= 45000 * 1000) {
					$resultTax = $minTax + 6585600 + ($basicSalary - 28000 * 1000) * 0.98 * 0.40;
				} else {
					$resultTax = $minTax + 13249600 + ($basicSalary - 45000 * 1000) * 0.98 * 0.42;
				}
				;
				break;
			case 201702:
				if ($basicSalary <= 14000 * 1000) {
					$resultTax = $minTax + ($basicSalary - 10000 * 1000) * 0.98 * 0.35;
				} else if ($basicSalary <= 45000 * 1000) {
					$resultTax = $minTax + 1372000 + ($basicSalary - 28000 * 1000) * 0.98 * 0.40;
				} else {
					$resultTax = $minTax + 12916400 + ($basicSalary - 45000 * 1000) * 0.98 * 0.42;
				}
				;
				break;
			default:
				;
				break;
		}
		return $resultTax;
	}

	public function getDescription($cid){
		$result = ISC_TerminologyInformation::where('cid',$cid)->first();
		return $result;
	}
	
	/**
	 *
	 * @return string
	 */
	public function ajax_description(Request $request) {
		$cid = $request->input('cid');
		
		
		$result = $this->getDescription($cid);
		
		if(!$result){
			return 'not found';
			
		} else {
			$resultSet = $result->toArray();
			echo json_encode($resultSet);
		}
	}
	
	/**
	 * 실수령액 계산
	 */
	public function run(Request $request) {
		$taxExemption = $request->input('taxExemption');
		
		$options = array(
			'inputMoney' => $request->input('inputMoney'),
			'taxExemption' => $taxExemption,
			'family' => $request->input('family'),
			'child' => $request->input('child'),
			'annualBasis' => $request->input('annualbasis', false),
			'includedSeverance' => $request->input('includedseverance', false)
		);
		
		// 준비
		$insuranceRates = new \App\Calculators\SalaryCalculator\InsuranceRates();
		// 세율을 데이터베이스 에서 조회할 수 있게 변경할 필요가 있음.
		$insuranceRates->nationalPension = 4.5; // < 국민연금율
		$insuranceRates->healthCare = 3.23; // < 건강보험료율
		$insuranceRates->longTermCare = 8.51; // <장기요양료율
		$insuranceRates->employmentCare = 0.65; // < 고용보험율
		$this->calculator->prepareOptions($options)->prepareInsuranceRates($insuranceRates);
		
		// 계산 실행
		$resultSet = $this->calculator->execute()->generateResult();
		
		echo json_encode($resultSet);
	}

	
	/**
	 *
	 * @return string
	 */
	public function ajax_description_fromXML(Request $request) {
		$path = app_path() . DIRECTORY_SEPARATOR . 'salary_description.xml';
		
		$content_id = $request->input('cid');
		
		$listKeys = array(
			'desc_insurance_nation' => 'nationalPension',
			'desc_insurance_healthcare' => 'healthCare',
			'desc_insurance_longtermcare' => 'longTermCare',
			'desc_insurance_employee' => 'employmentCare',
			'desc_tax_earned' => 'incomeTax',
			'desc_tax_earnedlocal' => 'incomeLocalTax',
			'1' => 'occupationalInsurance',
			'2' => 'deduction',
			'3' => 'taxCredit'
		);
		if (file_exists($path)) {
			$xml = simplexml_load_file($path);
			
			// $xmlstring = file_get_contents(APPPATH.'android.xml',true);
			// $xml = simplexml_load_string ( $xmlstring );
			$item = new \stdClass();
			foreach ($xml as $k => $v) {
				if ($v->id == $listKeys[$content_id]) {
					// print_r ( $v );
					$item = $v;
				}
			}
			print_r($item->asXML());
			// print_r($xml);
			// $json = json_encode($xml);
			// echo $xmlstring;
			// echo xml_convert($xmlstring);
			// echo $result;
		} else {
			return 'not found';
		}
	}
	
}
