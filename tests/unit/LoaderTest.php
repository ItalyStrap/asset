<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\AssetLoader;

class LoaderTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	private function getInstance() {
		$sut = new AssetLoader();
		$this->assertInstanceOf(\ItalyStrap\Asset\LoaderInterface::class, $sut, '');
		$this->assertInstanceOf(\ItalyStrap\Asset\AssetLoader::class, $sut, '');
		return $sut;
	}

	/**
	 * @test
	 */
//    public function instanceOk()
//    {
//		$sut = $this->getInstance();
//    }
}
