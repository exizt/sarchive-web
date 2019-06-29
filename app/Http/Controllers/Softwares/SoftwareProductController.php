<?php

namespace App\Http\Controllers\Softwares;

use App\Http\Controllers\Controller;
use App\Models\SoftwareProduct;
use App\Models\SoftwareProductImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * 
 * @author e2xist
 *
 */
class SoftwareProductController extends Controller
{
    protected const VIEW_PATH = 'site-contents.software-products';
    protected const ROUTE_ID = 'softwares';
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //DB::enableQueryLog();
        
        $list = SoftwareProduct::select(['software_name','description','external_link','software_uri'])->orderBy('updated_at','desc')->paginate(10);
        //$queries = DB::getQueryLog();
        //print_r($queries);
        
        foreach($list as &$item){
            if(strlen(trim($item->external_link))>=1){
                $link = $item->external_link;
            } else {
                $link = route(self::ROUTE_ID.'.show',$item->software_uri);
            }
            $item->link = $link;
        }
        unset($item);
        
        // DataSet
        $dataSet = $this->createViewData ();
        $dataSet['records'] = $list;
        
        
        return view ( self::VIEW_PATH . '.index', $dataSet );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($uri)
    {
        //
        $record = SoftwareProduct::where('software_uri',$uri)->firstOrFail();
        
        // 다운로드 경로 (예시 /softwares/screencapture/download)
        if(empty($record->download_link)){
            //$data['download_link'] = sprintf('/softwares/%s/download',$uri);
            if(!empty($record->download_file)){
                $record->download_link = sprintf('/softwares/%s/download',$record->software_uri);
            }
        }
        
        // 대표 미리보기 이미지 
        if(empty($record->preview_link)){
            if(!empty($record->preview_file)){
                $record->preview_link = sprintf('/softwares/%s/preview',$record->software_uri);
            }
        }
        
        // 개인정보 처리방침 링크
        $record->privacy_link = sprintf('/softwares/%s/privacy',$record->software_uri);
        
        // screenshot
        $screenshots = array();
        $SoftwareProductImages = SoftwareProductImages::where('software_product_id',$record->id)->get();
        foreach ($SoftwareProductImages as &$screenshot){
            $screenshots[] = '/softwares/'.$record->software_uri.'/screenshot/'.$screenshot->id;
        }
        $record->screenshots = $screenshots;
        
        // github 관련
        $record->github_enable = false;
        if(!empty($record->github_user_id)){
            $record->github_enable = true;
        }
        
        //sets Dataset and choices view page     
        $dataSet = $this->createViewData ();
        $dataSet['item'] = $record;
        $view_page = '.show';
        if($record->product_type=='android'){
            $view_page = '.android';
        }
        return view(self::VIEW_PATH.$view_page, $dataSet);
    }
    
    /**
     * 개인정보 처리 방침
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function privacy($uri)
    {
        //
        $record = SoftwareProduct::select(['software_name','subject','description',
            'software_uri','privacy_statement'])->where('software_uri',$uri)->firstOrFail();
        $record->link = route(self::ROUTE_ID.'.show',$record->software_uri);
        $record->privacy_statement = nl2br($record->privacy_statement);
        
        //dataSet
        $dataSet = $this->createViewData ();
        $dataSet['item'] = $record;
        return view(self::VIEW_PATH.'.privacy', $dataSet);
    }
    
    /**
     * 
     * @param string $uri
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($uri)
    {
        $record = SoftwareProduct::select(['software_name','download_file','version_latest'])->where('software_uri',$uri)->firstOrFail();
        //softwares/SHNSWR-APWND-AL002/1.4.0
        //echo $record->download_file;
        
        $filePath = storage_path('/app/'.$record->download_file);
        $fileName = $record->software_name.'-'.$record->version_latest.'.exe';
        //echo $filePath;
        //application/octet-stream
        /*
         * accept-ranges: bytes
            cf-ray: 42410ed759232e51-NRT
            content-length: 522734
            content-type: application/octet-stream
            date: Fri, 01 Jun 2018 10:39:43 GMT
            etag: "c2ce2-7f9ee-56cb61a9af1bc"
            expect-ct: max-age=604800, report-uri="https://report-uri.cloudflare.com/cdn-cgi/beacon/expect-ct"
            last-modified: Mon, 21 May 2018 12:04:29 GMT
            server: cloudflare
            status: 200
         */
        $headers = array(
            'Content-Type: application/octet-stream',
        );
        if(Storage::exists($record->download_file)){
            //이 바로 아랫줄은 5.6 이후로 변경될 것을 미리 적어놓은 것.
            //return Storage::download($record->download_file, 'download.exe', $headers);
            
            // 이 줄은 5.5 까지 이용되는 방식
            //return response()->download($record->download_file, 'download.exe', $headers);
            return response()->download($filePath, $fileName, $headers);
        }
    }

    /**
     *
     * @param string $uri
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function previewImage($uri)
    {
        $record = SoftwareProduct::select(['software_name','preview_file','software_uri'])->where('software_uri',$uri)->firstOrFail();
        if(Storage::exists($record->preview_file)){
          
            $filePath = storage_path('/app/'.$record->preview_file);
            return response()->file($filePath);
        }
    }

    /**
     * 
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function screenshotImage($sw_uri,$image_id)
    {
        // 단순히 체크하기 위한 부분.
        SoftwareProduct::select(['software_name'])->where('software_uri',$sw_uri)->firstOrFail();

        // 이미지 정보를 가져오는 부분.
        $screenshots = SoftwareProductImages::where('id',$image_id)->firstOrFail();
        if(Storage::exists($screenshots->image_file)){
            
            $filePath = storage_path('/app/'.$screenshots->image_file);
            return response()->file($filePath);
        }
    }
    
    /**
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
