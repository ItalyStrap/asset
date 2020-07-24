<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Finder\FinderInterface;

class ConfigBuilder {

	/**
	 * @var array
	 */
	private $config = [];

	private $schema = [
		'handle'		=> '',
		'file'			=> '',
		'deps'			=> [],
		'version'		=> '',
		'in_footer'		=> true,
	];

	private $types = [
		'css'	=> \Inpsyde\Assets\Style::class,
		'js'	=> \Inpsyde\Assets\Script::class
	];
	/**
	 * @var Finder
	 */
	private $finder;

	/**
	 * ConfigBuilder constructor.
	 * @param Finder $finder
	 */
	public function __construct( FinderInterface $finder ) {
		$this->finder = $finder;
	}

	/**
	 * @param array ...$configs
	 */
	public function addConfig( array $configs ) {
		$this->config = \array_merge( $this->config, $configs );
	}

	public function parsedConfig(): \Generator {

		$default = [
			'file_name'			=> '',
			'load_on'			=> true,
			Asset::DEPENDENCIES	=> [],
			Asset::IN_FOOTER	=> true,
		];

		foreach ( $this->config as $config ) {
			$config = \array_replace($default, $config);

			$config = $this->generateFileUrl( $config );

			if ( 'comment-reply' === $config[ Asset::HANDLE ] ) {
				$config[ Asset::URL ] = 'comment-reply.js';
			}

			$config['type'] = $this->getType( $config[ Asset::URL ] );
			$config['enqueue'] = $config['load_on'];
			$config['dependencies'] = $config[ Asset::DEPENDENCIES ];
			$config['inFooter'] = $config[ Asset::IN_FOOTER ];

			yield ConfigFactory::make($config);
		}
	}

	/**
	 * https://paulund.co.uk/get-the-file-extension-in-php
	 * @param string $url
	 * @return string
	 */
	private function fileExtension( string $url ): string {

		if ( empty( $url ) ) {
			throw new \InvalidArgumentException( 'Url must not be empty' );
		}

		$array = explode( ".", $url );

		if ( \count( $array ) === 1 ) {
			throw new \InvalidArgumentException('File extension is missing');
		}

		return end( $array );
	}

	/**
	 * @param $url
	 * @return string
	 */
	private function getType( $url ): string {
		$extension = $this->fileExtension( $url );

		if ( ! \array_key_exists( $extension, $this->types ) ) {
			throw new \RuntimeException(
				\sprintf(
					'%s extension is not registered',
					$extension
				)
			);
		}

		return $this->types[ $extension ];
	}

	/**
	 * @param $config
	 * @return mixed
	 */
	private function generateFileUrl( $config ) {
		$config[ Asset::URL ] = $config[ 'file' ] ?? ($config[ Asset::URL ] ?? '');

		if ( $config[ Asset::URL ] || ! $config['file_name'] ) {
			return $config;
		}

		$this->finder->names( $config['file_name'] );

		/** @var \SplFileInfo $item */
		foreach ( $this->finder as $item ) {
			break;
		}

		$file = new File(
			$item,
			new Version\EmptyVersion(),
			$_SERVER['TEST_SITE_WP_URL'],
			$_SERVER['WP_ROOT_FOLDER']
		);

		 $config[Asset::URL] = $file->url();
		 $config[Asset::VERSION] = $file->version();

		 return $config;
	}
}
