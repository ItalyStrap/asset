<?php
/**
 * Created by PhpStorm.
 * User: fisso
 * Date: 26/12/2018
 * Time: 16:45
 */

namespace ItalyStrap\Asset;

use InvalidArgumentException;
use ItalyStrap\Config\Config_Factory;

class Asset_Factory
{
	private static $type = [
		'style'		=> \ItalyStrap\Asset\Style::class,
		'script'	=> \ItalyStrap\Asset\Script::class,
	];

	public static function make( array $config, $type )	{
		$type = (string) $type;
		$search = strtolower($type);

		if ( isset( self::$type[ $search ] ) ) {
			$class = self::$type[ $search ];

			return new $class( Config_Factory::make( $config ) );
		}

		throw new InvalidArgumentException( sprintf( 'Invalid type %s', $type ) );
	}
}