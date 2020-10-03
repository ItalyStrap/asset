<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use InvalidArgumentException;
use ItalyStrap\Asset\Version\VersionInterface;
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Finder\FinderInterface;
use RuntimeException;
use SplFileInfo;
use function array_key_exists;
use function array_merge;
use function array_replace;
use function count;
use function implode;
use function sprintf;
use function str_replace;
use function strpos;
use function strval;

final class ConfigBuilder {

	public const FILE_NAME = 'file_name';

	/**
	 * @var array
	 */
	private $config = [];

	/**
	 * @var array<string>
	 */
	private $types = [];

	/**
	 * @var FinderInterface[]
	 */
	private $finder = [];

	/**
	 * @var ?VersionInterface
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
	 * @param string $base_url
	 * @param string $base_path
	 */
	public function __construct(
		string $base_url,
		string $base_path
	) {
		$this->base_url = $base_url;
		$this->base_path = $base_path;
	}

	/**
	 * @param string $key
	 * @param string $class
	 */
	public function withType( string $key, string $class ): void {
		if ( array_key_exists( $key, $this->types ) ) {
			throw new RuntimeException( sprintf(
				'%s as already been registered',
				$key
			));
		}

		$this->types[ $key ] = $class;
	}

	/**
	 * @param string $key
	 * @param FinderInterface $finder
	 */
	public function withFinderForType( string $key, FinderInterface $finder ): void {
		if ( array_key_exists( $key, $this->finder ) ) {
			throw new RuntimeException( sprintf(
				'%s for %s as already been registered',
				get_class($finder),
				$key
			));
		}

		$this->finder[ $key ] = $finder;
	}

	public function withVersion( VersionInterface $version ): void {
		$this->version = $version;
	}

	/**
	 * @param array ...$configs
	 */
	public function addConfig( array $configs ): void {
		$this->config = array_merge( $this->config, $configs );
	}

	/**
	 * @return iterable
	 */
	public function parseConfig(): iterable {

		$default = [
			'file'					=> '',
			self::FILE_NAME			=> '',
			Asset::URL				=> '',
			Asset::VERSION			=> null,
			Asset::SHOULD_LOAD		=> true,
			Asset::DEPENDENCIES		=> [],
			Asset::IN_FOOTER		=> true,
		];

		foreach ( $this->config as $config ) {
			$config = array_replace($default, $config);

			/**
			 * Avoid old deregister functionality
			 */
			if ( array_key_exists( 'deregister', $config ) ){
				continue;
			}

			if ( 'comment-reply' === $config[ Asset::HANDLE ] ) {
				$config[ Asset::URL ] = '//comment-reply.js';
			}

			$config = $this->generateFileUrl( $config );

			$config['type'] = $this->getType( $config[ Asset::URL ], $config[ Asset::HANDLE ] );
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

		/**
		 * In case the url is relative
		 */
		if ( strpos( $config[ Asset::URL ], '//' ) !== false ) {
			return $config;
		}

		/**
		 * Back compat with old key
		 */
		if ( strpos( $config[ 'file' ], '//' ) !== false ) {
			$config[ Asset::URL ] = $config[ 'file' ];
			return $config;
		}

		$fileInfo = $this->getFileInfo( $config[ self::FILE_NAME ], $config[ Asset::HANDLE ] );

		$config[ Asset::URL ] = $this->url( $fileInfo );
		$config[ Asset::VERSION ] = $this->version( $fileInfo, $config );

		return $config;
	}

	private function version( SplFileInfo $fileInfo, array $config ): string {
		if ( ! $this->version ) {
			return strval( $fileInfo->getMTime() );
		}

		return strval( $this->version->version( $fileInfo, $config ) );
	}

	/**
	 * @return string
	 */
	private function url( SplFileInfo $fileInfo ): string {
		return $this->generateUrl( $fileInfo );
	}

	/**
	 * @return string
	 */
	private function normalizePath( string $path ): string {
		return str_replace( '\\', '/', $path );
	}

	/**
	 * @param SplFileInfo $fileInfo
	 * @return string
	 */
	private function generateUrl( SplFileInfo $fileInfo ): string {
		return str_replace(
			$this->normalizePath( $this->base_path ),
			$this->base_url,
			$this->normalizePath( strval( $fileInfo->getRealPath() ) )
		);
	}

	/**
	 * @param string[] $file_name
	 * @param $handle
	 * @return SplFileInfo
	 */
	private function getFileInfo( $file_name, $handle ): SplFileInfo {
		$file_name = (array) $file_name;
		$extension = '';

		foreach ( $file_name as $name ) {
			$extension = $this->fileExtension( $name, $handle );
			break;
		}

		if ( ! array_key_exists( $extension, $this->finder ) ) {
			throw new RuntimeException(
				sprintf(
					'A finder for %s extension is not registered',
					$extension
				)
			);
		}

		$this->finder[ $extension ]
			->names( $file_name );

//		return $this->finder[ $extension ]->current();

		/** @var SplFileInfo $fileInfo */
		foreach ( $this->finder[ $extension ] as $fileInfo) {
			return $fileInfo;
		}

		throw new RuntimeException( sprintf(
			'%s file not found',
			implode(', ', $file_name)
		) );
	}

	/**
	 * @param string $url
	 * @return string
	 */
	private function getType( string $url, string $handle ): string {
		$extension = $this->fileExtension( $url, $handle );

		if ( ! array_key_exists( $extension, $this->types ) ) {
			throw new RuntimeException(
				sprintf(
					'%s extension is not registered',
					$extension
				)
			);
		}

		return $this->types[ $extension ];
	}

	/**
	 * https://paulund.co.uk/get-the-file-extension-in-php
	 * @param string $file_name_or_url
	 * @param string $handle
	 * @return string
	 */
	private function fileExtension( string $file_name_or_url, string $handle ): string {
		if ( empty( $file_name_or_url ) ) {
			throw new InvalidArgumentException( \sprintf(
				'File name or url must not be empty for "%s"',
				$handle
			) );
		}

		$array = explode( ".", $file_name_or_url );

		if ( count( $array ) <= 1 ) {
			throw new InvalidArgumentException( sprintf(
				'File extension is missing for %s',
				$file_name_or_url
			));
		}

		return strval( end( $array ) );
	}
}
