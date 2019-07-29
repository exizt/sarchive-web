<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveBookmark extends Model
{
    protected $table = 'sa_bookmarks';
    protected $primaryKey = 'archive_id';
    protected $fillable = ['archive_id','profile_id', 'is_bookmark','is_favorite'];
}
