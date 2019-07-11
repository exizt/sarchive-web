<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'sa_pages';
    protected $fillable = ['title', 'content'];
}
