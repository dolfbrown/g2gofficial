<?php
	// Manual
	Route::get('manualPaymentMethod/{code}/edit', 'SystemConfigController@editManualPaymentInstructions')
	->name('manualPaymentMethod.edit')->middleware('ajax');

	Route::put('manualPaymentMethod/{code}/update', 'SystemConfigController@updateManualPaymentInstructions')
	->name('manualPaymentMethod.update');