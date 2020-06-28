<?php

use ItalyStrap\Asset\Style;
use ItalyStrap\Asset\Script;

return [
	[
		'handle'	=> 'handle_style_01',
		'type'		=> Style::class,
	],
	[
		'handle'	=> 'handle_style_02',
		'type'		=> Style::class,
		'file'		=> 'style.css',
		'path'		=> 'style.css',
	],
	[
		'handle'		=> 'handle_style_03',
		'type'			=> Style::class,
		'file'			=> 'style.css',
		'load_if'		=> is_single(),
		'load_on'		=> 'some_hook_name',
//		'pre_register'	=> true,
	],
	[
		'handle'	=> 'handle_script_01',
		'type'		=> Script::class,
	],
	[
		'handle'	=> 'handle_script_02',
		'type'		=> Script::class,
		'file'		=> 'script.js'
	],
	[
		'handle'		=> 'comment-reply',
		'type'			=> Script::class,
		'load_on'		=> is_single(),
	],
];