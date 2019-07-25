<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveCategoryRel extends Model
{
    protected $table = 'sa_category_archive_rel';
    protected $fillable = ['archive_id', 'category'];

}
