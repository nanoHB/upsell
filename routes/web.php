<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => 'auth.shop'], function () {

    Route::get('/', 'ReportController@getDashboard')->name('home');

    Route::get('test', function() {
        phpinfo();
    });

    Route::get('offer/new','OfferController@new')->name('offer/new');
    Route::get('offer/list','OfferController@list')->name('offer/list');
    Route::post('offer/getTable','OfferController@getTable')->name('offer/getTable');
    Route::get('product/getForm','ProductController@getSelectFormData');
    Route::post('product/filter','ProductController@getProductByFilter')->name('product/filter');
    Route::post('offer/create','OfferController@offerCreate')->name('offer/create');
    Route::get('offer/edit/{id}','OfferController@edit')->name('offer/getEdit');
    Route::post('offer/edit','OfferController@offerEdit')->name('offer/postEdit');
    Route::get('offer/delete','OfferController@deleteOffer')->name('offer/delete');
    Route::post('offer/changeStatus','OfferController@changeStatus')->name('offer/changeStatus');
    Route::get('setting','SettingController@index');
    Route::post('setting/save','SettingController@save');
    Route::get('report/offer/{id}','ReportController@getOfferReport');
    Route::get('report/getGeneral','ReportController@getGeneralData');
    Route::get('report/index','ReportController@index');
    Route::get('report/getTable','ReportController@getOfferTable');
    Route::get('report/chartData','ReportController@getProductChartData');
});
