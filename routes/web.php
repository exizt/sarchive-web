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
});