<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Integration;

use ItalyStrap\Asset\AssetLoader;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\Style;
use lucatume\WPBrowser\TestCase\WPTestCase;
use PHPUnit\Framework\Attributes\Test;

final class AssetLoaderTest extends WPTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['wp_scripts'] = null;
        $GLOBALS['wp_styles'] = null;
    }

    #[Test]
    public function itLoadsTheScriptsAndTheStyles(): void
    {
        (new AssetLoader())->load(
            new Script(new Handle('my-script'), 'https://example.com/my-script.js'),
            new Style(new Handle('my-style'), 'https://example.com/my-style.css'),
        );

        $this->assertTrue(\wp_script_is('my-script', 'enqueued'));
        $this->assertTrue(\wp_style_is('my-style', 'enqueued'));
    }

    #[Test]
    public function itLoadsNothingWithoutAssets(): void
    {
        (new AssetLoader())->load();

        $this->assertSame([], \wp_scripts()->queue);
        $this->assertSame([], \wp_styles()->queue);
    }
}
