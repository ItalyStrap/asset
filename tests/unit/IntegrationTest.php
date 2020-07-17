<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use Codeception\Test\Unit;
use ItalyStrap\Asset\File;
use ItalyStrap\Asset\Style;
use ItalyStrap\Asset\Version\EmptyVersion;
use ItalyStrap\Config\ConfigFactory;
use UnitTester;

class IntegrationTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->paths = [
			'childPath'		=> \codecept_data_dir( 'fixtures/child/css' ),
			'parentPath'	=> \codecept_data_dir( 'fixtures/parent/css' ),
		];

//		$this->finder = new AssetFinder();
		$this->finder = $this->prophesize(\ItalyStrap\Finder\FinderInterface::class);
		$this->finder->in( $this->paths )->willReturn($this->finder);
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	public function configProvider() {
		return [
			'01'	=> [
				[
					'handle'	=> 'handle_style_01',
					'file'		=> 'style.css',
//					'file'		=> 'style.min.css',
				]
			],
			'02'	=> [
				[
					'handle'	=> 'handle_style_01',
//					'file'		=> 'style.css',
					'file'		=> 'custom.min.css',
				]
			],
		];
	}

	/**
	 * @dataProvider configProvider()
	 */
	public function integration( array $config ) {
		$files_to_search = array_reverse( explode( '.', $config['file'] ) );
//		codecept_debug( $files_to_search  );

		$extension = array_shift( $files_to_search );
//		codecept_debug('AFTER SHIFT');
//		codecept_debug( $files_to_search );
//		codecept_debug( $extension );

//    	codecept_debug( array_reverse( $config ) );

		$file = new File(
//    		new \SplFileInfo( $this->finder->find( $files_to_search, $extension) ),
			new \SplFileInfo( $this->finder->reveal()->find( array_reverse($files_to_search), $extension) ),
			new EmptyVersion(),
			$_SERVER['TEST_SITE_WP_URL'],
			$_SERVER['WP_ROOT_FOLDER']
		);

		$style = new Style( $file, ConfigFactory::make( $config ) );
		$style->enqueue();
	}
}
