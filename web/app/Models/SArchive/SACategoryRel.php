<?php

namespace App\Models\SArchive;

use Illuminate\Database\Eloquent\Model;

class SACategoryRel extends Model
{
    protected $table = 'sa_category_rel';
    protected $fillable = ['archive_id','category_name', 'child_category_name'];
    const UPDATED_AT = null;// updated_at 사용 안 함
}
