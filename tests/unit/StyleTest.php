<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\Style;

// phpcs:disable
include_once 'UnitBaseAsset.php';
// phpcs:enable

class StyleTest extends UnitBaseAsset {

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		parent::_before();
		$this->type = 'style';
		$this->in_footer_or_media = 'all';
	}

	/**
	 * @return Asset
	 * @throws \ReflectionException
	 */
	protected function getInstance() {
		$sut = new Style( $this->getConfig() );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = parent::instanceOk();
		$this->assertInstanceOf( Style::class, $sut, '' );
	}
}
