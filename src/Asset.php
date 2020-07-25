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

	const HANDLE		= 'handle';
	const URL			= 'url';
	const VERSION		= 'version';
	const DEPENDENCIES	= 'deps';
	const IN_FOOTER		= 'in_footer';
	const LOCALIZE		= 'localize';
	const MEDIA			= 'media';
	const LOCATION		= 'location';

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
	 * Asset constructor.
	 * @param ConfigInterface $config
	 * @throws ReflectionException
	 */
	public function __construct( ConfigInterface $config ) {

		/**
		 * Credits:
		 * @link https://coderwall.com/p/cpxxxw/php-get-class-name-without-namespace
		 * @php54
		 * $this->class_name =  ( new \ReflectionClass( $this ) )->getShortName();
		 */
		$this->class_name =  strtolower( ( new ReflectionClass( $this ) )->getShortName() );

		$this->config = $config;
		$this->assertHasHandle();
		$this->handle = \strval( $config->get(Asset::HANDLE ) );
	}

	/**
	 * @inheritDoc
	 */
	public function location(): string {
		return $this->config->get( Asset::LOCATION, 'wp_enqueue_scripts' );
	}

	/**
	 * Loading asset conditionally.
	 *
	 * @return bool
	 */
	public function shouldEnqueue(): bool {
		$to_load = $this->config->get('load_on' );

		/**
		 * Example:
		 * 'load_on'		=> false,
		 * 'load_on'		=> true,
		 * 'load_on'		=> is_my_function\return_bool(),
		 */
		if ( \is_bool( $to_load ) ) {
			return $to_load;
		}

		if ( ! is_callable( $to_load ) ) {
			return true;
		}

		return (bool) call_user_func( $to_load );
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
		if ( ! $this->config->has( self::HANDLE ) ) {
			throw new InvalidArgumentException( \sprintf(
				'A unique "handle" ID is required for the %s',
				$this->config->get( Asset::URL )
			) );
		}
	}
}
