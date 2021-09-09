<?php
namespace App\Models\SArchive;

use Illuminate\Database\Eloquent\Model;

/**
 * 카테고리와 문서 릴레이션.
 * category_name 과 document_id 를 릴레이션하는 테이블
 *
 * document 수정할 때 이 테이블에 같이 일괄 처리를 해준다.
 */
class SACategoryDocumentRel extends Model
{
    protected $table = 'sa_category_document_rel';
    protected $fillable = ['archive_id','category_name','document_id'];
    const UPDATED_AT = null;// updated_at 사용 안 함
}
