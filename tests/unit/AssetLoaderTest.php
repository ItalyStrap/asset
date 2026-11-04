<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use ItalyStrap\Asset\AssetLoader;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\ScriptLoader;
use ItalyStrap\Asset\Style;
use ItalyStrap\Asset\StyleLoader;
use ItalyStrap\Asset\Tests\Unit\Fake\ScriptRegistrySpy;
use ItalyStrap\Asset\Tests\Unit\Fake\StyleRegistrySpy;
use PHPUnit\Framework\Attributes\Test;

final class AssetLoaderTest extends Unit
{
    #[Test]
    public function itLoadsEachAssetWithItsLoaderInOrder(): void
    {
        $scripts = new ScriptRegistrySpy();
        $styles = new StyleRegistrySpy();

        (new AssetLoader(new ScriptLoader($scripts), new StyleLoader($styles)))->load(
            new Style(new Handle('first-style'), 'https://example.com/first.css'),
            new Script(new Handle('my-script'), 'https://example.com/my-script.js'),
            new Style(new Handle('second-style'), 'https://example.com/second.css'),
        );

        $this->assertSame(['my-script', 'my-script'], \array_column($scripts->calls, 1));
        $this->assertSame(
            ['first-style', 'first-style', 'second-style', 'second-style'],
            \array_column($styles->calls, 1)
        );
    }
}
