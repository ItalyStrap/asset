<?php
/**
 * Created by PhpStorm.
 * User: fisso
 * Date: 26/12/2018
 * Time: 16:45
 */
declare(strict_types=1);

namespace ItalyStrap\Asset;

use InvalidArgumentException;
use ItalyStrap\Config\ConfigFactory;

class AssetFactory {

	private static $type = [
		'style'		=> Style::class,
		'script'	=> Script::class,
	];

	public static function make( $type, array $config ) {
		$type = (string) $type;
		$search = strtolower($type);

		if ( isset( self::$type[ $search ] ) ) {
			$class = self::$type[ $search ];

			return new $class( ConfigFactory::make( $config ) );
		}

		throw new InvalidArgumentException( sprintf( 'Invalid type %s', $type ) );
	}
}
