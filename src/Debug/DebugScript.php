<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Debug;

use ItalyStrap\Asset\Script;
use ItalyStrap\Config\ConfigInterface;

final class DebugScript extends DebugAsset {

	/**
	 * @param ConfigInterface $config
	 * @throws \ReflectionException
	 */
	protected function getAssetInstance( ConfigInterface $config ): void {
		$this->asset = new Script( $config );
	}
}
