<?php

class AssetsTest extends \Codeception\TestCase\WPTestCase
{

    public function setUp()
    {
        // before
        parent::setUp();

        // your set up methods here

		$this->page_id = $this->factory()->post->create([ 'post_type' => 'page', 'post_title' => 'Page' ]);
		$this->post_id = $this->factory()->post->create([ 'post_type' => 'post', 'post_title' => 'Post' ]);
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
	public function it_should_thrown_exception_if_type_is_not_correct()
	{
		$this->expectException( InvalidArgumentException::class );
		\ItalyStrap\Asset\Asset_Factory::make( [], 'styles' );
		\ItalyStrap\Asset\Asset_Factory::make( [], 'scripts' );
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

	private function get_config_array( $n = '01' )
	{
		$file = sprintf(
			'%s/../_data/config_%s.php',
			__DIR__,
			$n
		);
		return require $file;
	}

	protected function get_instance( $type = 'style', $config_array = [] )
	{
		if ( empty( $config_array ) ) {
			$config_array = $this->get_config_array();
		}

		return \ItalyStrap\Asset\Asset_Factory::make( $config_array[ $type ], $type );
    }

	protected function _make( $type = 'style', $config_array = [] )
	{
		$sut = $this->get_instance( $type, $config_array );
		$sut->register();
		return $sut;
    }

	/**
	 * @test
	 */
    public function it_should_be_instantiable()
    {
    	$expected = '\ItalyStrap\Asset\Asset_Interface';
    	$this->assertInstanceOf( $expected, $this->get_instance( 'style' ) );
    	$this->assertInstanceOf( $expected, $this->get_instance( 'script' ) );

    	$expected = '\ItalyStrap\Asset\Asset_Interface';
    	$this->assertInstanceOf( $expected, $this->_make( 'style' ) );
    	$this->assertInstanceOf( $expected, $this->_make( 'script' ) );
    }

	/**
	 * @test
	 */
	public function it_should_have_asset_enqueued()
	{
		$assets = $this->get_config_array();

		$sut = $this->_make( 'script', $assets );
		$this->assertTrue( $sut->is_enqueued() );

		$sut = $this->_make( 'style', $assets );
		$this->assertTrue( $sut->is_enqueued() );
    }

	/**
	 * @test
	 */
	public function it_should_not_have_asset_enqueued()
	{

		$assets = $this->get_config_array( '02' );

		$sut = $this->_make( 'script', $assets );
		$this->assertFalse( $sut->is_enqueued() );
    }

	/**
	 * @test
	 */
	public function it_should_have_asset_enqueue_only_in_posts()
	{
		$this->go_to( get_permalink( $this->post_id ) );

		$assets = $this->get_config_array( '02' );

		$sut = $this->_make( 'script', $assets );
		$this->assertTrue( $sut->is_enqueued() );
    }
}