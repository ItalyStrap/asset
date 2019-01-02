<?php
/**
 * Created by PhpStorm.
 * User: fisso
 * Date: 31/12/2018
 * Time: 16:07
 */

return [
	'style'	=> [
		'handle'		=> 'handle_style',
		'file'			=> 'style.css',
		'load_on'		=> is_single(),
//		'pre_register'	=> true,
	],
	'script'	=> [
		'handle'		=> 'comment-reply',
		'load_on'		=> is_single(),
	],
];