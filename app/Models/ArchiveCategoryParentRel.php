<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveCategoryParentRel extends Model
{
    protected $table = 'sa_category_parent_rel';
    protected $fillable = ['profile_id','parent', 'child'];

}
