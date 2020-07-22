<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Asset\Version\VersionInterface;
use SplFileInfo;
use function rtrim;
use function str_replace;
use function strval;

class File implements FileInterface {

	/**
	 * @var SplFileInfo
	 */
	private $file;

	/**
	 * @var string
	 */
	private $base_url;

	/**
	 * @var string
	 */
	private $base_path;

	/**
	 * @var VersionInterface
	 */
	private $version;

	/**
	 * File constructor.
	 * @param SplFileInfo $splFileInfo
	 * @param Version\VersionInterface $version
	 * @param string $base_url
	 * @param string $base_path
	 */
	public function __construct(
		SplFileInfo $splFileInfo,
		VersionInterface $version,
		string $base_url,
		string $base_path
	) {
		$this->file = $splFileInfo;
		$this->version = $version;
		$this->base_url = rtrim( $base_url, '\/' );
		$this->base_path = rtrim( $base_path, '\/');
	}

	/**
	 * @inheritDoc
	 */
	public function version() {
		if ( $this->version->hasVersion() ) {
			return $this->version->version();
		}

		return strval( $this->file->getMTime() );
	}

	/**
	 * @inheritDoc
	 */
	public function url(): string {
		return $this->generateUrl();
	}

	/**
	 * @return string
	 */
	private function normalizePath( string $path ): string {
		return str_replace( '\\', '/', $path );
	}

	/**
	 * @return string
	 */
	private function generateUrl(): string {
		return str_replace(
			$this->normalizePath( $this->base_path ),
			$this->base_url,
			$this->normalizePath( strval( $this->file->getRealPath() ) )
		);
	}
}
