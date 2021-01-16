<?php

use Illuminate\Support\Facades\Route;

// Auth
Auth::routes(['register' => false, 'verify'=> true]);
// index
Route::get ( '/', 'Home@index' );

// SArchive ----------------------------
Route::middleware(['auth'])->group(function () {
    //Route::get('archives/{id}', 'Archive\ArchiveController@index');
    
    Route::get('archives/search', 'Archive\DocumentController@search')->name('archives.search');
    Route::get('archives/ajax_boards', 'Archive\DocumentController@doAjax_getBoardList');
    Route::get('archives/ajax_headerNav', 'Archive\DocumentController@doAjax_getHeaderNav');
    Route::post('archives/ajax_mark', 'Archive\DocumentController@doAjax_mark');
    Route::resource('{profile}/archives', 'Archive\DocumentController');
    Route::resource('{profile}/category', 'Archive\CategoryController', ['except'=>['create','store']]);
    Route::resource('page', 'Archive\PageController');
    //Route::get('category/{name?}', 'Archive\CategoryController@show')->where('category','(.*)');
});
Route::middleware(['auth'])->name('admin.')->prefix('admin')->group(function(){
    Route::view('/','admin.index',['ROUTE_ID'=>'archives']);
    Route::resource('archiveBoard', 'Admin\ArchiveBoardMgmt', ['except'=>['show','create','edit','store','update','destroy']]);
    Route::get('archiveBoard/index_ajax', 'Admin\ArchiveBoardMgmt@index_ajax')->name('archiveBoard.indexAjax');
    Route::post('archiveBoard/updateList', 'Admin\ArchiveBoardMgmt@updateList')->name('archiveBoard.updateList');
    Route::resource('archiveProfile', 'Admin\ArchiveProfileMgmt', ['except'=>['show']]);
    Route::post('archiveProfile/updateSort', 'Admin\ArchiveProfileMgmt@updateSort')->name('archiveProfile.updateSort');
    Route::resource('archivePage', 'Admin\ArchivePageMgmt', ['except'=>['show']]);
    Route::view('advanced','admin.advanced',['ROUTE_ID'=>'advanced']);
});