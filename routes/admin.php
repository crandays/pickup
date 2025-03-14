<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::get('/', 'AdminController@dashboard')->name('index');
Route::get('/dashboard', 'AdminController@dashboard')->name('dashboard');
Route::get('/heatmap', 'AdminController@heatmap')->name('heatmap');
Route::get('/translation',  'AdminController@translation')->name('translation');
Route::get('/push',  'AdminController@push')->name('push');

Route::post('/user_push',  'AdminController@user_push')->name('user_push');
Route::post('/driver_push',  'AdminController@driver_push')->name('driver_push');


Route::group(['as' => 'provider.'], function () {
	Route::get('/provider/{id}/payments', 'Resource\ProviderResource@payments')->name('payments');
	Route::get('/provider/deduction', 'Resource\ProviderResource@deduction')->name('deduction');
	Route::get('/provider/deposit', 'Resource\ProviderResource@deduction')->name('deposit');
    Route::get('review/provider', 'AdminController@provider_review')->name('review');
    Route::get('provider/{id}/approve', 'Resource\ProviderResource@approve')->name('approve');
    Route::get('provider/{id}/disapprove', 'Resource\ProviderResource@disapprove')->name('disapprove');
    Route::get('provider/{id}/request', 'Resource\ProviderResource@request')->name('request');
    Route::get('provider/{id}/statement', 'Resource\ProviderResource@statement')->name('statement');
    Route::resource('provider/{provider}/document', 'Resource\ProviderDocumentResource');
    Route::delete('provider/{provider}/service/{document}', 'Resource\ProviderDocumentResource@service_destroy')->name('document.service');
});



Route::resource('user', 'Resource\UserResource');
Route::resource('account-manager', 'Resource\AccountResource');
Route::resource('fleet', 'Resource\FleetResource');
Route::resource('provider', 'Resource\ProviderResource');
Route::resource('loyality-point-gifts', 'Resource\LoyalityPointGiftResource');
Route::get('loyality-point-gifts/{id}/history', 'Resource\LoyalityPointGiftResource@history');
Route::get('loyality-point-gifts-purchases', 'Resource\LoyalityPointGiftResource@purchases');
Route::post('loyality-point-gifts-purchases/{id}/delivered', 'Resource\LoyalityPointGiftResource@delivered');
Route::post('loyality-point-gifts-purchases/{id}/cancel', 'Resource\LoyalityPointGiftResource@cancel');
Route::resource('document', 'Resource\DocumentResource');
Route::resource('service', 'Resource\ServiceResource');
Route::resource('promocode', 'Resource\PromocodeResource');
Route::post('/provider/AddToWallet', 'Resource\ProviderResource@AddToWallet');
Route::get('promocodes/usage', 'PromocodeController@promocode_usage')->name('promocode.usage');

Route::get('review/user', 'AdminController@user_review')->name('user.review');
Route::get('user/{id}/request', 'Resource\UserResource@request')->name('user.request');

Route::get('map', 'AdminController@map_index')->name('map.index');
Route::get('map/ajax', 'AdminController@map_ajax')->name('map.ajax');

Route::get('settings', 'AdminController@settings')->name('settings');
Route::get('f_settings', 'AdminController@f_settings')->name('f_settings');
Route::post('settings/store', 'AdminController@settings_store')->name('settings.store');
Route::get('settings/payment', 'AdminController@settings_payment')->name('settings.payment');
Route::post('settings/payment', 'AdminController@settings_payment_store')->name('settings.payment.store');

Route::get('profile', 'AdminController@profile')->name('profile');
Route::post('profile', 'AdminController@profile_update')->name('profile.update');

Route::get('password', 'AdminController@password')->name('password');
Route::post('password', 'AdminController@password_update')->name('password.update');

Route::get('payment', 'AdminController@payment')->name('payment');

Route::get('/user-cancel-rides', 'AdminController@userCancelRides')->name('user_cancel_rides');


// statements

Route::get('/statement', 'AdminController@statement')->name('ride.statement');
Route::get('/statement/provider', 'AdminController@statement_provider')->name('ride.statement.provider');
Route::get('/statement/today', 'AdminController@statement_today')->name('ride.statement.today');
Route::get('/statement/monthly', 'AdminController@statement_monthly')->name('ride.statement.monthly');
Route::get('/statement/yearly', 'AdminController@statement_yearly')->name('ride.statement.yearly');


// Static Pages - Post updates to pages.update when adding new static pages.

Route::get('/terms', 'AdminController@terms')->name('terms');
// Route::get('/send/push', 'AdminController@push')->name('push');
// Route::post('/send/push', 'AdminController@send_push')->name('send.push');
Route::get('/privacy', 'AdminController@privacy')->name('privacy');
Route::post('/pages', 'AdminController@pages')->name('pages.update');
Route::resource('requests', 'Resource\TripResource');


Route::get('/dispute', function () {
    return view('admin.dispute.index');
});

Route::get('/dispute-create', function () {
    return view('admin.dispute.create');
});

Route::get('/dispute-edit', function () {
    return view('admin.dispute.edit');
});
