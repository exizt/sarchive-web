<?php

namespace App\Models\SArchive;

use Illuminate\Database\Eloquent\Model;

/**
 * [문서 메타 테이블](DocumentMeta)
 * Document 의 id 와 동일한 id 를 갖고 있으며, 메타 정보를 갖고 있는 테이블.
 * 주로 색인과 간단한 탐색을 위한 정보들을 다룬다.
 */
class SADocumentMeta extends Model
{
    // 테이블명
    protected $table = 'sa_document_meta';
    
    //protected $fillable = ['archive_id', 'folder_id','category'];

    protected $appends = array (
        'category_array' 
    );

    public function getCategoryArrayAttribute(){
        preg_match_all("/\[(.*?)\]/",$this->attributes['category'],$matches);

        if(is_array($matches[1])){
            return $matches[1];
        }
        return array();
    }

    public function document(){
        return $this->hasOne('App\Models\SArchive\SADocument', 'id');
    }
}