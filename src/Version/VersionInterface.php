<?php

namespace ItalyStrap\Asset\Version;

interface VersionInterface {

	/**
	 * @param \SplFileInfo $fileInfo
	 * @param array $config
	 * @return mixed|string|bool|null
	 */
	public function version( \SplFileInfo $fileInfo, array $config );
}
