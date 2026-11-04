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

use function tad\FunctionMockerLe\define;
use function sprintf;
use function tad\FunctionMockerLe\undefineAll;

class DebugAssetTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * @var bool
     */
    protected $is_wp_error_return_value;
    protected $wp_remote_get_return_value;
    protected $fake_asset_url = '/wp-content/themes/theme-name/fake.asset.url';
    protected $is_child_theme = false;
    protected $get_stylesheet = 'theme-name';

    protected function _before()
    {

        define('wp_remote_get', fn(string $url) => $this->wp_remote_get_return_value);
        define('is_wp_error', fn($error): bool => $this->is_wp_error_return_value);

        $wp_is = (fn(string $handle, $list = ''): bool => 'registered' === $list);

        define('wp_style_is', $wp_is);
        define('wp_script_is', $wp_is);
        define('wp_scripts', function (): \stdClass {
            $std_class = new stdClass();
            $std_class->base_url = '';

            $std_class->src = $this->fake_asset_url;

            $std_class->registered = [
                'handle'    => $std_class,
            ];
            return $std_class;
        });

        define('is_child_theme', fn() => $this->is_child_theme);
        define('get_stylesheet', fn() => $this->get_stylesheet);
    }

    protected function _after()
    {
        undefineAll([
            'wp_style_is',
            'wp_script_is',
            'wp_scripts',
            'is_child_theme',
            'get_stylesheet',
        ]);
    }

    public static function assetNotAvailableProvider(): array
    {
        return [
            'css'   => [
                '/wp-content/themes/theme-name/fake.asset.url',
                DebugStyle::class,
            ],
            'js'    => [
                '/wp-content/themes/theme-name/fake.asset.url',
                DebugScript::class,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider assetNotAvailableProvider
     */
    public function itShouldThrownInvalidArgumentExceptionIfGetRemoteAssetIsNotAvailableFor(
        string $url,
        string $type,
    ): void {
        $this->is_wp_error_return_value = true;
        $this->wp_remote_get_return_value = new class {
            public function get_error_message(): string // phpcs:ignore PSR1.Methods.CamelCapsMethodName -- WP_Error API
            {
                return 'Some error message from Remote Request';
            }
        };

        $config = ( new ConfigFactory() )->make([
            Asset::HANDLE       => 'handle',
            Asset::URL          => $url,
            Asset::SHOULD_LOAD  => false,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                DebugAsset::M_URL_NOT_ACCESSIBLE,
                $url,
                'Some error message from Remote Request'
            )
        );
        new $type($config);
    }

    /**
     * @test
     * @dataProvider assetNotAvailableProvider
     */
    public function itShouldThrownRunTimeExceptionIfAssetIsLoadedFromParentThemeNotChildThemeFor(
        string $url,
        string $type,
    ): void {
        $this->is_wp_error_return_value = false;
        $this->is_child_theme = true;
        $this->get_stylesheet = 'child';

        $config = ( new ConfigFactory() )->make([
            Asset::HANDLE   => 'handle',
            Asset::URL      => $url,
            Asset::SHOULD_LOAD  => false,
        ]);

        new $type($config);

        $this->assertStringContainsString(
            \sprintf(
                'Asset "%s" is loaded from parent, see: "%s"',
                'handle',
                $url
            ),
            $this->output(),
            ''
        );
    }

    public static function assetAvailableProvider(): array
    {
        return [
            'css'   => [
                'http://wordpress'
                . '/tests/_data/fixtures/parent/css/style.css',
                DebugStyle::class,
            ],
            'js'    => [
                'http://wordpress'
                . '/tests/_data/fixtures/parent/js/script.js',
                DebugScript::class,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider assetAvailableProvider
     */
    public function itShouldGetRemoteAssetFor(string $url, string $type): void
    {

        $this->is_wp_error_return_value = false;
        $config = ( new ConfigFactory() )->make([
            Asset::HANDLE   => 'handle',
            Asset::URL      => $url,
            Asset::SHOULD_LOAD  => false,
        ]);

        new $type($config);
    }
}
