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
use stdClass;
use UnitTester;
use function sprintf;
use function tad\FunctionMockerLe\undefineAll;

class DebugAssetTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var bool
	 */
	protected $is_wp_error_return_value;
	protected $fake_asset_url = '/wp-content/themes/theme-name/fake.asset.url';
	protected $is_child_theme = false;
	protected $get_stylesheet = 'theme-name';

	protected function _before() {

		\tad\FunctionMockerLe\define('wp_remote_get', function ( string $url ): array {
			return [];
		});
		\tad\FunctionMockerLe\define('is_wp_error', function ( array $error ): bool {
			return $this->is_wp_error_return_value;
		});

		$wp_is = function ( string $handle, $list = '' ): bool {
			if ( 'registered' === $list ) {
				return true;
			}

			return false;
		};

		\tad\FunctionMockerLe\define('wp_style_is', $wp_is );
		\tad\FunctionMockerLe\define('wp_script_is', $wp_is );
		\tad\FunctionMockerLe\define('wp_scripts', function () {
			$std_class = new stdClass();
			$std_class->base_url = '';

			$std_class->src = $this->fake_asset_url;

			$std_class->registered = [
				'handle'	=> $std_class,
			];
			return $std_class;
		} );

		\tad\FunctionMockerLe\define('is_child_theme', function () {
			return $this->is_child_theme;
		} );
		\tad\FunctionMockerLe\define('get_stylesheet', function () {
			return $this->get_stylesheet;
		} );
	}

	protected function _after() {
		undefineAll([
			'wp_style_is',
			'wp_script_is',
			'wp_scripts',
			'is_child_theme',
			'get_stylesheet',
		]);
	}

	public function assetNotAvailableProvider() {
		return [
			'css'	=> [
				$this->fake_asset_url,
				DebugStyle::class,
			],
			'js'	=> [
				$this->fake_asset_url,
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
			Asset::SHOULD_LOAD	=> false,
		]);

		$this->expectException( InvalidArgumentException::class);
		$this->expectExceptionMessage(
			sprintf(
				DebugAsset::M_URL_NOT_ACCESSIBLE,
				$url
			)
		);
		$style = new $type( $config );
	}

	/**
	 * @test
	 * @dataProvider assetNotAvailableProvider()
	 */
	public function itShouldThrownRunTimeExceptionIfAssetIsLoadedFromParentThemeNotChildThemeFor( $url, $type ) {
		$this->is_wp_error_return_value = false;
		$this->is_child_theme = true;
		$this->get_stylesheet = 'child';

		$config = ConfigFactory::make([
			Asset::HANDLE	=> 'handle',
			Asset::URL		=> $url,
			Asset::SHOULD_LOAD	=> false,
		]);

		$style = new $type( $config );

		$this->assertStringContainsString(
			\sprintf(
				'Asset "%s" is loaded from parent, see: "%s"',
				'handle',
				$url
			),
			$this->getActualOutput(),
			''
		);

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
			Asset::SHOULD_LOAD	=> false,
		]);

		$style = new $type($config);
	}
}
