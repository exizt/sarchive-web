<?php
// Auth
Auth::routes();
// index
Route::get ( '/', 'Home@index' );
// SArchive ----------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('archives/search', 'Services\ArchiveController@search')->name('archives.search');
    Route::get('archives/ajax_boards', 'Services\ArchiveController@doAjax_getBoardList');
    Route::get('archives/ajax_headerNav', 'Services\ArchiveController@doAjax_getHeaderNav');
    Route::resource('{profile}/archives', 'Services\ArchiveController');
    Route::resource('page', 'Archive\PageController');
    Route::resource('category', 'Archive\CategoryController');
    //Route::get('category/{name?}', 'Archive\CategoryController@show')->where('category','(.*)');
});
Route::middleware(['auth'])->name('admin.')->prefix('admin')->group(function(){
    Route::view('/','admin.index',['ROUTE_ID'=>'archives']);
    Route::resource('archiveBoard', 'Admin\ArchiveBoardMgmt', ['except'=>['show']]);
    Route::get('archiveBoard/index_ajax', 'Admin\ArchiveBoardMgmt@index_ajax');
    Route::post('archiveBoard/updateList', 'Admin\ArchiveBoardMgmt@updateList');
    Route::view('advanced','admin.advanced',['ROUTE_ID'=>'advanced']);
});