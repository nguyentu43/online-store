<?php

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\User;

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

Route::middleware('auth:api')->group(function(){

	Route::apiResource('/users', 'UserController');

	Route::apiResource('/categories', 'CategoryController');
	Route::apiResource('/brands', 'BrandController');

	Route::apiResource('/products', 'ProductController');
	Route::prefix('/products/{product}')->group(function(){

		Route::apiResource('/attributes', 'ProductAttributeValueController');
		Route::apiResource('/skus', 'ProductSkuController')->except('index');
	});

	Route::apiResource('/rate', 'RateController');

	Route::prefix('/products/{product}/skus/{sku}')->group(function(){

		Route::post('/media', 'MediaController@store');
		Route::delete('/media/{id}', 'MediaController@destroy');
	});

	Route::apiResource('/roles', 'RoleController');

	Route::prefix('/user')->group(function(){
		
		Route::apiResource('/addressbooks', 'AddressBookController');

		Route::get('/orders', 'OrderController@getListUserOrders');
		Route::post('/orders', 'OrderController@createOrderFromCart');

	});

	Route::get('/user', 'UserController@getUserCurrent');
	Route::put('/user', 'UserController@updateUserCurrent');

	Route::apiResource('/orders', 'OrderController');
	Route::patch('/orders/{order}/update-status', 'OrderController@updateOrderStatus');
	Route::post('/orders/{order}/payment', 'OrderController@payment');

	Route::apiResource('/cart', 'CartController')->only(['index']);
	Route::delete('/cart', 'CartController@destroy');
	Route::post('/cart/items', 'CartController@addItem');
	Route::put('/cart/items/{id}', 'CartController@changeItem');
	Route::delete('/cart/items/{id}', 'CartController@removeItem');

	Route::apiResource('/producttypes', 'ProductTypeController');
	Route::put('/producttypes/{producttype}/attach/{attr_id}', 'ProductTypeController@attach');
	Route::delete('/producttypes/{producttype}/detach/{attr_id}', 'ProductTypeController@detach');
	Route::apiResource('/attributes', 'ProductAttributeController');

	Route::post('/upload', 'StorageController@store');
	Route::post('/upload/remove', 'StorageController@destroy');

	Route::apiResource('/campaigns', 'CampaignController');
	Route::apiResource('/comments', 'CommentController');
	Route::apiResource('/discount-code', 'DiscountCodeController');

	Route::prefix('/statistic')->group(function(){
		Route::get('/order', 'OrderController@statistical');
		Route::get('/products-sold-out', 'ProductController@getProductSoldOut');
	});
	
});

Route::post('/login', 'UserController@login');
Route::post('/register', 'UserController@register');

Route::get('/login-provider/{provider}', 'UserController@redirectToProvider');
Route::get('/login-provider/{provider}/callback', 'UserController@handleLoginCallback');

Route::apiResource('/categories', 'CategoryController')->only(['index', 'show']);
Route::apiResource('/brands', 'BrandController')->only(['index', 'show']);
Route::apiResource('/products', 'ProductController')->only(['index', 'show']);
Route::apiResource('/producttypes/', 'ProductTypeController')->only(['index', 'show']);
Route::apiResource('/campaigns', 'CampaignController')->only(['index', 'show']);
Route::apiResource('/comments', 'CommentController')->only(['index', 'show']);
Route::apiResource('/rate', 'RateController')->only(['index', 'show']);
Route::apiResource('/discount-code', 'DiscountCodeController')->only(['index', 'show']);
Route::post('/check-discount-code', 'DiscountCodeController@check');


Route::get('/skus', 'ProductSkuController@index');
Route::post('/request-reset', 'UserController@requestResetPassword');
Route::post('/reset-password', 'UserController@resetPassword');

Route::get('/user/email', 'UserController@checkEmail');

Route::get('/payment-methods', function(){
	return App\PaymentMethod::all();
});