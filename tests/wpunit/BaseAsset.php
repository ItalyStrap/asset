<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\AssetFinder;
use ItalyStrap\Asset\AssetStatusInterface;

abstract class BaseAsset extends \Codeception\TestCase\WPTestCase
{

    public function setUp(): void
    {
        // before
        parent::setUp();

        // your set up methods here

		$this->paths = [
			'childPath'		=> \codecept_data_dir( 'fixtures/child/css' ),
			'parentPath'	=> \codecept_data_dir( 'fixtures/parent/css' ),
		];

		$this->finder = new AssetFinder();
		$this->finder->in( $this->paths );

    }

    public function tearDown(): void
    {
        // your tear down methods here

        // then
        parent::tearDown();
    }

	/**
	 * @return AssetStatusInterface
	 */
    abstract protected function getInstance();

	/**
	 * @test
	 */
	public function itShouldHaveAssetEnqueued()
	{
		$sut = $this->getInstance();

		$sut->register();
		$this->assertTrue( $sut->isRegistered() );
		$this->assertFalse( $sut->isEnqueued() );

		$sut->enqueue();
		$this->assertTrue( $sut->isEnqueued() );
    }

	/**
	 * @test
	 */
	public function itfgnsfgnfsx()
	{
		\wp_enqueue_script(
			'ciao',
			'url',
			[],
			'42',
			true
		);
		\wp_enqueue_script(
			'bello',
			'url',
			[],
			'4242',
			true
		);

//		codecept_debug( wp_scripts()->registered['bello'] );
//		codecept_debug( wp_scripts()->queue );
//		codecept_debug( wp_scripts()->get_data('bello', 'group') );
//		codecept_debug( wp_scripts()->in_footer );
    }

	/**
	 * @test
	 */
//	public function it_should_not_have_asset_enqueued()
//	{
//		$this->go_to( get_permalink( $this->page_id ) );
//
//		$assets = $this->get_config_array( '02' );
////		codecept_debug($assets);
//		$sut = $this->_make( 'style', $assets );
////		codecept_debug($sut);
//		codecept_debug($sut->is_enqueued());
////		$this->assertFalse( $sut->is_enqueued() );
//
//		$sut = $this->_make( 'script', $assets );
//		$this->assertFalse( $sut->is_enqueued() );
//    }

	/**
	 * @test
	 */
//	public function it_should_have_asset_enqueue_only_in_posts()
//	{
//		$this->go_to( get_permalink( $this->post_id ) );
//
//		$assets = $this->get_config_array( '02' );
//
//		$sut = $this->_make( 'script', $assets );
//		$this->assertTrue( $sut->is_enqueued() );
//
//		$sut = $this->_make( 'style', $assets );
//		$this->assertTrue( $sut->is_enqueued() );
//    }
}