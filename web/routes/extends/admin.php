<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MainAdminController;
use App\Http\Controllers\Admin\ArchiveFolderMgmt;


// 관리자 모드
Route::middleware(['auth.404', 'verified'])->name('admin.')->prefix('admin')->group(function(){
    // 기본
    Route::controller(MainAdminController::class)->group(function() {
        Route::get('/', 'index');
        Route::get('version', 'viewVersion');
    });

    // 아카이브 폴더 관리
    Route::controller(ArchiveFolderMgmt::class)->group(function() {
        // Route::resource('folderMgmt', 'App\Http\Controllers\Admin\ArchiveFolderMgmt', ['only'=>['index']]);
        Route::get('folderMgmt', 'index')->name('folderMgmt.index');
        Route::get('folderMgmt/index_ajax', 'index_ajax')->name('folderMgmt.indexAjax');
        Route::post('folderMgmt/updateList', 'updateList')->name('folderMgmt.updateList');
    });
    
    // 작업중인 페이지
    Route::view('advanced','admin.advanced',['ROUTE_ID'=>'advanced']);
});