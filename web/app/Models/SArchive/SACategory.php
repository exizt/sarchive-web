<?php

namespace App\Models\SArchive;

use Illuminate\Database\Eloquent\Model;

class SACategory extends Model
{
    protected $table = 'sa_categories';
    protected $fillable = ['archive_id','name','comments','category','redirect'];
    protected $attributes = [
        'archive_id'=>'',
        'name'=>'',
        'comments'=>'',
        'category'=>'',
        'redirect'=>''
    ];
    protected $appends = array (
        'category_array' 
    );

    /**
     * category 값을 배열로 전환해서 반환
     */
    public function getCategoryArrayAttribute(){
        preg_match_all("/\[(.*?)\]/",$this->attributes['category'],$matches);

        if(is_array($matches[1])){
            return $matches[1];
        }
        return array();
    }
}
