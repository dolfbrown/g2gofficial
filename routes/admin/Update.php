<?php
    Route::get('version/check', 'UpdateController@check')->name('version.check');
	Route::post('version/update', 'UpdateController@update')->name('version.update');
