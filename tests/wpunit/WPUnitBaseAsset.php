<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Asset\AssetInterface;
use ItalyStrap\Finder\Finder;
use ItalyStrap\Finder\FinderFactory;
use function codecept_data_dir;
use function wp_enqueue_script;

abstract class WPUnitBaseAsset extends WPTestCase {

	/**
	 * @var Finder
	 */
	protected $finder;

	public function setUp(): void {
		// before
		parent::setUp();

		// your set up methods here

		global $wp_scripts;
		$wp_scripts = null;
		global $wp_styles;
		$wp_styles = null;

		$this->paths = [
			'childPath'		=> codecept_data_dir( 'fixtures/child/css' ),
			'parentPath'	=> codecept_data_dir( 'fixtures/parent/css' ),
		];

		$this->finder = ( new FinderFactory() )->make();
		$this->finder->in( $this->paths );
	}

	public function tearDown(): void {
		// your tear down methods here

		// then
		parent::tearDown();
	}

	/**
	 * @return AssetInterface
	 */
	abstract protected function getInstance();

	/**
	 * @test
	 */
	public function itShouldHaveAssetEnqueued() {
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
	public function itfgnsfgnfsx() {
		wp_enqueue_script(
			'ciao',
			'url',
			[],
			'42',
			true
		);
		wp_enqueue_script(
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
