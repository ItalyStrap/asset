<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\Script;

// phpcs:disable
include_once 'UnitBaseAsset.php';
// phpcs:enable

class ScriptTest extends UnitBaseAsset {

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		parent::_before();
		$this->type = 'script';
		$this->in_footer_or_media = true;
	}

	/**
	 * @return Asset
	 * @throws \ReflectionException
	 */
	protected function getInstance() {
		$sut = new Script( $this->getFile(), $this->getConfig() );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = parent::instanceOk();
		$this->assertInstanceOf( Script::class, $sut, '' );
	}
}
