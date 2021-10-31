<?php
	// system Configs
	Route::put('system/notification/{node}/toggle', 'SystemConfigController@toggleNotification')->name('system.notification.toggle')->middleware('ajax');

	Route::put('system/paymentMethod/{id}/toggle', 'SystemConfigController@togglePaymentMethod')->name('system.paymentMethod.toggle')->middleware('ajax');

	Route::put('system/updateConfig', 'SystemConfigController@update')->name('system.update')->middleware('ajax');

	Route::get('system/config', 'SystemConfigController@view')->name('system.config');

	Route::get('payment_method', 'SystemConfigController@payment_methods')->name('config.payment_method');

	Route::get('fb_messenger', 'SystemConfigController@modifyFBMessengerConfigFile')->name('config.fb_messenger');

	Route::post('fb_messenger', 'SystemConfigController@saveFBMessengetConfigFile')->name('saveFBMessengerConfigFile');
