<?php

class AssetsTest extends \Codeception\TestCase\WPTestCase
{

    public function setUp()
    {
        // before
        parent::setUp();

		$this->config_array = require __DIR__ . '/../_data/config.php';

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
		$this->go_to( get_permalink( $this->page_id ) );

		$assets = $this->get_config_array( '02' );
//		codecept_debug($assets);
		$sut = $this->_make( 'style', $assets );
//		codecept_debug($sut);
		codecept_debug($sut->is_enqueued());
//		$this->assertFalse( $sut->is_enqueued() );

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

		$sut = $this->_make( 'style', $assets );
		$this->assertTrue( $sut->is_enqueued() );
    }
}