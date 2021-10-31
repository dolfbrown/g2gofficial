<?php
	// Bulk upload routes
	Route::get('product/upload/downloadCategorySlugs', 'ProductUploadController@downloadCategorySlugs')->name('product.downloadCategorySlugs');
	Route::get('product/upload/downloadTemplate', 'ProductUploadController@downloadTemplate')->name('product.downloadTemplate');
	Route::get('product/upload', 'ProductUploadController@showForm')->name('product.bulk');
	Route::post('product/upload', 'ProductUploadController@upload')->name('product.upload');
	Route::post('product/import', 'ProductUploadController@import')->name('product.import');
	Route::post('product/downloadFailedRows', 'ProductUploadController@downloadFailedRows')->name('product.downloadFailedRows');

	// Product model routes
    Route::get('product/getCombinations', 'ProductController@getCombinations')->name('product.getCombinations');
	Route::get('product/{product}/addVariant', 'ProductController@singleVariantForm')->name('product.addVariant'); // get form to add a single variant
	Route::post('product/{product}/saveVariant', 'ProductController@saveSingleVariant')->name('product.saveVariant'); // save a single variant

	Route::delete('product/{product}/trash', 'ProductController@trash')->name('product.trash'); // product move to trash
	Route::get('product/{product}/restore', 'ProductController@restore')->name('product.restore');
	Route::post('product/store', 'ProductController@store')->name('product.store')->middleware('ajax');
	Route::post('product/{product}/update', 'ProductController@update')->name('product.update')->middleware('ajax');
	Route::get('product/getProducts', 'ProductController@getProducts')->name('product.getMore');
	Route::resource('product', 'ProductController', ['except' =>['store', 'update']]);

	Route::get('product/{product}/editQtt', 'ProductController@editQtt')->name('product.editQtt');
	Route::put('product/{product}/updateQtt', 'ProductController@updateQtt')->name('product.updateQtt');

	// Downloadable
    Route::get('downloadable', 'DownloadableController@index')->name('downloadable.index');
    Route::get('downloadable/create', 'DownloadableController@create')->name('downloadable.create');
    Route::get('downloadable/getProducts', 'DownloadableController@getProducts')->name('downloadable.getMore');
    Route::post('downloadable/store', 'DownloadableController@store')->name('downloadable.store')->middleware('ajax');
    Route::get('downloadable/{product}/edit', 'DownloadableController@edit')->name('downloadable.edit');
    Route::post('downloadable/{product}/update', 'DownloadableController@update')->name('downloadable.update')->middleware('ajax');

