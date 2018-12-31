<?php
/**
 * Created by PhpStorm.
 * User: fisso
 * Date: 31/12/2018
 * Time: 16:07
 */

return [
	'script'	=> [
		'handle'		=> 'comment-reply',
		'load_on'		=> is_single( $this->post_id ),
	],
];