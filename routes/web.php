<?php
/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register web routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | contains the "web" middleware group. Now create something great!
 * |
 */
// Auth
Auth::routes();
// index
Route::get ( '/', 'Home@index' );
// Admin ----------------------------
Route::middleware(['auth'])->name('admin.')->prefix('admin')->group(function(){
    Route::get('/', 'Admin\HomeController@index')->name('index');
    Route::get('vers', 'Admin\HomeController@versionInfo')->name('vers');
    Route::get('post/tags', 'Admin\PostController@indexTag')->name('post.tags');
    Route::resource('post', 'Admin\PostController');
    Route::resource('softwareManager', 'Admin\SoftwareProductManagerController');
    Route::resource('sku', 'Admin\SKUController', ['except'=>['show']]);
    Route::resource('archiveCategory', 'Admin\ArchiveCategoryController', ['except'=>['show']]);
    Route::resource('isc_termMgmt', 'Admin\SalaryCalculatorMgmt\ISC_TermsMgmtController');
    Route::resource('isc_rateMgmt', 'Admin\SalaryCalculatorMgmt\ISC_RatesMgmtController');
    Route::resource('isc_incomeTaxMgmt', 'Admin\SalaryCalculatorMgmt\ISC_IncomeTaxTableMgmtController');
});
// Blog ----------------------------
Route::get('blog', 'BlogController@index')->name('blog');
Route::get('blog/tags', 'BlogController@indexTag')->name('blog.tags.index');
Route::get('blog/tags/{slug}', 'BlogController@showTag')->name('blog.tags.show');
Route::get('blog/{slug}', 'BlogController@showPost')->name('blog.show');
// My Services ----------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('archives/search', 'Services\ArchiveController@search')->name('archives.search');
    Route::resource('archives', 'Services\ArchiveController');
    Route::get('NormalArchives/search', 'Services\ArchiveController@search')->name('NormalArchives.search');
    Route::resource('NormalArchives', 'Services\ArchiveController',['parameters'=>['unit','G']]);
	Route::get('myservice', 'MyServices\HomeController@index');
	Route::resource ('myservice/todo', 'MyServices\TodoManager_Controller', ['as'=>'myservice']);
	Route::resource ( 'myservice/furniture', 'MyServices\Room_furniture', ['as'=>'myservice']);
	Route::resource ( 'myservice/resume', 'MyServices\ResumeManager_Controller', ['as'=>'myservice']);
});
// Softwares ----------------------------
Route::get ( 'ios/korea-salary-income-calculator-for-ios', 'Softwares\IOS_Controller@income_tax');
Route::get ( 'ios/korea-salary-income-calculator-for-ios/privacy', 'Softwares\IOS_Controller@income_tax_privacy');
Route::resource ( 'softwares', 'Softwares\SoftwareProductController',['only' => ['index', 'show']] );
Route::get ( 'softwares/{id}/download', 'Softwares\SoftwareProductController@download');
Route::get ( 'softwares/{id}/preview', 'Softwares\SoftwareProductController@previewImage');
Route::get ( 'softwares/{sw_uri}/screenshot/{id}', 'Softwares\SoftwareProductController@screenshotImage');
Route::get ( 'softwares/{id}/privacy', 'Softwares\SoftwareProductController@privacy');
// Site Services > Calculators ----------------------------
Route::resource ( 'services/punycode-converter', 'Services\Calculators\PunycodeController',['only' => ['index', 'show','store']] );
Route::resource ( 'services/loan-calculator', 'Services\Calculators\LoanCalculatorController',['only' => ['index', 'show','store']] );
Route::resource ( 'services/income-salary-calculator', 'Services\Calculators\SalaryCalculatorController',['only' => ['index', 'show','store']] );
Route::resource ( 'services/electric-bill-calculator-korea', 'Services\Calculators\ElectricityFeeController' ,['only' => ['index', 'show','store']]);
Route::resource ( 'services/area-unit-converter', 'Services\Calculators\LandUnitCalculatorController',['only' => ['index', 'show','store']] );
Route::resource ( 'services/brokerage-calculator-korea', 'Services\Calculators\HouseCommissionController',['only' => ['index', 'show','store']] );
// Site Services > Simple Services -------------------------
Route::get ( 'services/ipservice', 'Services\Simple\IpView_Controller@index' );
Route::get ( 'services/excel_to_mediawiki', 'Services\Simple\ExcelToMediawiki_Controller@index' );
Route::get ( 'services/excel_to_dokuwiki', 'Services\Simple\ExcelToDokuwiki_Controller@index' );
Route::get ( 'services/excel_to_query', 'Services\Simple\ExcelToQuery_Controller@index' );
// data. information tables
//Route::resource ( 'information/cpu', 'Information\CpuData_Controller' );
//Route::resource ( 'information/html5', 'Information\Html5Data_Controller' );
//Route::resource ( 'information/shortcut', 'Information\Cpu' );