<?php
namespace App\Models\SArchive;

use Illuminate\Database\Eloquent\Model;

/**
 * [아카이브의 문서 테이블](Document)
 * 아카이브 내에 위치하는 문서와 문서 내용에 대한 테이블이다.
 */
class SADocument extends Model
{
    // FullTextSearch 기능을 이용
    use FullTextSearch;
    
    // 테이블명
    protected $table = 'sa_documents';
    
    //protected $fillable = ['title', 'content','reference','category','archive_id_weak'];
    // 데이터 기본값
    protected $attributes = [
        'title'=>'',
        'content'=>'',
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

    /**
     * meta 테이블과의 조인
     */
    public function meta(){
        return $this->hasOne('App\Models\SADocumentMeta', 'id');
    }
}