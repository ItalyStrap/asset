<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\AssetInterface;
use ItalyStrap\Asset\Loader\GeneratorLoader;
use ItalyStrap\Asset\Style;
use ItalyStrap\Config\ConfigFactory;
use UnitTester;

class LoaderTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	private function getInstance(): GeneratorLoader {
		$sut = new GeneratorLoader();
//		$this->assertInstanceOf(\ItalyStrap\Asset\LoaderInterface::class, $sut, '');
		$this->assertInstanceOf( GeneratorLoader::class, $sut, '');
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = $this->getInstance();
	}

	function getConfig() {
		yield ConfigFactory::make([
			Asset::HANDLE	=> 'handle',
			'type'			=> Style::class,
		]);
	}

	/**
	 * @test
	 */
	public function itShouldLoad() {
		$sut = $this->getInstance();
		$assets = $sut->load( $this->getConfig() );

		$should_load = 0;
		foreach ( $assets as $asset ) {
			$this->assertInstanceOf( AssetInterface::class, $asset, '' );
			$should_load++;
		}

		$this->assertTrue(\boolval( $should_load ), '$assets is empty');
	}
}
