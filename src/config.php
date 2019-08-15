<?php

return [
	'default_driver' => 'PlumpBoy/FileManager/Handler/Image',
	'driver' => [
		'image' => 'PlumpBoy/FileManager/Handler/Image',
		'doc' => 'PlumpBoy/FileManager/Handler/Doc',
	],
	'image' => [
		'default_thumbnail' => [
			'tiny' => '64x64',
			'small' => '100x100',
			'medium' => '240x240',
			'large' => '480x480',
		],
	],
];