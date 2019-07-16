<?php
// Auth
Auth::routes();
// index
Route::get ( '/', 'Home@index' );
// SArchive ----------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('archives/search', 'Services\ArchiveController@search')->name('archives.search');
    Route::resource('archives', 'Services\ArchiveController');
    Route::resource('page', 'Archive\PageController');
    Route::get('NormalArchives/search', 'Services\ArchiveController@search')->name('NormalArchives.search');
    Route::resource('NormalArchives', 'Services\ArchiveController',['parameters'=>['unit','G']]);
    Route::view('admin','admin.index',['ROUTE_ID'=>'archives']);
});
Route::middleware(['auth'])->name('admin.')->prefix('admin')->group(function(){
    Route::resource('archiveCategory', 'Admin\ArchiveBoardMgmt', ['except'=>['show']]);
    Route::get('archiveCategory/index_ajax', 'Admin\ArchiveBoardMgmt@index_ajax');
    Route::get('archiveCategory/index3', 'Admin\ArchiveBoardMgmt@index3');
    Route::post('archiveCategory/updateList', 'Admin\ArchiveBoardMgmt@updateList');
    
});