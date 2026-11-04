<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use lucatume\WPBrowser\TestCase\WPTestCase;
use Exception;
use InvalidArgumentException;
use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\Debug\DebugScript;
use ItalyStrap\Asset\Debug\DebugStyle;
use ItalyStrap\Config\ConfigFactory;
use PHPUnit\Framework\Assert;
use IntegrationTester;

class DebugAssetTest extends WPTestCase
{
    /**
     * @var IntegrationTester
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

    public static function assetNotAvailableProvider(): array
    {
        return [
            'css'   => [
                'fake.asset.url',
                DebugStyle::class,
            ],
            'js'    => [
                'fake.asset.url',
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

        $config = ( new ConfigFactory() )->make([
            Asset::HANDLE   => 'handle',
            Asset::URL      => $url,
        ]);

        try {
            $style = new $type($config);
        } catch (Exception $e) {
            Assert::assertStringContainsString(
                InvalidArgumentException::class,
                $e::class,
                ''
            );
            Assert::assertStringContainsString(
                'The url "fake.asset.url" is not accessible',
                $e->getMessage(),
                ''
            );
        }
    }

    public static function assetAvailableProvider(): array
    {
        return [
            'css'   => [
                $_SERVER['WP_URL']
                . '/wp-content/plugins/asset/tests/_data/fixtures/parent/css/style.css',
                DebugStyle::class,
            ],
            'js'    => [
                $_SERVER['WP_URL']
                . '/wp-content/plugins/asset/tests/_data/fixtures/parent/js/script.js',
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

        $config = ( new ConfigFactory() )->make([
            Asset::HANDLE   => 'handle',
            Asset::URL      => $url,
        ]);

        new $type($config);
    }
}
