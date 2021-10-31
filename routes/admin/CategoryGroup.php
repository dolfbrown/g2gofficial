<?php
Route::delete('categoryGroup/{catGrp}/trash', 'CategoryGroupController@trash')->name('categoryGroup.trash'); // category post move to trash
Route::get('categoryGroup/{catGrp}/restore', 'CategoryGroupController@restore')->name('categoryGroup.restore');

Route::resource('categoryGroup', 'CategoryGroupController', ['except' => ['show']]);