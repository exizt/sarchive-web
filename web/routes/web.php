<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Archive\ArchiveController;
use App\Http\Controllers\Archive\ExplorerController;


// 대문 페이지
Route::get('/', [ArchiveController::class, 'mainPage']);

// ajax
Route::controller(ExplorerController::class)->group(function() {
    Route::get('ajax/header_nav', 'doAjax_getHeaderNav');
    Route::get('ajax/folder_nav', 'doAjax_getFolderNav');
    Route::get('ajax/folderList', 'doAjax_getChildFolder');
});

// SArchive 서비스
Route::middleware(['auth.404', 'verified'])->group(function () {

    // static page
    Route::get('static/{uri}', 'App\Http\Controllers\Home@staticPage');

    // 아카이브 게시물
    Route::get('archives/{archive}', 'App\Http\Controllers\Archive\ArchiveController@first')->name('archive.first')->where('archive', '[0-9]+');
    Route::controller(ExplorerController::class)->group(function() {
        Route::get('archives/{archive}/latest', 'showDocsByArchive')->name('explorer.archive')->where('archive', '[0-9]+');
        Route::get('archives/{archive}/search', 'search')->name('search')->where('archive', '[0-9]+');
        Route::get('archives/{archive}/category/{category}', 'showDocsByCategory')->name('explorer.category');
        Route::get('folders/{id}', 'showDocsByFolder')->name('explorer.folder')->where('id', '[0-9]+');
        Route::get('folder-selector', 'folderSelector');
    });

    Route::resource('archives', 'App\Http\Controllers\Archive\ArchiveController', ['except'=>['show']]);
    Route::get('archives/editableIndex', 'App\Http\Controllers\Archive\ArchiveController@editableIndex')->name('archives.editableIndex');
    Route::post('archives/updateSort', 'App\Http\Controllers\Archive\ArchiveController@updateSort')->name('archives.updateSort');
    Route::resource('doc', 'App\Http\Controllers\Archive\DocumentController', ['except'=>['index']]);

    Route::post('folders/updateSort', 'App\Http\Controllers\Archive\FolderController@updateSort')->name('folders.updateSort');
    Route::resource('folders', 'App\Http\Controllers\Archive\FolderController', ['except'=>['show','index']]);
    Route::post('archives/ajax_mark', 'App\Http\Controllers\Archive\DocumentController@doAjax_mark');

    Route::resource('archives/{archive}/category', 'App\Http\Controllers\Archive\CategoryController', ['except'=>['create','store','show']]);

    //Route::get('category/{name?}', 'Archive\CategoryController@show')->where('category','(.*)');
});


// 관리자 모드 관련
require __DIR__.'/extends/admin.php';
// 로컬 로그인 관련
require __DIR__.'/extends/custom_auth.php';
