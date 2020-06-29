<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\AssetStatusInterface;
use ItalyStrap\Asset\File;
use ItalyStrap\Asset\FileInterface;
use Prophecy\Argument;

class BaseAsset extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $type;

	private $config_params = [];

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

	protected function _before()
    {
    	$this->config = $this->prophesize( \ItalyStrap\Config\ConfigInterface::class );
    	$this->file = $this->prophesize( File::class );
    }

    protected function _after()
    {
    }

	protected function setConfigParams( array $params = [] ) {
		$this->config_params = $params;
    }

	protected function getConfigParams() {
		return $this->config_params;
    }

	/**
	 * @test
	 */
	public function instanceOk() {
		$this->config->has(Argument::type('string'))->willReturn(true);
		$this->config->handle = 'handle';

		$sut = $this->getInstance();

		$this->assertInstanceOf( \ItalyStrap\Asset\AssetStatusInterface::class, $sut, '' );
		$this->assertInstanceOf( \ItalyStrap\Asset\Asset::class, $sut, '' );

		return $sut;
    }

	/**
	 * @test
	 */
	public function itShouldThrownInvalidArgumentExceptionIfHandleIsNotDefined() {
		$this->config->has(Argument::type('string'))->willReturn(false);

		$this->expectException( \InvalidArgumentException::class );
		$this->getInstance();
    }
}