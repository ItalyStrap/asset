<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use Inpsyde\Assets\Loader\LoaderInterface;
use ItalyStrap\Asset\Adapters\InpsydeGeneratorLoader;
use UnitTester;

class InpsydeGeneratorLoaderTest extends Unit
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
	 * @return InpsydeGeneratorLoader
	 */
	private function getInstance(): InpsydeGeneratorLoader {
		$sut = new InpsydeGeneratorLoader();
		$this->assertInstanceOf( LoaderInterface::class, $sut, '' );
		$this->assertInstanceOf( InpsydeGeneratorLoader::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
    public function instanceOk()
    {
		$sut = $this->getInstance();
	}
}