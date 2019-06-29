<?php

namespace App\Http\Controllers\Admin\SalaryCalculatorMgmt;

use App\Http\Controllers\Admin\AdminController;
use App\Models\IncomeSalaryCalculator\ISC_TerminologyInformation;
use Illuminate\Http\Request;

/**
 * 용어 사전 관리 for 실수령액 계산기
 * @author e2xist
 * 최초작성일 2019-02-27
 * @todo '작은 따옴표' 값이 저장될 때, 다른 값으로 치환할 필요가 있음. sqlite 로의 변환 할 때 자꾸 까다롭다...
 */
class ISC_TermsMgmtController extends AdminController
{
	/**
	 * view 의 경로.
	 */
	protected const VIEW_PATH = 'admin.isc_mgmt.term_mgmt';
	/**
	 * routes/web 에 지정된 값
	 */
	protected const ROUTE_ID = 'admin.isc_termMgmt';
	
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
		$masterRecords = ISC_TerminologyInformation::orderBy('id','asc')
		->paginate(10);
		
		$data = $this->createViewData ();
		
		$data['masterRecords'] = $masterRecords;
		return view(self::VIEW_PATH.'.index', $data);
	}
	
	public function show($id)
	{
		$masterData = ISC_TerminologyInformation::where('id',$id)->firstOrFail();
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
			
			$item = new ISC_TerminologyInformation;
			
			// saving
			$item->cid = $request->input('cid');
			$item->name = $request->input('name');
			$item->description = $request->input('description');
			$item->process = $request->input('process');
			$item->history = $request->input('history');
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
		$masterData = ISC_TerminologyInformation::where('id',$id)->firstOrFail();
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
			$item = ISC_TerminologyInformation::findOrFail($id);
			
			// saving
			$item->name = $request->input('name');
			$item->description = $request->input('description');
			$item->process = $request->input('process');
			$item->history = $request->input('history');
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
		$item = ISC_TerminologyInformation::findOrFail($id);
		$item->delete();

		return redirect()
		->route(self::ROUTE_ID.'.index')
		->with ('message', '삭제 처리되었습니다. item_id['. $id.']' );
	}
}
