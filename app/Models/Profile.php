<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'sa_profiles';
    protected $fillable = ['user_id', 'name','text','root_board_id','route','index'];
}
