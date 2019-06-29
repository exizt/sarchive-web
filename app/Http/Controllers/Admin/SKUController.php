<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SKU;
use Illuminate\Validation\Validator;

class SKUController extends Controller
{
	protected const VIEW_PATH = 'admin.sku';
	protected const ROUTE_ID = 'admin.sku';
	public function __construct() {
		$this->middleware ( 'auth' );
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    	$isAjax = $request->input('ajax',false);
    	if(!$isAjax){
	    	//$records = SKU::orderBy ( 'created_at', 'desc' )->paginate ( 10 );
	    	$sku = SKU::orderBy ( 'created_at', 'desc' );
	    	
	    	$searches = array();
	    	for($i=1;$i<5;$i++){
		    	$value = $request->input ( 'depth'.$i );
		    	if($value!== null)
		    	{
		    		$sku->where('depth_'.$i,$value);
		    	}
		    	$searches['depth_'.$i] = $value;
	    	}
	    
	    	$records = $sku->paginate(10);
	    	
	    	$data = $this->createViewData();
	        $data['records'] = $records;
	        $data['search'] = $searches;
	    	return view ( self::VIEW_PATH . '.index', $data );

    	} else return $this->ajax($request);
    }

    public function ajax(Request $request)
    {
    	$records= SKU::orderBy ( 'created_at', 'desc' )->paginate ( 10 );
    	
    	$resultSet = array();
    	foreach ($records as $_item)
    	{
    		$item = new \stdClass();
    		$item->id = $_item->id;
    		$item->product_name= $_item->product_name;
    		$item->sku= $_item->product_sku;
    		$item->created_at = $_item->created_at->format('Y-m-d g:ia');
    		$resultSet[] = $item;
    	}
    	//echo json_encode($resultSet);
    	return response()->json($resultSet);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    	//$item = collect(new SKU);
    	$item = new SKU;
    	$data = $this->createViewData ();
    	$data ['item'] = $item;
    	//print_r($item);
    	return view ( self::VIEW_PATH . '.create', $data );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$rules = [
    			'title' => 'required|max:255',
    			'depth1' => 'required|max:5',
    			'depth2' => 'required|max:5',
    			'depth3' => 'required|max:5',
    			'depth4' => 'required|max:5'
    	];
    	$this->validate($request, $rules);
    	//$validator = Validator::make($request->all(), $rules)->validate();
    	
    	//print_r($request);
    	$data = array ();
    	$data ['product_name'] = $request->input ( 'title' );
    	$data ['prefix'] = 'SHN';
    	$data ['depth_1'] = $request->input ( 'depth1' );
    	$data ['depth_2'] = $request->input ( 'depth2' );
    	$data ['depth_3'] = $request->input ( 'depth3' );
    	$data ['depth_4'] = $request->input ( 'depth4' );
    	
    	
    	$depth_5_from_db = SKU::where([
    			['depth_1', $data ['depth_1']],
    			['depth_2', $data ['depth_2']],
    			['depth_3', $data ['depth_3']],
    			['depth_4', $data ['depth_4']],
    	])->max ('depth_5');
    	//$depth_5 = sprintf("%03d", intval($item)+1);
    	//print_r($depth_5);
    	$data ['depth_5'] = sprintf("%03d", intval($depth_5_from_db)+1);
    	
    	$sku = SKU::create ( $data );
    	$sku->save ();
    	
    	return redirect ()->route ( self::ROUTE_ID . '.index' )->withSuccess ( 'New Post Successfully Created.' );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    	$item= SKU::where ( 'id', $id )->firstOrFail ();
    	$data = $this->createViewData ();
    	$data ['item'] = $item;
    	return view ( self::VIEW_PATH . '.edit', $data );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    	// 있는 값인지 id 체크
    	$item = SKU::findOrFail ( $id );
    	
    	// saving
    	$item->product_name = $request->input ( 'title' );
    	$item->save ();
    	
    	return redirect ()->route ( self::ROUTE_ID . '.edit', $id)->with ('message', '변경이 완료되었습니다.' );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    /**
     *
     * @return string[]
     */
    protected function createViewData() {
    	$data = array ();
    	$data ['ROUTE_ID'] = self::ROUTE_ID;
    	$data ['VIEW_PATH'] = self::VIEW_PATH;
    	return $data;
    }
}
