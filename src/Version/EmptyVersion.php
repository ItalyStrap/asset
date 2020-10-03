<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Version;

final class EmptyVersion implements VersionInterface {

	/**
	 * @inheritDoc
	 */
	public function version( \SplFileInfo $fileInfo, array $config ) {
		return '';
	}
}
