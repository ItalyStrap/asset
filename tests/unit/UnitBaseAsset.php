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
	 * @var ObjectProphecy
	 */
	protected $file;

	/**
	 * @return ConfigInterface
	 */
	public function getConfig(): ConfigInterface {
		return $this->config->reveal();
	}

	/**
	 * @return FileInterface
	 */
	public function getFile(): FileInterface {
		return $this->file->reveal();
	}

	/**
	 * @return Asset
	 * @throws ReflectionException
	 */
	abstract protected function getInstance();

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->config = $this->prophesize( ConfigInterface::class );
		$this->file = $this->prophesize( File::class );

		$this->config->has('handle')->willReturn(true);
		$this->config->get('handle')->willReturn('handle');
//		$this->config->handle = 'handle';
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

		$this->expectException( InvalidArgumentException::class );
		$this->getInstance();
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
		$this->assertEquals(1, $called, '');
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
		$this->file->url()->willReturn( 'url' );
		$this->file->version()->willReturn( '42' );

		/**
		 * Only for Script
		 */
		$this->config->get( 'deps', [] )->willReturn( [] );
		$this->config->get( 'in_footer', false )->willReturn( true );

		/**
		 * Only for Style
		 */
		$this->config->get( 'media', 'all' )->willReturn( 'all' );

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
		$sut->register();
	}

	/**
	 * @test
	 */
	public function itShouldEnqueue() {
		$this->commonRegisterEnqueue('wp_enqueue_%s');
		$sut = $this->getInstance();
		$sut->enqueue();
	}
}
