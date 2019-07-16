<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveBoard extends Model
{
    protected $fillable = ['profile_id','name', 'parent_id','comment','index'];
    protected $table = 'sa_boards';


    public function getSubBoardList($boardId=0, $depth = 1){

    }
}
