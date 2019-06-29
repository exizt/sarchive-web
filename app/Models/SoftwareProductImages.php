<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareProductImages extends Model
{
    //
    protected $fillable = [
        'software_product_id','image_file','image_link','order'
    ];
}
