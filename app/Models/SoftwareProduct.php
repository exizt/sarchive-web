<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareProduct extends Model
{
    //
    protected $fillable = ['software_uri','software_sku',
        'software_name', 'software_name_en',
        'subject','description','contents','contents_markdown',
        'version_latest',
        'download_file','download_link',
        'preview_file','preview_link',
        'external_link',
        'github_user_id', 'github_repo_name',
        'store_link','privacy_statement','product_type'
    ];
}
