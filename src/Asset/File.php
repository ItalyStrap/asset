<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Asset\Version\VersionInterface;
use SplFileInfo;
use function rtrim;
use function str_replace;
use function strval;

class File implements FileInterface
{
	/**
	 * @var SplFileInfo
	 */
	private $file;
	private $base_url;
	private $base_path;
	/**
	 * @var VersionInterface
	 */
	private $version;

	/**
	 * File constructor.
	 * @param SplFileInfo $splFileInfo
	 * @param Version\VersionInterface $version
	 * @param $base_url
	 * @param $base_path
	 */
	public function __construct( SplFileInfo $splFileInfo, VersionInterface $version, $base_url, $base_path ) {
		$this->file = $splFileInfo;
		$this->version = $version;
		$this->base_url = rtrim( $base_url, '\/' );
		$this->base_path = rtrim( $base_path, '\/');
	}

	public function version(): string {
		if ( $this->version->hasVersion() ) {
			return $this->version->version();
		}

		return strval( $this->file->getMTime() );
	}

	public function url(): string {
		return $this->generateUrl();
	}

	/**
	 * @return string
	 */
	private function normalizedString(): string {
		return str_replace( '\\', '/', $this->file->getRealPath() );
	}

	/**
	 * @return string
	 */
	private function generateUrl(): string {
		return str_replace( $this->base_path, $this->base_url, $this->normalizedString() );
	}
}
