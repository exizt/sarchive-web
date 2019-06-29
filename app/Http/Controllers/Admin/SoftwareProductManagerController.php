<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoftwareProduct;
use App\Models\SoftwareProductImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Parsedown;

/**
 * 
 * @author e2xist
 *
 */
class SoftwareProductManagerController extends Controller
{
    protected const VIEW_PATH = 'admin.software-manager';
    protected const ROUTE_ID = 'admin.softwareManager';
    
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
        $list = SoftwareProduct::orderBy('updated_at','desc')->paginate(10);
        $dataSet = $this->createViewData ();
        $dataSet['records'] = $list;
        
        return view ( self::VIEW_PATH . '.index', $dataSet );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $item = new SoftwareProduct;
        $item->screenshots = array();
        
        // sets DataSet
        $dataSet = $this->createViewData ();
        $dataSet ['item'] = $item;
        //print_r($item);
        return view ( self::VIEW_PATH . '.create', $dataSet );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = new SoftwareProduct();
        
        // saving
        $this->applyDataFromRequestData($data,$request);
        
        //
        $data->save ();
        
        // screenshot 파일 추가
        if($request->hasFile('screenshot_images')){
            $scrFileNames = array();
            
            // 이미지 처리
            $files = $request->file('screenshot_images');
            foreach ($files as $file) {
                $screenshotPath = $file->store('softwares/'.$data->software_sku);
                
                $ProductImages = new SoftwareProductImages();
                $ProductImages->software_product_id = $data->id;
                $ProductImages->image_file = $screenshotPath;
                $ProductImages->order = 0;
                $ProductImages->save();
            }
        }
        
        return redirect()->route(self::ROUTE_ID.'.index')->with('message','신규 생성을 완료하였습니다.');
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
        $record = SoftwareProduct::where('id',$id)->firstOrFail();
        $record->screenshots = SoftwareProductImages::where('software_product_id',$id)->get();
        foreach ($record->screenshots as &$screenshot){
            $screenshot->uri = '/softwares/'.$record->software_uri.'/screenshot/'.$screenshot->id;
        }
        unset($screenshot);
        
        // set DataSet
        $dataSet = $this->createViewData ();
        $dataSet['item'] = $record;
        return view(self::VIEW_PATH.'.edit', $dataSet);
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
        //
        // 있는 값인지 id 체크
        $data = SoftwareProduct::findOrFail ( $id );
        
        // saving
        $this->applyDataFromRequestData($data,$request);
        
        // 
        $data->save ();
        
        
        // screenshot 파일 삭제
        if($request->has ( 'delete_screenshots' )){
            $deleteScreenshots = $request->input ( 'delete_screenshots' );
            foreach($deleteScreenshots as $deleteScreenshotId){
                $ScreenShot = SoftwareProductImages::findOrFail($deleteScreenshotId);
                Storage::delete($ScreenShot->image_file);
                $ScreenShot->delete();
            }
        }
        
        
        // screenshot 파일 추가
        if($request->hasFile('screenshot_images')){
            $scrFileNames = array();
            
            // 이미지 처리
            $files = $request->file('screenshot_images');
            foreach ($files as $file) {
                $screenshotPath = $file->store('softwares/'.$data->software_sku);
                
                $ProductImages = new SoftwareProductImages();
                $ProductImages->software_product_id = $id;
                $ProductImages->image_file = $screenshotPath;
                $ProductImages->order = 0;
                $ProductImages->save();
            }
        }
        
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
        // 있는 값인지 id 체크
        $data = SoftwareProduct::findOrFail ( $id );
        
        // remove Screenshot data and file
        $ScreenShots = SoftwareProductImages::where('software_product_id',$id)->get();
        if(count($ScreenShots) > 0){
            foreach($ScreenShots as $ScreenShot){
                //$ScreenShot = SoftwareProductImages::findOrFail($screenshotId);
                if(Storage::exists($ScreenShot->image_file)){
                    Storage::delete($ScreenShot->image_file);
                }
                $ScreenShot->delete();
            }
        }
        
        // 
        if(strlen(trim($data->download_file))>2){
            if(Storage::exists($data->download_file)){
                Storage::delete($data->download_file);
            }
        }
        if(strlen(trim($data->preview_file))>2){
            if(Storage::exists($data->preview_file)){
                Storage::delete($data->preview_file);
            }
        }
        Storage::deleteDirectory('softwares/'.$data->software_sku);
        
        $data->delete();
        
        return redirect()->route(self::ROUTE_ID.'.index')->with('message','삭제를 완료하였습니다.');
    }
    
    /**
     * 
     * @param object $data
     * @param \Illuminate\Http\Request  $request
     */
    protected function applyDataFromRequestData(&$data, &$request){
        // saving
        $data->software_uri = $request->input ( 'software_uri' );
        $data->software_sku = trim($request->input ( 'software_sku' ));
        $data->software_name = $request->input ( 'software_name' );
        $data->subject = $request->input ( 'subject' );
        $data->description = $request->input ( 'description' );
        $data->version_latest = $request->input ( 'version_latest' );
        $data->download_link = $request->input ( 'download_link' );
        $data->preview_link = $request->input ( 'preview_link' );
        $data->external_link = trim($request->input ( 'external_link' ));
        $data->store_link = trim($request->input ( 'store_link' ));
        $data->privacy_statement = $request->input ( 'privacy_statement' );

        
        if(empty($data->version_latest)){
            $data->version_latest = '1.0.0';
        }
        
        // generate content
        $data->contents_markdown = $request->input ( 'contents' );
        $Parsedown = new Parsedown();
        $data->contents = $Parsedown->text($data->contents_markdown); # prints: <p>Hello <em>Parsedown</em>!</p>
        
        // upload files
        if($request->hasFile('download_file')){
            $file = $request->file('download_file');
            $data->download_file = $file->storeAs('softwares/'.$data->software_sku, $data->version_latest);
        }
        
        
        // preview Image files
        if($request->hasFile('preview_file')){
            $file = $request->file('preview_file');
            $data->preview_file = $file->storeAs('softwares/'.$data->software_sku, 'preview.'.$file->getClientOriginalExtension());
        }
        

    }
    
    /**
     *
     * @return string[]
     */
    protected function createViewData() {
        $dataSet = array ();
        $dataSet ['ROUTE_ID'] = self::ROUTE_ID;
        $dataSet ['VIEW_PATH'] = self::VIEW_PATH;
        $dataSet ['parameters'] = array();
        return $dataSet;
    }
}
