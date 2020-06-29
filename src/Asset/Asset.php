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
 * Class description
 * @todo http://wordpress.stackexchange.com/questions/195864/most-elegant-way-to-enqueue-scripts-in-function-php-with-foreach-loop
 */
abstract class Asset implements AssetStatusInterface {

	use AssetStatusTrait;

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
	 * Get the default structure.
	 *
	 * @return array
	 */
	abstract protected function getDefaultStructure();

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

		$this->handle = (string) $config->handle;
	}

	/**
	 * Validates the asset.
	 *
	 * @throws InvalidArgumentException
	 */
	private function assertHasHandle() {
		if ( ! $this->config->has( 'handle' ) ){
			throw new InvalidArgumentException( \sprintf(
				'A unique "handle" ID is required for the %s',
				$this->class_name
			) );
		}
	}
}
