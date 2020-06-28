<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Version\EmptyVersion;
use ItalyStrap\Asset\Version\VersionInterface;
use UnitTester;

class EmptyVersionTest extends Unit
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
	 * @return EmptyVersion
	 */
	private function getInstance() {
    	$sut = new EmptyVersion();
    	$this->assertInstanceOf( VersionInterface::class, $sut, '' );
    	$this->assertInstanceOf( EmptyVersion::class, $sut, '' );
    	return $sut;
	}

	/**
	 * @test
	 */
    public function instanceOk()
    {
		$sut = $this->getInstance();
    }

	/**
	 * @test
	 */
    public function itShouldNotHaveVersion()
    {
		$sut = $this->getInstance();
		$this->assertFalse( $sut->hasVersion(), '');
    }

	/**
	 * @test
	 */
    public function itShouldReturnEmptyVersion()
    {
		$sut = $this->getInstance();
		$this->assertEmpty( $sut->version(), '' );
    }
}