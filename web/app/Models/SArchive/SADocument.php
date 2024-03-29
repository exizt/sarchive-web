<?php
namespace App\Models\SArchive;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FullTextSearch;

/**
 * [아카이브의 문서 테이블](Document)
 * 아카이브 내에 위치하는 문서와 문서 내용에 대한 테이블이다.
 */
class SADocument extends Model
{
    use SoftDeletes;
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
     * FullText 검색에 해당하는 컬럼 지정
     */
    protected $searchable = ['title','content'];

    /**
     * content 변경이 일어날 때 summary_var 컬럼도 같이 생성 변경함
     */
    public function setContentAttribute($value){
        $this->attributes['content'] = $value;
        $this->attributes['summary_var'] = $this->generateSummary($value,255);
    }

    /**
     * summary 생성
     */
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

    /**
     * category 값을 배열로 전환해서 반환
     */
    public function getCategoryArrayAttribute(){
        preg_match_all("/\[(.*?)\]/",$this->attributes['category'],$matches);

        if(is_array($matches[1])){
            return $matches[1];
        }
        return array();
    }

    /**
     * Archive 테이블과의 조인
     */
    public function archive(){
        return $this->belongsTo('App\Models\SArchive\SAArchive', 'archive_id');
    }

    /**
     * Folder 테이블과의 조인
     */
    public function folder(){
        return $this->belongsTo('App\Models\SArchive\SAFolder', 'folder_id');
    }
}
