<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveCategory extends Model
{
    protected $table = 'sa_categories';
    protected $fillable = ['name', 'parent','text','redirect','profile_id'];
}
