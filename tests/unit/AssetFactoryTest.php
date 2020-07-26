<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\AssetFactory;
use ItalyStrap\Asset\AssetInterface;
use ItalyStrap\Asset\Style;
use ItalyStrap\Config\ConfigFactory;
use stdClass;
use UnitTester;

class AssetFactoryTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

	/**
	 * @test
	 */
    public function instanceOk()
    {
		$asset = ( new AssetFactory() )->make( ConfigFactory::make([
			'type'	=> Style::class,
			Asset::HANDLE	=> 'handle',
		]) );

		$this->assertInstanceOf( AssetInterface::class, $asset, '' );
		$this->assertInstanceOf( Asset::class, $asset, '' );
    }

	/**
	 * @test
	 */
    public function itShouldThrownInvalidArgumentExceptionIfTypeIsNotAssetInterface()
    {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage('The class stdClass must implements ItalyStrap\Asset\AssetInterface');

		$asset = ( new AssetFactory() )->make( ConfigFactory::make([
			'type'	=> stdClass::class,
		]) );
    }
}