<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\AssetInterface;
use ItalyStrap\Asset\AssetManager;
use ItalyStrap\Asset\AssetsSubscriber;
use ItalyStrap\Asset\ConfigBuilder;
use ItalyStrap\Asset\Debug\DebugScript;
use ItalyStrap\Asset\Debug\DebugStyle;
use ItalyStrap\Asset\Loader\GeneratorLoader;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\Style;
use ItalyStrap\Asset\Version\EmptyVersion;
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Debug\Debug;
use ItalyStrap\Finder\Finder;
use ItalyStrap\Finder\FinderFactory;
use PHPUnit\Framework\Assert;

class IntegrationTest extends \Codeception\TestCase\WPTestCase {

	/**
	 * @var \WpunitTester
	 */
	protected $tester;
	
	public function setUp(): void {
		// Before...
		parent::setUp();

		// Your set up methods here.

		global $wp_scripts;
		$wp_scripts = null;
		global $wp_styles;
		$wp_styles = null;
	}

	public function tearDown(): void {
		// Your tear down methods here.

		// Then...
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function itShouldRunAssets() {
		$event_dispatcher = new \ItalyStrap\Event\EventDispatcher();
		$event_subscriber = new \ItalyStrap\Event\SubscriberRegister( $event_dispatcher );

		$config = [
			[
				Asset::HANDLE	=> 'handle',
				Asset::URL		=> 'file/url.css',
			],
			[
				Asset::HANDLE	=> 'handle',
				Asset::URL		=> 'file/url.js',
				Asset::LOCALIZE	=> [
					'object_name'	=> 'pluginParams',
					'params'		=> [
						'ajaxurl'		=> \admin_url( '/admin-ajax.php' ),
						'ajaxnonce'		=> \wp_create_nonce( 'ajaxnonce' ),
					],
				],
			]
		];

		$finder = (new FinderFactory())->make();
		$finder->in(
			[
				codecept_data_dir('/fixtures/child/js/'),
				codecept_data_dir('/fixtures/parent/js/')
			]
		);

		$config_builder = new ConfigBuilder(
			new EmptyVersion(),
			$_SERVER['TEST_SITE_WP_URL'],
			$_SERVER['WP_ROOT_FOLDER']
		);

//		$config_builder->withType('css', Style::class );
//		$config_builder->withType('js', Script::class );

		$config_builder->withType('css', DebugStyle::class );
		$config_builder->withType('js', DebugScript::class );
		$config_builder->addConfig( $config );

		$assets = ( new GeneratorLoader() )->load( $config_builder->parsedConfig() );

		$assets_manager = new AssetManager();
		$assets_manager->withAssets(...$assets);
		$sut = new AssetsSubscriber( $assets_manager );

		$event_subscriber->addSubscriber( $sut );


		$enqueued = \wp_style_is('handle', 'enqueued');
		Assert::assertFalse( $enqueued, '' );

		// Start content
		\ob_start();
		\do_action('wp_head');
		\do_action('wp_footer');
		$output = \ob_get_clean();
		// End content

		$enqueued = \wp_style_is('handle', 'enqueued');
		Assert::assertTrue( $enqueued, '' );

		$done = \wp_style_is('handle', 'done');
		Assert::assertTrue( $done, '' );

		Assert::assertStringContainsString("id='handle-css'", $output, '');
		Assert::assertStringContainsString("file/url.js", $output, '');
		Assert::assertStringContainsString("pluginParams", $output, '');

		/** @var AssetInterface $asset */
		foreach ( $assets as $asset ) {
			Assert::assertTrue( $asset->isRegistered(), \sprintf(
				'The %s is not Registered',
				$asset->handle()
			) );
			Assert::assertTrue( $asset->isEnqueued(), \sprintf(
				'The %s is not Enqueued',
				$asset->handle()
			) );
		}
	}
}
