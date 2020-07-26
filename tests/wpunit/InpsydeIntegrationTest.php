<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Inpsyde\Assets\AssetManager;
use Inpsyde\Assets\Script;
use Inpsyde\Assets\Style;
use ItalyStrap\Asset\Adapters\InpsydeGeneratorLoader;
use ItalyStrap\Asset\ConfigBuilder;
use ItalyStrap\Asset\Version\EmptyVersion;
use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\Finder\Finder;
use ItalyStrap\Finder\FinderFactory;

class InpsydeIntegrationTest extends \Codeception\TestCase\WPTestCase {

	/**
	 * @var \WpunitTester
	 */
	protected $tester;
	
	public function setUp(): void {
		// Before...
		parent::setUp();

		// Your set up methods here.
	}

	public function tearDown(): void {
		// Your tear down methods here.

		// Then...
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function integrationWithOldAssetConfig() {

		$finder = (new FinderFactory())->make();

		$config_builder = new ConfigBuilder($finder,
			new EmptyVersion(),
			$_SERVER['TEST_SITE_WP_URL'],
			$_SERVER['WP_ROOT_FOLDER']);

		$config_builder->withType('css', Style::class);
		$config_builder->withType('js', Script::class);

		$config_builder->addConfig( require codecept_data_dir('/fixtures/_config/styles.php') );
		$config_builder->addConfig( require codecept_data_dir('/fixtures/_config/scripts.php') );

		$loader = new InpsydeGeneratorLoader();
		$assets = $loader->load( $config_builder->parsedConfig() );

		$assets_mamager = new AssetManager();

		foreach ( $assets as $asset ) {
			$assets_mamager->register( $asset );
		}

		$assets_mamager->setup();

	}
}
