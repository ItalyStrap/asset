<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\Debug\DebugAsset;
use ItalyStrap\Asset\Debug\DebugScript;
use ItalyStrap\Asset\Debug\DebugStyle;
use ItalyStrap\Config\ConfigFactory;
use UnitTester;

class DebugAssetTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var bool
	 */
	protected $is_wp_error_return_value;
	
	protected function _before() {
		\tad\FunctionMockerLe\define('wp_remote_get', function ( string $url ): array {
			return [];
		});

		\tad\FunctionMockerLe\define('is_wp_error', function ( array $error ): bool {
			return $this->is_wp_error_return_value;
		});
	}

	protected function _after() {
	}

	public function assetNotAvailableProvider() {
		return [
			'css'	=> [
				'fake.asset.url',
				DebugStyle::class,
			],
			'js'	=> [
				'fake.asset.url',
				DebugScript::class,
			],
		];
	}

	/**
	 * @test
	 * @dataProvider assetNotAvailableProvider()
	 */
	public function itShouldThrownInvalidArgumentExceptionIfGetRemoteAssetIsNotAvailableFor( $url, $type ) {
		$this->is_wp_error_return_value = true;

		$config = ConfigFactory::make([
			Asset::HANDLE	=> 'handle',
			Asset::URL		=> $url,
		]);

		$this->expectException( InvalidArgumentException::class);
		$this->expectExceptionMessage(
			\sprintf(
				DebugAsset::M_URL_NOT_ACCESSIBLE,
				$url
			)
		);
		$style = new $type( $config );
	}

	public function assetAvailableProvider() {
		return [
			'css'	=> [
				$_SERVER['TEST_SITE_WP_URL']
				. '/wp-content/plugins/asset/tests/_data/fixtures/parent/css/style.css',
				DebugStyle::class,
			],
			'js'	=> [
				$_SERVER['TEST_SITE_WP_URL']
				. '/wp-content/plugins/asset/tests/_data/fixtures/parent/js/script.js',
				DebugScript::class,
			],
		];
	}

	/**
	 * @test
	 * @dataProvider assetAvailableProvider()
	 */
	public function itShouldGetRemoteAssetFor( $url, $type ) {

		$this->is_wp_error_return_value = false;
		$config = ConfigFactory::make([
			Asset::HANDLE	=> 'handle',
			Asset::URL		=> $url,
		]);

		$style = new $type($config);
	}
}
