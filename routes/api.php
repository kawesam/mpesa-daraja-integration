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


//test function to generate access token
Route::get('/generate/access_token','HooksController@getAccessToken');

//validation url
Route::post('/build/validation','HooksController@registerValidationUrl');

//confirmation url
Route::post('/build/registerUrls','HooksController@registerConfirmationUrl');


//method to simulate c2b payment
Route::post('/build/payment/receive','HooksController@simulateC2BMethod');

//process C2B payments
Route::post('/build/confirmation','HooksController@processPaymentsReceived');

//process b2C payments /call back url
Route::post('/build/disburse','HooksController@initiatePaymentsDisbursement');

//simulate the b2c disbursment
Route::post('/build/simulate/disburse','HooksController@simulateB2CMethod');

//timeout callback
Route::post('build/timeout','HooksController@recordTimeOut');


//B2B route
Route::post('/build/simulate/transferfunds','HooksController@simulateB2BTransfer');

//b2b timeout url
Route::post('build/b2b/timeout','HooksController@recordb2bTimeOut');

//b2b callback url
Route::post('build/transfer/process','HooksController@processB2Bpayments');


// simulate  for LipA na Mpesa APi
Route::post('build/trigger/payment','HooksController@sdkPush');

//callback for sdkPush
Route::post('build/receive/sdkpayments','HooksController@receiveSdkPayments');

//simulate stkpush query for a payment
Route::post('build/stkpush/query','HooksController@stKPushQuery');





