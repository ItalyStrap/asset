<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Version;

class EmptyVersion implements VersionInterface {

	/**
	 * @inheritDoc
	 */
	public function version() {
		return '';
	}

	/**
	 * @inheritDoc
	 */
	public function hasVersion(): bool {
		return false;
	}
}
