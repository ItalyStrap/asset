<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Asset\ConfigBuilder;
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

		$config_builder = new ConfigBuilder($finder);
		$config_builder->addConfig( require codecept_data_dir('/fixtures/_config/styles.php') );
		$config_builder->addConfig( require codecept_data_dir('/fixtures/_config/scripts.php') );

		$configs = $config_builder->parsedConfig();
		/** @var ConfigInterface $config */
		foreach ( $configs as $config ) {
			$asset = \Inpsyde\Assets\AssetFactory::create($config->toArray());
		}
	}
}
