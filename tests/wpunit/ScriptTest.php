<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\File;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\Version\EmptyVersion;

if ( ! \class_exists( \ItalyStrap\Asset\Test\BaseAsset::class ) ) {
	require 'BaseAsset.php';
}

class ScriptTest extends \ItalyStrap\Asset\Test\BaseAsset {

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
		$config = \ItalyStrap\Config\ConfigFactory::make([
			'handle'	=> 'handle',
		]);
		$file = new File(
			new \SplFileInfo($this->finder->find( 'style', 'css') ),
			new EmptyVersion(),
			$_SERVER['TEST_SITE_WP_URL'],
			$_SERVER['WP_ROOT_FOLDER']
		);

		$sut = new Script( $file, $config );

		return $sut;
	}
}
