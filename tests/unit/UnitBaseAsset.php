<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\File;
use ItalyStrap\Asset\FileInterface;
use ItalyStrap\Config\ConfigInterface;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use ReflectionException;
use UnitTester;
use function sprintf;
use function tad\FunctionMockerLe\undefineAll;

abstract class UnitBaseAsset extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	protected $type;
	protected $in_footer_or_media;

	/**
	 * @var ObjectProphecy
	 */
	protected $config;

	/**
	 * @return ConfigInterface
	 */
	public function getConfig(): ConfigInterface {
		return $this->config->reveal();
	}

	/**
	 * @return Asset
	 * @throws ReflectionException
	 */
	abstract protected function getInstance();

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->config = $this->prophesize( ConfigInterface::class );

		$this->config->has('handle')->willReturn(true);
		$this->config->get('handle')->willReturn('handle');
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
		undefineAll([
			'wp_script_is',
			'wp_style_is',
			'wp_register_script',
			'wp_register_style',
			'wp_enqueue_script',
			'wp_enqueue_style',
			'wp_localize_script',
		]);
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = $this->getInstance();
		$this->assertInstanceOf( Asset::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldThrownInvalidArgumentExceptionIfHandleIsNotDefined() {
		$this->config->has('handle')->willReturn(false);
		$this->config->get('url')->willReturn('file-name.' . $this->type);

		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage(
			\sprintf(
				'A unique "handle" ID is required for the file-name.%s',
				$this->type
			)
		);
		$this->getInstance();
	}

	/**
	 * @test
	 */
	public function itShouldHaveDefaultLocation() {

		$this->config->get(Asset::LOCATION, Argument::type('string'))->willReturn('wp_enqueue_scripts');

		$sut = $this->getInstance();
		$this->assertSame( 'wp_enqueue_scripts', $sut->location(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldBeRegistered() {

		$func_name = sprintf(
			'wp_%s_is',
			$this->type
		);

		$called = 0;
		\tad\FunctionMockerLe\define( $func_name, function (
			$handle,
			$list = 'enqueued'
		) use ( &$called ) {
			$called++;
			return true;
		} );

		$sut = $this->getInstance();

		$this->assertTrue( $sut->isRegistered(), '' );
		$this->assertEquals(1, $called, "{$func_name} is not called");
	}

	/**
	 * @test
	 */
	public function itShouldBeEnqueued() {

		$func_name = sprintf(
			'wp_%s_is',
			$this->type
		);

		$called = 0;
		\tad\FunctionMockerLe\define( $func_name, function (
			$handle,
			$list = 'enqueued'
		) use ( &$called ) {
			$called++;
			return true;
		} );

		$sut = $this->getInstance();

		$this->assertTrue( $sut->isEnqueued(), '' );
		$this->assertEquals(1, $called, '');
	}

	private function commonRegisterEnqueue( string $func_name_pattern ): void {
		$this->config->has( Asset::LOCALIZE )->willReturn(false);

		$this->config->get( Asset::URL )->willReturn( 'url' );
		$this->config->get( Asset::VERSION )->willReturn( '42' );

		/**
		 * Only for Script
		 */
		$this->config->get( Asset::DEPENDENCIES, [] )->willReturn( [] );
		$this->config->get( Asset::IN_FOOTER, false )->willReturn( true );

		/**
		 * Only for Style
		 */
		$this->config->get( Asset::MEDIA, 'all' )->willReturn( 'all' );

		$func_name = sprintf(
			$func_name_pattern,
			$this->type
		);

		\tad\FunctionMockerLe\define( $func_name, function (
			string $handle,
			string $src,
			array $deps = [],
			$ver = false,
			$in_footer_or_media = false
) {
			Assert::assertStringContainsString( 'handle', $handle, '' );
			Assert::assertStringContainsString( 'url', $src, '' );
			Assert::assertIsArray( $deps, '' );
			Assert::assertStringContainsString( '42', (string)$ver, '' );
			Assert::assertEquals( $this->in_footer_or_media, $in_footer_or_media, '' );
			return true;
		} );
	}

	/**
	 * @test
	 */
	public function itShouldRegister() {
		$this->commonRegisterEnqueue('wp_register_%s');
		$sut = $this->getInstance();
		$result = $sut->register();
		$this->assertTrue($result, '');
	}

	/**
	 * @test
	 */
	public function itShouldEnqueue() {
		$this->commonRegisterEnqueue('wp_enqueue_%s');
		$sut = $this->getInstance();
		$sut->enqueue();
	}

	/**
	 * @test
	 */
	public function itShouldLoad() {
		$sut = $this->getInstance();

		$this->config->get("load_on")->willReturn(true);
		$this->assertTrue( $sut->shouldEnqueue(), 'Should enqueue the asset' );

		$this->config->get("load_on")->willReturn([]);
		$this->assertTrue( $sut->shouldEnqueue(), 'Should enqueue the asset' );

		$this->config->get("load_on")->willReturn(function () {
			return true;
		});
		$this->assertTrue( $sut->shouldEnqueue(), 'Should enqueue the asset' );
	}

	/**
	 * @test
	 */
	public function itShouldNotLoad() {
		$sut = $this->getInstance();

		$this->config->get("load_on")->willReturn(false);
		$this->assertFalse( $sut->shouldEnqueue(), 'Should enqueue the asset' );

		$this->config->get("load_on")->willReturn(function () {
			return false;
		});
		$this->assertFalse( $sut->shouldEnqueue(), 'Should enqueue the asset' );
	}
}
