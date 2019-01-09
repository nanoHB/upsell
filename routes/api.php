<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//api for shopify front end fetch widget data
Route::get('/widget/product_page','WidgetController@getWidget');
Route::post('/widget/cart_page','WidgetController@getCartWidget');
Route::post('/tracking/add_cart','TrackingController@trackingAddToCartOffer');
