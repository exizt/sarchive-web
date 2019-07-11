<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use FullTextSearch;
    protected $fillable = ['title', 'content','unit_code','board_id','reference','category'];
    protected $attributes = ['title'=>'',
     'content'=>'',
     'unit_code'=>'',
     'board_id'=>'',
     'reference'=>'',
     'category'=>''];
    protected $appends = array (
        'category_array' 
    );
    protected $perPage = 15;
    /**
     * The columns of the full text index
     */
    protected $searchable = ['title','content'];
    
    public function setContentAttribute($value){
        $this->attributes['content'] = $value;
        //$this->attributes['summary'] = $this->generateSummary($value,300);
        $this->attributes['summary_var'] = $this->generateSummary($value,255);
    }
    
    private function generateSummary($content, $char_length=255){
        // 길이가 너무 길 경우에 대비.
        if(mb_strlen($content) > 5000){
            $text = mb_substr($content,0,5000);
        } else {
            $text = $content;
        }
        
        $text = strip_tags($text);
        $text = str_replace("\r\n",' ',$text);
        $text = str_replace("\n",' ',$text);
        $text = str_replace("\r",' ',$text);
        $text = str_replace("\t",' ',$text);
        $text = str_replace("&nbsp;",' ',$text);
        $text = preg_replace('!\s+!', ' ', $text);
        
        return mb_substr($text, 0, $char_length);
    }

    public function getCategoryArrayAttribute(){
        preg_match_all("/\[(.*?)\]/",$this->attributes['category'],$matches);

        if(is_array($matches[1])){
            return $matches[1];
        }
        return array();
    }
}
