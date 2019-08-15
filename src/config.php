<?php

return [
	'in_container_name' => 'filemanager',
	'package_name' => 'file_manager',
	'default_driver' => 'Plumpboy/FileManager/Handler/Image',
	'driver' => [
		'image' => 'Plumpboy/FileManager/Handler/Image',
		'doc' => 'Plumpboy/FileManager/Handler/Doc',
		'video' => 'Plumpboy/FileManager/Handler/Video',
	],
	'image' => [
		'default_thumbnail' => [
			'tiny' => '64x64',
			'small' => '100x100',
			'medium' => '240x240',
			'large' => '480x480',
		],
	],
	'video' => [],
	'sheet' => [],

];