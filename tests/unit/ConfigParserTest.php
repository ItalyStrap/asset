<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Asset\ConfigBuilder;
use UnitTester;

class ConfigParserTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;
	
	protected function _before() {
	}

	protected function _after() {
	}

	private function getInstance(): ConfigBuilder {
		$sut = new ConfigBuilder();
		$this->assertInstanceOf( ConfigBuilder::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = $this->getInstance();
	}

	/**
	 * @test
	 */
	public function itShouldAddConfig() {
		$sut = $this->getInstance();
		$sut->addConfig( require codecept_data_dir('/fixtures/_config/styles.php') );
		$sut->addConfig( require codecept_data_dir('/fixtures/_config/styles.php') );
		$sut->addConfig( require codecept_data_dir('/fixtures/_config/scripts.php') );
	}

	/**
	 * @test
	 */
	public function itShouldGetParsedConfig() {
		$sut = $this->getInstance();
		$sut->addConfig( require codecept_data_dir('/fixtures/_config/styles.php') );
		$sut->addConfig( require codecept_data_dir('/fixtures/_config/scripts.php') );
		$configs = $sut->parsedConfig();

		$is_called = 0;
		foreach ( $configs as $items ) {
			$this->assertArrayHasKey('handle', $items, '');
			$this->assertArrayHasKey('url', $items, '');
			$this->assertArrayHasKey('type', $items, '');
			$is_called++;
		}

		$this->assertTrue(\boolval( $is_called ), '$configs is empty');
	}
}
