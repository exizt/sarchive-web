<?php

namespace App\Http\Controllers\Softwares;

use App\Http\Controllers\Controller;
use App\Models\SoftwareProduct;

/**
 * 차후에 미리보기 이미지 업로드 기능 추가 될 것
 *
 * @author Adminn
 *        
 */
class IOS_Controller extends Controller {
	protected const VIEW_PATH = 'site.softwares.ios';
	private $appData = array();
	
	// 기본데이터 지정
	public function __construct(){
		$incomeTax = array();
		$incomeTax['disqus_pid'] = '/ios/incometax';
		$incomeTax['link_privacy'] = '/ios/korea-salary-income-calculator-for-ios/privacy';
		$incomeTax['link_apppage'] = '/ios/korea-salary-income-calculator-for-ios';
		$incomeTax['link_store'] = 'https://appsto.re/kr/bX_Okb.i';
		$this->appData['incomeTax'] = $incomeTax;
	}

	/**
	 * 
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function income_tax(){
		$data = array();
		$data ['disqus_pid'] = $this->appData['incomeTax']['disqus_pid'];
		$data ['link_privacy'] = $this->appData['incomeTax']['link_privacy'];
		$data ['link_store'] = $this->appData['incomeTax']['link_store'];
		return view ( self::VIEW_PATH.'.korea_income_tax_calculator', $data );
	}
	
	/**
	 * 
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function income_tax_privacy(){
		$data = array();
		
		//gets privacy text file
		//$contents = File::get(storage_path('text/ios_income_tax_privacy.txt'));
		//$contents = Storage::get('ios_income_tax_privacy.txt');
		$record = SoftwareProduct::where('software_sku','SHNSWR-APIPH-AL001')->firstOrFail();
		$contents = $record->privacy_statement;
		
		
		$contents = nl2br($contents);
		$data['contents'] = $contents;
		$data['link_apppage'] = $this->appData['incomeTax']['link_apppage'];
		
		// call view
		return view ( self::VIEW_PATH.'.korea_income_tax_calculator_privacy', $data );
	}
}

