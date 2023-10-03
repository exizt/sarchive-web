<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Archive\ExplorerController;
use App\Http\Controllers\Admin\MainAdminController;
use App\Http\Controllers\Admin\ArchiveFolderMgmt;


// 대문 페이지
Route::get('/', function () {
    if(Auth::check()){
        return redirect('/archives');
    } else {
        abort(404);
    }
});

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
    Route::get('archives/{id}', 'App\Http\Controllers\Archive\ArchiveController@first')->name('archive.first')->where('id', '[0-9]+');
    Route::controller(ExplorerController::class)->group(function() {
        Route::get('archives/{id}/latest', 'showDocsByArchive')->name('explorer.archive')->where('id', '[0-9]+');
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


// 관리자 모드
Route::middleware(['auth.404', 'verified'])->name('admin.')->prefix('admin')->group(function(){
    Route::controller(MainAdminController::class)->group(function() {
        Route::get('/', 'index');
        Route::get('version', 'viewVersion');
    });
    Route::controller(ArchiveFolderMgmt::class)->group(function() {
        // Route::resource('folderMgmt', 'App\Http\Controllers\Admin\ArchiveFolderMgmt', ['only'=>['index']]);
        Route::get('folderMgmt', 'index')->name('folderMgmt.index');
        Route::get('folderMgmt/index_ajax', 'index_ajax')->name('folderMgmt.indexAjax');
        Route::post('folderMgmt/updateList', 'updateList')->name('folderMgmt.updateList');
    });
    
    // 작업중인 페이지
    Route::view('advanced','admin.advanced',['ROUTE_ID'=>'advanced']);
});

require __DIR__.'/custom_auth.php';
