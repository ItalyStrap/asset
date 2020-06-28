<?php

namespace ItalyStrap\Asset\Version;

interface VersionInterface
{
	/**
	 * @return bool
	 */
	public function hasVersion(): bool;

	/**
	 * @return mixed|string|bool|null
	 */
	public function version();
}