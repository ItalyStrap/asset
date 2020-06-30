<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

class BaseViewFinderTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var array
	 */
	private $paths;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->paths = [
			'childPath'		=> \codecept_data_dir( 'fixtures/child/css' ),
			'parentPath'	=> \codecept_data_dir( 'fixtures/parent/css' ),
		];
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	/**
	 * Files to search
	 *
	 * path/to/file/asset/css/single.min.css
	 * path/to/file/asset/css/single.css
	 * path/to/file/asset/css/custom.min.css
	 * path/to/file/asset/css/custom.css
	 *
	 * @test
	 */
	public function isShouldReturnThePathForCustomCss() {
		$sut = new \ItalyStrap\Asset\AssetFinder();
		$sut->in( $this->paths );
		$this->assertInstanceOf( \ItalyStrap\Asset\AssetFinder::class, $sut, '' );

		$file = $sut->find( ['custom', 'min'], 'css' );
		$this->assertStringContainsString('custom.css', $file, '' );
	}
}
