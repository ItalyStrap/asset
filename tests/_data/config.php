<?php
/**
 * Created by PhpStorm.
 * User: fisso
 * Date: 02/01/2019
 * Time: 16:10
 */

return [
	'style'		=> [
		[
			'handle'	=> 'handle_style',
		],
		[
			'handle'	=> 'handle_style',
			'file'		=> 'style.css'
		],
		[
			'handle'		=> 'handle_style',
			'file'			=> 'style.css',
			'load_on'		=> is_single(),
//		'pre_register'	=> true,
		],
	],
	'script'	=> [
		[
			'handle'	=> 'handle_script',
		],
		[
			'handle'	=> 'handle_script',
			'file'		=> 'script.js'
		],
		[
			'handle'		=> 'comment-reply',
			'load_on'		=> is_single(),
		],
	],
];