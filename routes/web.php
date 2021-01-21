<?php

use Illuminate\Support\Facades\Route;

// Auth
Auth::routes(['register' => false, 'verify'=> true]);
// index
Route::get ( '/', 'Home@index' );

// ajax
Route::get('ajax/header_nav', 'Archive\ExplorerController@doAjax_getHeaderNav');
Route::get('ajax/folder_nav', 'Archive\ExplorerController@doAjax_getFolderNav');

// SArchive ----------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('static/{uri}', 'Home@staticPage');
    Route::get('archives/{id}', 'Archive\ArchiveController@first')->name('archive.first');
    Route::get('archives/{archiveId}/search', 'Archive\ExplorerController@search')->name('search')->where('archiveId', '[0-9]+');;
    Route::get('archives/{id}/latest', 'Archive\ExplorerController@showDocsByArchive')->name('archive.retrieve');
    Route::get('folders/{id}', 'Archive\ExplorerController@showDocsByFolder')->name('folder.show');

    Route::resource('doc', 'Archive\DocumentController', ['except'=>['index']]);
    Route::post('archives/ajax_mark', 'Archive\DocumentController@doAjax_mark');
    Route::resource('archives/{archiveId}/category', 'Archive\CategoryController', ['except'=>['create','store']]);

    //Route::get('category/{name?}', 'Archive\CategoryController@show')->where('category','(.*)');
});
Route::middleware(['auth'])->name('admin.')->prefix('admin')->group(function(){
    Route::get('/', 'Admin\AdminController@index');
    Route::resource('folderMgmt', 'Admin\ArchiveFolderMgmt', ['except'=>['show','create','edit','store','update','destroy']]);
    Route::get('folderMgmt/index_ajax', 'Admin\ArchiveFolderMgmt@index_ajax')->name('folderMgmt.indexAjax');
    Route::post('folderMgmt/updateList', 'Admin\ArchiveFolderMgmt@updateList')->name('folderMgmt.updateList');
    Route::resource('archiveMgmt', 'Admin\ArchiveMgmt', ['except'=>['show']]);
    Route::post('archiveMgmt/updateSort', 'Admin\ArchiveMgmt@updateSort')->name('archiveMgmt.updateSort');
    //Route::resource('archivePage', 'Admin\ArchivePageMgmt', ['except'=>['show']]);
    Route::view('advanced','admin.advanced',['ROUTE_ID'=>'advanced']);
});