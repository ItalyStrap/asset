<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\ConfigBuilder;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\Style;
use ItalyStrap\Asset\Version\EmptyVersion;
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
		$this->finder = $this->prophesize( FinderInterface::class );
	}

	protected function _after() {
	}

	private function getInstance(): ConfigBuilder {
		$sut = new ConfigBuilder(
			$this->getFinder(),
			new EmptyVersion(),
			$_SERVER['TEST_SITE_WP_URL'],
			$_SERVER['WP_ROOT_FOLDER']
		);
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
	public function itShouldThrownRunTimeExceptionIfTypeIsAlreadyRegistered() {
		$sut = $this->getInstance();
		$sut->withType( 'css', Style::class );

		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'css as already been registered' );
		$sut->withType( 'css', Style::class );
	}

	/**
	 * @test
	 */
	public function itShouldGetParsedConfigWithDefaultValues() {
		$sut = $this->getInstance();
		$sut->withType( 'css', Style::class );

		$sut->addConfig([
			[
				Asset::HANDLE	=> 'handle',
				Asset::URL		=> 'url.css',
			]
		]);

		$is_called = 0;
		foreach ( $sut->parsedConfig() as $items ) {
			$this->assertSame('', $items['file_name'], '');
			$this->assertSame(true, $items['load_on'], '');
			$this->assertSame([], $items[ Asset::DEPENDENCIES ], '');
			$this->assertSame( true, $items[Asset::IN_FOOTER], '');

			$is_called++;
		}

		$this->assertTrue(\boolval( $is_called ), '$configs is empty');
	}

	/**
	 * @test
	 */
	public function itShouldGetParsedConfig() {
		$sut = $this->getInstance();
		$sut->withType( 'css', Style::class );
		$sut->withType( 'js', Script::class );
		$sut->addConfig( require codecept_data_dir('/fixtures/_config/styles.php') );
		$sut->addConfig( require codecept_data_dir('/fixtures/_config/scripts.php') );

		$is_called = 0;
		foreach ( $sut->parsedConfig() as $items ) {
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

		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Url must not be empty');

		foreach ( $sut->parsedConfig() as $items ) {
		}
	}

	/**
	 * @test
	 */
	public function itShouldThrownInvalidArgumentExceptionIfExtensionIsMissing() {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('File extension is missing for test-with-no-extension');

		$sut = $this->getInstance();
		$sut->addConfig( [
			[
				Asset::HANDLE	=> 'test',
				Asset::URL 		=> 'test-with-no-extension',
			]
		] );

		foreach ( $sut->parsedConfig() as $items ) {
		}
	}

	/**
	 * @test
	 */
	public function itShouldThrownInvalidArgumentExceptionIfExtensionIsNotRegistered() {
		$sut = $this->getInstance();
		$sut->addConfig( [
			[
				Asset::HANDLE	=> 'test',
				Asset::URL 		=> 'test-with-extension.test',
			]
		] );

		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('test extension is not registered');

		foreach ( $sut->parsedConfig() as $items ) {
		}
	}

	/**
	 * @test
	 */
	public function itShouldIterate() {
		$config = require codecept_data_dir('/fixtures/_config/new-assets-config.php');

//		codecept_debug( $config[0]['file_name'] );

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
		$sut->withType( 'css', Style::class );

		$sut->addConfig( $config );
		$parsedConfig = $sut->parsedConfig();

		$is_called = 0;
		foreach ( $parsedConfig as $items ) {
//			codecept_debug($items);
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
