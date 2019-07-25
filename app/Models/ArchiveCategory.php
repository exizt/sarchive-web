<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveCategory extends Model
{
    protected $table = 'sa_categories';
    protected $fillable = ['name', 'parent','text','redirect','profile_id'];
    protected $attributes = ['name'=>'',
     'parent'=>'',
     'text'=>'',
     'redirect'=>'',
     'profile_id'=>''
    ];
    protected $appends = array (
        'parent_array' 
    );


    public function getParentArrayAttribute(){
        preg_match_all("/\[(.*?)\]/",$this->attributes['parent'],$matches);

        if(is_array($matches[1])){
            return $matches[1];
        }
        return array();
    }
}
