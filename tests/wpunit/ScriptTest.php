<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\File;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\Version\EmptyVersion;
use ItalyStrap\Finder\Finder;

// phpcs:disable
include_once 'WPUnitBaseAsset.php';
// phpcs:enable

class ScriptTest extends WPUnitBaseAsset {

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

	protected function getInstance() {

		$file = new File(
			$this->finder->firstFile( 'style', 'css'),
			new EmptyVersion(),
			$_SERVER['TEST_SITE_WP_URL'],
			$_SERVER['WP_ROOT_FOLDER']
		);

		$config = \ItalyStrap\Config\ConfigFactory::make([
			Asset::HANDLE	=> 'handle',
			Asset::URL		=> $file->url(),
			Asset::VERSION	=> $file->version(),
		]);

		$sut = new Script( $config );

		return $sut;
	}
}
