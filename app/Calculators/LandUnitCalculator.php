<?php
namespace App\Calculators;

class LandUnitCalculator
{
	// --기본변수
	private $result; // 결과
	private $debugstring; // 디버그 문자열
	private $options = array ();
	
	// --변수
	private $pyeong; // 평수
	private $squareMeter; // 평방미터(제곱미터)
	                         
	// --추가변수
	
	/**
	 * 생성자
	 */
	public function __construct()
	{
		// 옵션 초기값
		$this->options ['pyeong'] = '0';
		$this->options ['squareMeter'] = '0';
		
		//초기값
		$this->result = false;
	}
	
	/**
	 * 연산
	 *
	 * @return LandUnitCalculator
	 */
	public function execute()
	{
		$pyeong = $this->options['pyeong'];
		$squareMeter = $this->options['squareMeter'];
		if ($pyeong > 0)
		{
			$squareMeter = $pyeong * 3.305785;
		}
		else
		{
			if ($squareMeter > 0)
			{
				$pyeong = $squareMeter * 0.3025;
			}
		}
		$this->pyeong = $pyeong;
		$this->squareMeter = $squareMeter;
		$this->result = true;
		
		return $this;
	}
	
	/**
	 * 결과 배열 리턴
	 * @return NULL[]|boolean[]
	 */
	public function generateResult()
	{
		$resultSet = array ();
		$resultSet['result'] = $this->result;
		$resultSet['debug'] = $this->getDebug();
		$resultSet['dataSet'] = $this->getDataSet();
		return $resultSet;
	}

	/**
	 *
	 * @return string
	 */
	private function getDataSet()
	{
		$data = array();
		$data['pyeong'] = $this->pyeong;
		$data['squareMeter'] = $this->squareMeter;
		return $data;
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
	 * 디버그 값
	 * @return string
	 */
	private function getDebug()
	{
		return $this->debugstring;
	}
}