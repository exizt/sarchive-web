<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveCategory extends Model
{
    protected $fillable = ['name', 'parent_id','comment'];
    

}
