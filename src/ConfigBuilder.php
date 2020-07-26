<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Asset\Version\VersionInterface;
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

	/**
	 * @var array<string>
	 */
	private $types = [];

	/**
	 * @var FinderInterface
	 */
	private $finder;

	/**
	 * @var VersionInterface
	 */
	private $version;

	/**
	 * @var string
	 */
	private $base_url;

	/**
	 * @var string
	 */
	private $base_path;

	/**
	 * ConfigBuilder constructor.
	 * @param FinderInterface $finder
	 * @param VersionInterface $version
	 * @param string $base_url
	 * @param string $base_path
	 */
	public function __construct(
		FinderInterface $finder,
		VersionInterface $version,
		string $base_url,
		string $base_path
	) {
		$this->finder = $finder;
		$this->version = $version;
		$this->base_url = $base_url;
		$this->base_path = $base_path;
	}

	/**
	 * @param string $key
	 * @param string $class
	 */
	public function withType( string $key, string $class ): void {
		if ( \array_key_exists( $key, $this->types ) ) {
			throw new \RuntimeException(\sprintf(
				'%s as already been registered',
				$key
			));
		}

		$this->types[ $key ] = $class;
	}

	/**
	 * @param array ...$configs
	 */
	public function addConfig( array $configs ): void {
		$this->config = \array_merge( $this->config, $configs );
	}

	public function parsedConfig(): \Generator {

		$default = [
			'file_name'			=> '',
			Asset::SHOULD_LOAD	=> true,
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
			$config['enqueue'] = $config[ Asset::SHOULD_LOAD ];
			$config['dependencies'] = $config[ Asset::DEPENDENCIES ];
			$config['inFooter'] = $config[ Asset::IN_FOOTER ];

			yield ConfigFactory::make($config);
		}
	}

	/**
	 * @param $config
	 * @return mixed
	 */
	private function generateFileUrl( array $config ) {
		$config[ Asset::URL ] = $config[ 'file' ] ?? ($config[ Asset::URL ] ?? '');

		if ( $config[ Asset::URL ] || ! $config['file_name'] ) {
			return $config;
		}

		$this->finder->names( $config['file_name'] );

		/** @var \SplFileInfo $fileInfo */
		foreach ( $this->finder as $fileInfo ) {
			break;
		}

		$config[ Asset::URL ] = $this->url( $fileInfo );
		$config[ Asset::VERSION ] = $this->version( $fileInfo );

		return $config;
	}

	/**
	 * @return mixed
	 */
	private function version( \SplFileInfo $fileInfo ) {
		if ( $this->version->hasVersion() ) {
			return $this->version->version();
		}

		return \strval( $fileInfo->getMTime() );
	}

	/**
	 * @return string
	 */
	private function url( \SplFileInfo $fileInfo ): string {
		return $this->generateUrl( $fileInfo );
	}

	/**
	 * @return string
	 */
	private function normalizePath( string $path ): string {
		return \str_replace( '\\', '/', $path );
	}

	/**
	 * @return string
	 */
	private function generateUrl( \SplFileInfo $fileInfo ): string {
		return \str_replace(
			$this->normalizePath( $this->base_path ),
			$this->base_url,
			$this->normalizePath( strval( $fileInfo->getRealPath() ) )
		);
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
			throw new \InvalidArgumentException(\sprintf(
				'File extension is missing for %s',
				$url
			));
		}

		return end( $array );
	}
}
