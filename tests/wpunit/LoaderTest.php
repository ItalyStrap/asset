<?php

class LoaderTest extends \Codeception\TestCase\WPTestCase
{

    public function setUp()
    {
        // before
        parent::setUp();

		$this->config_array = require __DIR__ . '/../_data/config.php';

        // your set up methods here
    }

    public function tearDown()
    {
        // your tear down methods here

        // then
        parent::tearDown();
    }

	/**
	 * @test
	 */
	public function it_should_be_instantiable()
	{
		$expected = '\ItalyStrap\Asset\Loader_Interface';
		$this->assertInstanceOf( $expected, new \ItalyStrap\Asset\Loader() );

		$expected = '\ItalyStrap\Asset\Loader';
		$this->assertInstanceOf( $expected, new \ItalyStrap\Asset\Loader() );
	}

	/**
	 * @test
	 */
	public function it_should_be_handle_assets()
	{
		( new \ItalyStrap\Asset\Loader() )->run( $this->config_array );
	}

}