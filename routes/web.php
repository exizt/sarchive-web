<?php

use Illuminate\Support\Facades\Route;

// Auth
Auth::routes(['register' => false, 'verify'=> true]);
// index
Route::get ( '/', 'Home@index' );

// ajax
Route::get('ajax/header_nav', 'Archive\ArchiveController@doAjax_getHeaderNav');
Route::get('ajax/folder_nav', 'Archive\ArchiveController@doAjax_getFolderNav');

// SArchive ----------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('archives/{id}', 'Archive\ArchiveController@retrieveDocsByArchive')->name('archive.retrieve');
    Route::get('folders/{id}', 'Archive\ArchiveController@retrieveDocsByFolder')->name('folder.show');
    Route::get('archives/{archiveId}/search', 'Archive\ArchiveController@search')->name('search')->where('archiveId', '[0-9]+');;

    Route::resource('doc', 'Archive\DocumentController', ['except'=>['index']]);
    Route::get('static/{uri}', 'Archive\PageController@staticPage');

    Route::post('archives/ajax_mark', 'Archive\DocumentController@doAjax_mark');
    Route::resource('archives/{archiveId}/category', 'Archive\CategoryController', ['except'=>['create','store']]);

    Route::resource('page', 'Archive\PageController');
    //Route::get('category/{name?}', 'Archive\CategoryController@show')->where('category','(.*)');
});
Route::middleware(['auth'])->name('admin.')->prefix('admin')->group(function(){
    Route::view('/','admin.index',['ROUTE_ID'=>'archives']);
    Route::resource('archiveBoard', 'Admin\ArchiveBoardMgmt', ['except'=>['show','create','edit','store','update','destroy']]);
    Route::get('archiveBoard/index_ajax', 'Admin\ArchiveBoardMgmt@index_ajax')->name('archiveBoard.indexAjax');
    Route::post('archiveBoard/updateList', 'Admin\ArchiveBoardMgmt@updateList')->name('archiveBoard.updateList');
    Route::resource('archiveProfile', 'Admin\ArchiveMgmt', ['except'=>['show']]);
    Route::post('archiveProfile/updateSort', 'Admin\ArchiveMgmt@updateSort')->name('archiveProfile.updateSort');
    Route::resource('archivePage', 'Admin\ArchivePageMgmt', ['except'=>['show']]);
    Route::view('advanced','admin.advanced',['ROUTE_ID'=>'advanced']);
});