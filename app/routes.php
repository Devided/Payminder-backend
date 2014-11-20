<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/v1/send/{payload}', ['uses' => 'PaymindersController@send', 'as' => 'payminder.send']);
Route::get('/v1/get/{hash}', ['uses' => 'PaymindersController@get', 'as' => 'payminder.get']);
Route::get('/v1/get/{hash}/friends', ['uses' => 'PaymindersController@getFriends', 'as' => 'payminder.getFriends']);
Route::get('/c/{id}', ['uses' => 'FriendsController@setPayed', 'as' => 'friend.setpayed']);
Route::get('/v1/view/{hash}', ['uses' => 'PaymindersController@show']);

Route::post('/iron/recieve', function(){
   return Queue::marshal();
});