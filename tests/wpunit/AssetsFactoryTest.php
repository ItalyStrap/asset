<?php

class AssetsFactoryTest extends \Codeception\TestCase\WPTestCase
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

		$style = $this->config_array['style'];
		$script = $this->config_array['script'];
		$expected = '\ItalyStrap\Asset\Asset_Interface';
		$this->assertInstanceOf( $expected, \ItalyStrap\Asset\Asset_Factory::make( $style[0],'style' ) );
		$this->assertInstanceOf( $expected, \ItalyStrap\Asset\Asset_Factory::make( $script[0],'script' ) );

		$expected = '\ItalyStrap\Asset\Asset';
		$this->assertInstanceOf( $expected, \ItalyStrap\Asset\Asset_Factory::make( $style[0],'style' ) );
		$this->assertInstanceOf( $expected, \ItalyStrap\Asset\Asset_Factory::make( $script[0],'script' ) );

		$expected = '\ItalyStrap\Asset\Style';
		$this->assertInstanceOf( $expected, \ItalyStrap\Asset\Asset_Factory::make( $style[0],'style' ) );

		$expected = '\ItalyStrap\Asset\Script';
		$this->assertInstanceOf( $expected, \ItalyStrap\Asset\Asset_Factory::make( $script[0],'script' ) );
	}

	/**
	 * @test
	 */
	public function it_should_thrown_exception_if_type_is_not_correct()
	{
		$this->expectException( InvalidArgumentException::class );
		\ItalyStrap\Asset\Asset_Factory::make( $this->config_array['style'][0], 'incorrect_type' );
	}

	/**
	 * @test
	 */
	public function it_should_thrown_exception_if_handle_is_not_provided()
	{
		$this->expectException( InvalidArgumentException::class );
		\ItalyStrap\Asset\Asset_Factory::make( [], 'style' );
		\ItalyStrap\Asset\Asset_Factory::make( [], 'script' );
	}

}