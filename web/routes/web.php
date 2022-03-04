<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


// 대문 페이지
Route::get('/', function () {
    if(Auth::check()){
        return redirect('/archives');
    } else {
        abort(404);
    }
});


// ajax
Route::get('ajax/header_nav', 'Archive\ExplorerController@doAjax_getHeaderNav');
Route::get('ajax/folder_nav', 'Archive\ExplorerController@doAjax_getFolderNav');
Route::get('ajax/folderList', 'Archive\ExplorerController@doAjax_getChildFolder');


// SArchive 서비스
Route::middleware(['auth.404', 'verified'])->group(function () {

    // static page
    Route::get('static/{uri}', 'Home@staticPage');

    // archives
    Route::get('archives/{id}', 'Archive\ArchiveController@first')->name('archive.first')->where('id', '[0-9]+');
    Route::get('archives/{archive}/search', 'Archive\ExplorerController@search')->name('search')->where('archive', '[0-9]+');
    Route::get('archives/{id}/latest', 'Archive\ExplorerController@showDocsByArchive')->name('explorer.archive')->where('id', '[0-9]+');
    Route::get('folders/{id}', 'Archive\ExplorerController@showDocsByFolder')->name('explorer.folder')->where('id', '[0-9]+');
    Route::get('archives/{archive}/category/{category}', 'Archive\ExplorerController@showDocsByCategory')->name('explorer.category');
    Route::get('folder-selector', 'Archive\ExplorerController@folderSelector');

    Route::resource('archives', 'Archive\ArchiveController', ['except'=>['show']]);
    Route::get('archives/editaleIndex', 'Archive\ArchiveController@editableIndex')->name('archives.editableIndex');
    Route::post('archives/updateSort', 'Archive\ArchiveController@updateSort')->name('archives.updateSort');
    Route::resource('doc', 'Archive\DocumentController', ['except'=>['index']]);

    Route::post('folders/updateSort', 'Archive\FolderController@updateSort')->name('folders.updateSort');
    Route::resource('folders', 'Archive\FolderController', ['except'=>['show','index']]);
    Route::post('archives/ajax_mark', 'Archive\DocumentController@doAjax_mark');

    Route::resource('archives/{archive}/category', 'Archive\CategoryController', ['except'=>['create','store','show']]);

    //Route::get('category/{name?}', 'Archive\CategoryController@show')->where('category','(.*)');
});


// 관리자 모드
Route::middleware(['auth.404', 'verified'])->name('admin.')->prefix('admin')->group(function(){
    Route::get('/', 'Admin\AdminController@index');
    Route::get('/ver', 'Admin\AdminController@view_version');
    Route::resource('folderMgmt', 'Admin\ArchiveFolderMgmt', ['except'=>['show','create','edit','store','update','destroy']]);
    Route::get('folderMgmt/index_ajax', 'Admin\ArchiveFolderMgmt@index_ajax')->name('folderMgmt.indexAjax');
    Route::post('folderMgmt/updateList', 'Admin\ArchiveFolderMgmt@updateList')->name('folderMgmt.updateList');
    Route::view('advanced','admin.advanced',['ROUTE_ID'=>'advanced']);
});

require __DIR__.'/auth.php';
