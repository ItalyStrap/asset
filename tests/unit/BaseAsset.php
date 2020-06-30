<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\File;
use ItalyStrap\Asset\FileInterface;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;

abstract class BaseAsset extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $type;
    protected $in_footer_or_media;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	protected $config;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	protected $file;

	/**
	 * @return \ItalyStrap\Config\ConfigInterface
	 */
	public function getConfig(): \ItalyStrap\Config\ConfigInterface {
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
	 * @throws \ReflectionException
	 */
	abstract protected function getInstance();

	protected function _before()
    {
    	$this->config = $this->prophesize( \ItalyStrap\Config\ConfigInterface::class );
    	$this->file = $this->prophesize( File::class );

		$this->config->has('handle')->willReturn(true);
		$this->config->handle = 'handle';
    }

    protected function _after()
    {
    }

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = $this->getInstance();
		$this->assertInstanceOf( \ItalyStrap\Asset\Asset::class, $sut, '' );
		return $sut;
    }

	/**
	 * @test
	 */
	public function itShouldThrownInvalidArgumentExceptionIfHandleIsNotDefined() {
		$this->config->has('handle')->willReturn(false);

		$this->expectException( \InvalidArgumentException::class );
		$this->getInstance();
    }

	/**
	 * @test
	 */
	public function itShouldBeRegistered() {

		$func_name = \sprintf(
			'wp_%s_is',
			$this->type
		);

		$called = 0;
		\tad\FunctionMockerLe\define( $func_name, function (
			$handle, $list = 'enqueued'
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

		$func_name = \sprintf(
			'wp_%s_is',
			$this->type
		);

		$called = 0;
		\tad\FunctionMockerLe\define( $func_name, function (
			$handle, $list = 'enqueued'
		) use ( &$called ) {
			$called++;
			return true;
		} );

		$sut = $this->getInstance();

		$this->assertTrue( $sut->isEnqueued(), '' );
		$this->assertEquals(1, $called, '');
	}

	/**
	 * @test
	 */
	public function itShouldRegister() {

		$this->file->url()->willReturn('url');
		$this->file->version()->willReturn('42');

		/**
		 * Only for Script
		 */
		$this->config->get( 'deps', Argument::type('array') )->willReturn([]);
		$this->config->get( 'in_footer', Argument::type('bool') )->willReturn(true);

		/**
		 * Only for Style
		 */
		$this->config->get( 'media', Argument::type('string') )->willReturn('all');

		$func_name = \sprintf(
			'wp_register_%s',
			$this->type
		);

		\tad\FunctionMockerLe\define( $func_name, function (
			string $handle,
			string $src,
			array $deps = [],
			$ver = false,
			$in_footer_or_media = false ) {
			Assert::assertStringContainsString('handle', $handle, '');
			Assert::assertStringContainsString('url', $src, '');
			Assert::assertIsArray($deps, '');
			Assert::assertStringContainsString('42', (string)$ver, '');
			Assert::assertEquals($this->in_footer_or_media, $in_footer_or_media, '');
			return true;
		} );

		$sut = $this->getInstance();
		$sut->register();
	}

	/**
	 * @test
	 */
	public function itShouldEnqueue() {

		$this->file->url()->willReturn('url');
		$this->file->version()->willReturn('42');

		/**
		 * Only for Script
		 */
		$this->config->get( 'deps', Argument::type('array') )->willReturn([]);
		$this->config->get( 'in_footer', Argument::type('bool') )->willReturn(true);

		/**
		 * Only for Style
		 */
		$this->config->get( 'media', Argument::type('string') )->willReturn('all');

		$func_name = \sprintf(
			'wp_enqueue_%s',
			$this->type
		);

		\tad\FunctionMockerLe\define( $func_name, function (
			string $handle,
			string $src,
			array $deps = [],
			$ver = false,
			$in_footer_or_media = false ) {
			Assert::assertStringContainsString('handle', $handle, '');
			Assert::assertStringContainsString('url', $src, '');
			Assert::assertIsArray($deps, '');
			Assert::assertStringContainsString('42', (string)$ver, '');
			Assert::assertEquals($this->in_footer_or_media, $in_footer_or_media, '');
			return true;
		} );

		$sut = $this->getInstance();
		$sut->enqueue();
	}
}