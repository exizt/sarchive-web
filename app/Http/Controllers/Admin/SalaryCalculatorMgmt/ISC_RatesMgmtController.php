<?php

namespace App\Http\Controllers\Admin\SalaryCalculatorMgmt;

use App\Http\Controllers\Admin\AdminController;
use App\Models\IncomeSalaryCalculator\ISC_InsuranceTaxRates;
use Illuminate\Http\Request;

/**
 * 세율 정보 관리 
 * @author e2xist
 * 최초작성일 2019-02-28
 */
class ISC_RatesMgmtController extends AdminController
{
	/**
	 * view 의 경로.
	 */
	protected const VIEW_PATH = 'admin.isc_mgmt.rate_mgmt';
	/**
	 * routes/web 에 지정된 값
	 */
	protected const ROUTE_ID = 'admin.isc_rateMgmt';
	
	/**
	 * 생성자
	 */
	public function __construct() {
		$this->middleware('auth');
	}
	
	/**
	 * 목록 조회
	 */
	public function index(Request $request)
	{
		$masterRecords = ISC_InsuranceTaxRates::orderBy('yearmonth','desc')
		->paginate(15);
		
		$data = $this->createViewData ();
		
		//$test = ISC_InsuranceTaxRates::where('yearmonth','<=','201806')->orderBy('yearmonth','desc')->first();
		//print_r($test);
		
		//$test2 = ISC_InsuranceTaxRates::getNowRates();
		//print_r($test2);
		
		$data['masterRecords'] = $masterRecords;
		return view(self::VIEW_PATH.'.index', $data);
	}
	
	public function show($id)
	{
		$masterData = ISC_InsuranceTaxRates::where('id',$id)->firstOrFail();
		$data = $this->createViewData ();
		$data['item'] = $masterData;
		
		
		return view(self::VIEW_PATH.'.show', $data);
	}
	
	/**
	 * Show the new post form
	 */
	public function create()
	{
		$data = $this->createViewData ();
		return view(self::VIEW_PATH.'.create', $data);
	}
	
	/**
	 * Store a newly created Post
	 *
	 * @param Request $request
	 */
	public function store(Request $request)
	{
		if ($request->is('admin/*')) {
			
			$item = new ISC_InsuranceTaxRates;
			
			// saving
			$item->yearmonth = $request->input('yearmonth');
			$item->national_pension = $request->input('national_pension');
			$item->health_care = $request->input('health_care');
			$item->long_term_care = $request->input('long_term_care');
			$item->employment_care = $request->input('employment_care');
			$item->save();
			
			//return redirect()->route(self::ROUTE_ID.'.index')->withSuccess('Post saved.');
			return redirect ()->route ( self::ROUTE_ID . '.show', $item->id)->with ('message', '생성이 완료되었습니다.' );
			
		} else {
			//failed
		}
	}
	
	/**
	 * 정보 변경하기
	 *
	 * @param  int  $id
	 */
	public function edit($id)
	{
		$masterData = ISC_InsuranceTaxRates::where('id',$id)->firstOrFail();
		$data = $this->createViewData ();
		$data['item'] = $masterData;
		return view(self::VIEW_PATH.'.edit',$data);
	}
	
	/**
	 * 변경 처리 하기.
	 *
	 * @param Request $request
	 * @param int $id
	 */
	public function update(Request $request, $id)
	{
		if ($request->is('admin/*')) {
			// 있는 값인지 id 체크
			$item = ISC_InsuranceTaxRates::findOrFail($id);
			
			// saving
			//$item->yearmonth = $request->input('yearmonth');
			$item->national_pension = $request->input('national_pension');
			$item->health_care = $request->input('health_care');
			$item->long_term_care = $request->input('long_term_care');
			$item->employment_care = $request->input('employment_care');
			$item->save();
			
			// after processing
			if ($request->action === 'continue') {
				return redirect()->back()->with ('message', '변경이 완료되었습니다.' );
			}
			//return redirect()->route(self::ROUTE_ID.'.index')->withSuccess('Post saved.');
			return redirect ()->route ( self::ROUTE_ID . '.show', $id)->with ('message', '변경이 완료되었습니다.' );
			
		} else {
			//failed
		}
	}
	
	/**
	 * 데이터 삭제
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		$item = ISC_InsuranceTaxRates::findOrFail($id);
		$item->delete();
		
		return redirect()
		->route(self::ROUTE_ID.'.index')
		->with ('message', '삭제 처리되었습니다. item_id['. $id.']' );
	}
}
