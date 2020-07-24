<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\ConfigBuilder;
use ItalyStrap\Finder\Finder;
use ItalyStrap\Finder\FinderInterface;
use Prophecy\Argument;
use UnitTester;

class ConfigParserTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $finder;

	/**
	 * @return FinderInterface
	 */
	public function getFinder(): FinderInterface {
		return $this->finder->reveal();
	}

	protected function _before() {
//		$this->finder = $this->prophesize( FinderInterface::class );
		$this->finder = $this->prophesize( Finder::class );
	}

	protected function _after() {
	}

	private function getInstance(): ConfigBuilder {
		$sut = new ConfigBuilder( $this->getFinder() );
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

	/**
	 * @test
	 */
	public function itShouldThrownInvalidArgumentExceptionIfUrlIsEmpty() {
		$sut = $this->getInstance();
		$sut->addConfig( [
			[
				Asset::HANDLE => 'test'
			]
		] );

		$configs = $sut->parsedConfig();

		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Url must not be empty');

		foreach ( $configs as $items ) {
		}
	}

	/**
	 * @test
	 */
	public function itShouldThrownInvalidArgumentExceptionIfExtensionIsMissing() {
		$sut = $this->getInstance();
		$sut->addConfig( [
			[
				Asset::HANDLE	=> 'test',
				Asset::URL 		=> 'test-with-no-extension',
			]
		] );

		$configs = $sut->parsedConfig();

		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('File extension is missing');

		foreach ( $configs as $items ) {
		}
	}

	/**
	 * @test
	 */
	public function itShouldThrownInvalidArgumentExceptionIfExtensionIsMissingytjtyjt() {
		$sut = $this->getInstance();
		$sut->addConfig( [
			[
				Asset::HANDLE	=> 'test',
				Asset::URL 		=> 'test-with-extension.test',
			]
		] );

		$configs = $sut->parsedConfig();

		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('test extension is not registered');

		foreach ( $configs as $items ) {
		}
	}

	/**
	 * @test
	 */
	public function itShouldIterate() {
		$config = require codecept_data_dir('/fixtures/_config/new-assets-config.php');

		codecept_debug( $config[0]['file_name'] );

		$style_css = codecept_data_dir('/fixtures/parent/css/style.css');

		$this->assertFileExists(
			codecept_data_dir('/fixtures/parent/css/style.css'),
			''
		);

		$this->finder->getIterator()->willReturn(new \ArrayIterator(
			[
				new \SplFileInfo($style_css)
			]
		));

		$this->finder->names( $config[0]['file_name'] )->will( function () {
		} )->shouldBeCalled(1);


		$sut = $this->getInstance();
		$sut->addConfig( $config );
		$parsedConfig = $sut->parsedConfig();

		$is_called = 0;
		foreach ( $parsedConfig as $items ) {
			codecept_debug($items);
			$this->assertArrayHasKey('url', $items, '');
			$this->assertArrayHasKey('version', $items, '');

			$this->assertSame(
				'http://moduli.test/wp-content/plugins/asset/tests/_data/fixtures/parent/css/style.css',
				$items['url'],
				''
			);

			$this->assertSame(\strval( \filemtime( $style_css ) ), $items['version'], '');

			$is_called++;
		}

		$this->assertTrue(\boolval( $is_called ), '$configs is empty');
	}
}
