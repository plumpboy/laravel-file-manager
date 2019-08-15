<?php

Route::group([
		'name' => 'FileManager.',
		'namespace' => '\Plumpboy\Filemanager\Controllers',
	], function() {

	// Get files and sub-folders
    Route::get('/folders', [
	    'uses' => 'FileManagerController@getItems',
	    'as' => 'getItems',
	]);

    Route::get('/', function () {
	    return view('laravel-file-manager::index');
	});
});
// // collection
// Route::post('/collections', [
//     'uses' => 'FileController@addFolder',
//     'as' => 'folders.add',
// ]);
// Route::delete('/collections/{name}', [
//     'uses' => 'FileController@deleteFolder',
//     'as' => 'folders.delete',
// ]);
// Route::get('/collections', [
//     'uses' => 'FileController@getFolders',
//     'as' => 'folders.list',
// ]);

// /////////////
// /// files ///
// /////////////

// // crop
// Route::post('/crop', [
//     'uses' => 'FileController@getCrop',
//     'as' => 'files.crop',
// ]);
// // rename
// Route::post('/rename', [
//     'uses' => 'FileController@rename',
//     'as' => 'files.rename',
// ]);
// // scale/resize
// Route::post('/resize', [
//     'uses' => 'FileController@resize',
//     'as' => 'files.resize',
// ]);
// // download
// Route::post('/download', [
//     'uses' => 'FileController@download',
//     'as' => 'files.download',
// ]);
// // upload
// Route::post('/upload', [
//     'uses' => 'FileController@upload',
//     'as' => 'files.upload',
// ]);
// // delete
// Route::post('/delete', [
//     'uses' => 'FileController@delete',
//     'as' => 'files.delete',
// ]);
