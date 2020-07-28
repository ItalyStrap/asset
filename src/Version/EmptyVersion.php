<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Version;

class EmptyVersion implements VersionInterface {

	/**
	 * @inheritDoc
	 */
	public function version( \SplFileInfo $fileInfo, array $config ) {
		return '';
	}
}
