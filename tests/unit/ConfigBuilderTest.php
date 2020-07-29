<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\AssetInterface;
use ItalyStrap\Asset\ConfigBuilder;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\Style;
use ItalyStrap\Asset\Version\EmptyVersion;
use ItalyStrap\Asset\Version\VersionInterface;
use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\Finder\Finder;
use ItalyStrap\Finder\FinderInterface;
use Prophecy\Argument;
use UnitTester;

class ConfigBuilderTest extends Unit {


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
			$_SERVER[ 'TEST_SITE_WP_URL' ],
			$_SERVER[ 'WP_ROOT_FOLDER' ]
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
		$sut->addConfig( require codecept_data_dir( '/fixtures/_config/styles.php' ) );
		$sut->addConfig( require codecept_data_dir( '/fixtures/_config/scripts.php' ) );
	}

	/**
	 * @test
	 */
	public function itShouldThrownRunTimeExceptionIfTypeIsAlreadyRegistered() {
		$sut = $this->getInstance();
		$sut->withType( Style::EXTENSION, Style::class );
		$sut->withType( Script::EXTENSION, Script::class );

		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'css as already been registered' );
		$sut->withType( Style::EXTENSION, Style::class );

		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'js as already been registered' );
		$sut->withType( Script::EXTENSION, Script::class );
	}

	/**
	 * @test
	 */
	public function itShouldThrownRunTimeExceptionIfFinderForTypeIsAlreadyRegistered() {
		$sut = $this->getInstance();
		$sut->withFinderForType( Style::EXTENSION, $this->getFinder() );
		$sut->withFinderForType( Script::EXTENSION, $this->getFinder() );

		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( get_class( $this->getFinder() ) . ' for css as already been registered' );
		$sut->withFinderForType( Style::EXTENSION, $this->getFinder() );

		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( get_class( $this->getFinder() ) . ' for js as already been registered' );
		$sut->withFinderForType( Script::EXTENSION, $this->getFinder() );
	}

	/**
	 * @test
	 */
	public function itShouldThrownRunTimeExceptionIfFinderForTypeIsNotRegistered() {
		$this->finder->names( Argument::exact('style.css') )->will( function () {
		} )->shouldNotBeCalled();

		$sut = $this->getInstance();

		$sut->addConfig( [
			[
				Asset::HANDLE				=> 'test',
				ConfigBuilder::FILE_NAME	=> 'style.css',
			]
		] );

		$sut->withType(Style::EXTENSION, Style::class);

		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'A finder for css extension is not registered' );

		/** @var ConfigInterface $config */
		foreach ($sut->parsedConfig() as $config) {
		}
	}

	public function urlConfigProvider() {

		yield 'slashes'	=> [
			[
				Asset::HANDLE	=> 'test',
				Asset::URL		=> '//test-with-extension.css',
			]
		];

		yield 'back compat with file key'	=> [
			[
				Asset::HANDLE	=> 'test',
				'file'			=> '//test-with-extension.css',
			]
		];
	}

	/**
	 * @test
	 * @group url
	 * @dataProvider urlConfigProvider()
	 */
	public function itShouldGenerateUrlFromProvider( $config_from_provider ) {
		$this->finder->names( Argument::exact('//test-with-extension.css') )->will( function () {
		} )->shouldNotBeCalled();

		$sut = $this->getInstance();
		$sut->addConfig( [
			$config_from_provider
		] );

		$sut->withType(Style::EXTENSION, Style::class);

		/** @var ConfigInterface $config_obj */
		foreach ($sut->parsedConfig() as $config_obj) {
			$this->assertStringContainsString(
				'//test-with-extension.css',
				$config_obj->get( Asset::URL ),
				''
			);
		}
	}

	/**
	 * @test
	 * @group url
	 */
	public function itShouldGenerateUrlFromFileName() {
		$style_css = codecept_data_dir( '/fixtures/parent/css/style.css' );
		$this->assertFileExists( $style_css, '');
		$this->finder->getIterator()->willReturn( new \ArrayIterator(
			[
				new \SplFileInfo( $style_css )
			]
		) );
		$this->finder->names( 'style.css' )->will( function () {
		} )->shouldBeCalled( 1 );


		$sut = $this->getInstance();
		$sut->addConfig( [
			[
				Asset::HANDLE				=> 'test',
				ConfigBuilder::FILE_NAME	=> 'style.css',
			]
		] );

		$sut->withType(Style::EXTENSION, Style::class);
		$sut->withFinderForType( Style::EXTENSION, $this->getFinder() );

		/** @var ConfigInterface $config_obj */
		foreach ($sut->parsedConfig() as $config_obj) {
			$this->assertStringContainsString(
				'/tests/_data/fixtures/parent/css/style.css',
				$config_obj->get( Asset::URL ),
				''
			);
		}
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

		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'File name or url must not be empty' );

		foreach ($sut->parsedConfig() as $items) {
		}
	}

	/**
	 * @test
	 */
	public function itShouldHaveVersionFromConfig() {
		$sut = $this->getInstance();
		$sut->addConfig( [
			[
				Asset::HANDLE	=> 'test',
				Asset::URL	 	=> '//test.css',
				Asset::VERSION	=> 42,
			]
		] );

		$sut->withType( Style::EXTENSION, Style::class );

		/** @var ConfigInterface $config */
		foreach ($sut->parsedConfig() as $config) {
			$this->assertSame( 42, $config->get( Asset::VERSION ), '' );
		}
	}

	/**
	 * @test
	 */
	public function itShouldHaveVersionFromFileInfo() {
		$style_css = codecept_data_dir( '/fixtures/parent/css/style.css' );
		$this->assertFileExists( $style_css, '');
		$file_info = new \SplFileInfo( $style_css );
		$this->finder->getIterator()->willReturn( new \ArrayIterator(
			[
				$file_info
			]
		) );
		$this->finder->names( 'style.css' )->will( function () {
		} )->shouldBeCalled( 1 );

		$sut = $this->getInstance();
		$sut->addConfig( [
			[
				Asset::HANDLE	=> 'test',
				ConfigBuilder::FILE_NAME	=> 'style.css',
				Asset::VERSION	=> 42, // This will not be used
			]
		] );

		$sut->withType( Style::EXTENSION, Style::class );
		$sut->withFinderForType( Style::EXTENSION, $this->getFinder() );

		/** @var ConfigInterface $config */
		foreach ($sut->parsedConfig() as $config) {
			$this->assertSame(
				\strval( $file_info->getMTime() ),
				$config->get( Asset::VERSION ),
				''
			);
			$this->assertNotSame(42, $config->get( Asset::VERSION ), '');
		}
	}

	/**
	 * @test
	 */
	public function itShouldHaveCustomVersion() {
		$style_css = codecept_data_dir( '/fixtures/parent/css/style.css' );
		$this->assertFileExists( $style_css, '');
		$file_info = new \SplFileInfo( $style_css );
		$this->finder->getIterator()->willReturn( new \ArrayIterator(
			[
				$file_info
			]
		) );
		$this->finder->names( 'style.css' )->will( function () {
		} )->shouldBeCalled( 1 );

		$sut = $this->getInstance();
		$config_ = [
			Asset::HANDLE	=> 'test',
			ConfigBuilder::FILE_NAME	=> 'style.css',
//			Asset::VERSION	=> 42, // This will not be used
		];
		$sut->addConfig( [
			$config_
		] );

		$sut->withType( Style::EXTENSION, Style::class );
		$sut->withFinderForType( Style::EXTENSION, $this->getFinder() );
		$version = $this->prophesize( VersionInterface::class );
		$version->version(Argument::any(), Argument::type('array'))->willReturn('55')->shouldBeCalled(1);
		$sut->withVersion( $version->reveal() );

		$called = 0;
		/** @var ConfigInterface $config */
		foreach ($sut->parsedConfig() as $config) {
			$this->assertSame('55', $config->get( Asset::VERSION ), '');
			$called++;
		}
		$this->assertTrue( \boolval( $called ) );
	}

	/**
	 * @test
	 */
	public function itShouldGetParsedConfigWithDefaultValues() {
		$sut = $this->getInstance();
		$sut->withType( 'css', Style::class );

		$sut->addConfig( [
			[
				Asset::HANDLE => 'handle',
				Asset::URL => '//url.css',
			]
		] );

		$is_called = 0;
		foreach ($sut->parsedConfig() as $items) {
			$this->assertSame( '', $items[ ConfigBuilder::FILE_NAME ], '' );
			$this->assertSame( true, $items[ Asset::SHOULD_LOAD ], '' );
			$this->assertSame( [], $items[ Asset::DEPENDENCIES ], '' );
			$this->assertSame( true, $items[ Asset::IN_FOOTER ], '' );
			$this->assertSame( null, $items[ Asset::VERSION ], '' );

			$is_called ++;
		}

		$this->assertTrue( \boolval( $is_called ), '$configs is empty' );
	}

	/**
	 * @test
	 */
	public function itShouldGetParsedConfigFromOldConfiguration() {
		$sut = $this->getInstance();
		$sut->withType( Style::EXTENSION, Style::class );
		$sut->withType( Script::EXTENSION, Script::class );
		$sut->addConfig( require codecept_data_dir( '/fixtures/_config/styles.php' ) );
		$sut->addConfig( require codecept_data_dir( '/fixtures/_config/scripts.php' ) );

		$is_called = 0;
		foreach ($sut->parsedConfig() as $items) {
			$this->assertArrayHasKey( 'handle', $items, '' );
			$this->assertArrayHasKey( 'url', $items, '' );
			$this->assertArrayHasKey( 'type', $items, '' );
			$is_called ++;
		}

		$this->assertTrue( \boolval( $is_called ), '$configs is empty' );
	}

	/**
	 * @test
	 */
	public function itShouldThrownInvalidArgumentExceptionIfExtensionIsMissing() {

		$sut = $this->getInstance();
		$sut->addConfig( [
			[
				Asset::HANDLE => 'test',
				Asset::URL => '//test-with-no-extension',
			]
		] );


		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'File extension is missing for //test-with-no-extension' );

		foreach ($sut->parsedConfig() as $items) {
		}
	}

	/**
	 * @test
	 */
	public function itShouldThrownInvalidArgumentExceptionIfExtensionIsNotRegistered() {
		$sut = $this->getInstance();
		$sut->addConfig( [
			[
				Asset::HANDLE => 'test',
				Asset::URL => '//test-with-extension.test',
			]
		] );

		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'test extension is not registered' );

		foreach ($sut->parsedConfig() as $items) {
		}
	}

	/**
	 *
	 */
	public function itShouldIterate() {
		$config = require codecept_data_dir( '/fixtures/_config/new-assets-config.php' );
		$style_css = codecept_data_dir( '/fixtures/parent/css/style.css' );

		$this->assertFileExists(
			codecept_data_dir( '/fixtures/parent/css/style.css' ),
			''
		);

		$this->finder->getIterator()->willReturn( new \ArrayIterator(
			[
				new \SplFileInfo( $style_css )
			]
		) );

		$this->finder->names( $config[ 0 ][ 'file_name' ] )->will( function () {
		} )->shouldBeCalled( 1 );


		$sut = $this->getInstance();
		$sut->withType( Style::EXTENSION, Style::class );
		$sut->withFinderForType( Style::EXTENSION, $this->getFinder() );

		$sut->addConfig( $config );
		$parsedConfig = $sut->parsedConfig();

		$is_called = 0;
		foreach ($parsedConfig as $items) {
//			codecept_debug($items);
			$this->assertArrayHasKey( 'url', $items, '' );
			$this->assertArrayHasKey( 'version', $items, '' );

			$this->assertSame(
				'http://moduli.test/wp-content/plugins/asset/tests/_data/fixtures/parent/css/style.css',
				$items[ 'url' ],
				''
			);

			$this->assertSame( \strval( \filemtime( $style_css ) ), $items[ 'version' ], '' );

			$is_called ++;
		}

		$this->assertTrue( \boolval( $is_called ), '$configs is empty' );
	}
}
