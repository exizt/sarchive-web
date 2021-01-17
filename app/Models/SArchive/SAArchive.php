<?php

namespace App\Models\SArchive;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SAArchive extends Model
{
    use SoftDeletes;

    // 테이블명
    protected $table = 'sa_archives';
    //protected $fillable = ['user_id', 'name','comments','route','index'];
}
