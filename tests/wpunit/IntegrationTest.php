<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\AssetManager;
use ItalyStrap\Asset\AssetsSubscriber;
use ItalyStrap\Config\ConfigFactory;
use PHPUnit\Framework\Assert;

class IntegrationTest extends \Codeception\TestCase\WPTestCase
{
    /**
     * @var \WpunitTester
     */
    protected $tester;
    
    public function setUp(): void
    {
        // Before...
        parent::setUp();

        // Your set up methods here.
    }

    public function tearDown(): void
    {
        // Your tear down methods here.

        // Then...
        parent::tearDown();
    }

	/**
	 * @test
	 */
    public function itShouldRunAssets()
    {
		$event_dispatcher = new \ItalyStrap\Event\EventDispatcher();
		$event_subscriber = new \ItalyStrap\Event\SubscriberRegister( $event_dispatcher );

		$assets = [];
		$assets[] = new \ItalyStrap\Asset\Style( ConfigFactory::make( [
			Asset::HANDLE	=> 'handle',
			Asset::URL		=> 'url',
		] ) );
		$assets[] = new \ItalyStrap\Asset\Script( ConfigFactory::make( [
			Asset::HANDLE	=> 'handle',
			Asset::URL		=> 'url',
			Asset::LOCALIZE	=> [
				'object_name'	=> 'pluginParams',
				'params'		=> [
					'ajaxurl'		=> \admin_url( '/admin-ajax.php' ),
					'ajaxnonce'		=> \wp_create_nonce( 'ajaxnonce' ),
				],
			],
		] ) );

		$assets_manager = new AssetManager();
		$assets_manager->withAssets(...$assets);
		$sut = new AssetsSubscriber( $assets_manager );

		$event_subscriber->addSubscriber( $sut );


		$enqueued = \wp_style_is('handle', 'enqueued');
		Assert::assertFalse( $enqueued, '' );

		\ob_start();
		\do_action('wp_head');
//		\do_action('wp_enqueue_scripts');
		\do_action('wp_footer');
		$output = \ob_get_clean();
		codecept_debug( $output );

		$enqueued = \wp_style_is('handle', 'enqueued');
		Assert::assertTrue( $enqueued, '' );

		$done = \wp_style_is('handle', 'done');
		Assert::assertTrue( $done, '' );

		Assert::assertStringContainsString("id='handle-css'", $output, '');
		Assert::assertStringContainsString("pluginParams", $output, '');
    }
}
