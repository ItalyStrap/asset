<?php
/**
 * Abstract Asset Class API
 *
 * Handle the CSS and JS regiter and enque
 *
 * @credits      [hellofromTonya](http://hellofromtonya.github.io/Fulcrum/)
 *
 * @version 1.0.0
 *
 * @package ItalyStrap\Asset
 */
declare(strict_types=1);

namespace ItalyStrap\Asset;

use ReflectionClass;
use ReflectionException;
use InvalidArgumentException;
use ItalyStrap\Config\ConfigInterface;

/**
 * Class Asset
 * @package ItalyStrap\Asset
 */
abstract class Asset implements AssetInterface {

	const HANDLE_KEY = 'handle';

	/**
	 * Configuration for the class
	 *
	 * @var ConfigInterface
	 */
	protected $config;

	/**
	 *
	 * @var string
	 */
	protected $handle = '';

	/**
	 * The Class name without namespace
	 *
	 * @var string
	 */
	protected $class_name = '';

	/**
	 * @var FileInterface
	 */
	protected $file;

	/**
	 * Asset constructor.
	 * @param FileInterface $file
	 * @param ConfigInterface $config
	 * @throws ReflectionException
	 */
	public function __construct( FileInterface $file, ConfigInterface $config ) {

		/**
		 * Credits:
		 * @link https://coderwall.com/p/cpxxxw/php-get-class-name-without-namespace
		 * @php54
		 * $this->class_name =  ( new \ReflectionClass( $this ) )->getShortName();
		 */
		$this->class_name =  strtolower( ( new ReflectionClass( $this ) )->getShortName() );

		$this->file = $file;
		$this->config = $config;
		$this->assertHasHandle();

		$this->handle = \strval( $config->get('handle') );
	}

	/**
	 * @inheritDoc
	 */
	public function isEnqueued(): bool {
		return $this->is( 'enqueued' );
	}

	/**
	 * @inheritDoc
	 */
	public function isRegistered(): bool {
		return $this->is( 'registered' );
	}

	/**
	 * Optional. Status of the script to check. Default 'enqueued'.
	 * Accepts 'enqueued', 'registered', 'queue', 'to_do', and 'done'.
	 *
	 * @param string $list
	 * @return bool
	 */
	private function is( $list = 'enqueued' ): bool {
		$func = \sprintf( 'wp_%s_is', $this->class_name );

		if ( ! is_callable( $func ) ) {
			throw new \BadFunctionCallException(\sprintf(
				'The function wp_%s_is does not exists',
				$this->class_name
			));
		}

		return $func( $this->handle, $list );
	}

	/**
	 * Validates the asset.
	 *
	 * @throws InvalidArgumentException
	 */
	private function assertHasHandle(): void {
		if ( ! $this->config->has( self::HANDLE_KEY ) ) {
			throw new InvalidArgumentException( \sprintf(
				'A unique "handle" ID is required for the %s',
				$this->class_name
			) );
		}
	}
}
