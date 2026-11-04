<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Integration;

use ItalyStrap\Asset\Dependencies;
use ItalyStrap\Asset\FixedVersion;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\Style;
use ItalyStrap\Asset\StyleLoader;
use ItalyStrap\Asset\When;
use lucatume\WPBrowser\TestCase\WPTestCase;
use PHPUnit\Framework\Attributes\Test;

final class StyleLoaderTest extends WPTestCase
{
    private const URL = 'https://example.com/my-style.css';

    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['wp_styles'] = null;
    }

    #[Test]
    public function itRegistersAndEnqueuesTheStyle(): void
    {
        (new StyleLoader())->load(new Style(
            handle: new Handle('my-style'),
            source: self::URL,
            dependencies: new Dependencies('dashicons'),
            version: new FixedVersion('abc123'),
            media: 'print',
        ));

        $registered = \wp_styles()->registered['my-style'];
        $this->assertSame(self::URL, $registered->src);
        $this->assertSame(['dashicons'], $registered->deps);
        $this->assertSame('abc123', $registered->ver);
        $this->assertSame('print', $registered->args);
        $this->assertTrue(\wp_style_is('my-style', 'enqueued'));
    }

    #[Test]
    public function itOnlyRegistersTheStyleWhenTheConditionIsFalse(): void
    {
        (new StyleLoader())->load(new Style(
            handle: new Handle('my-style'),
            source: self::URL,
            condition: new When(static fn (): bool => false),
        ));

        $this->assertTrue(\wp_style_is('my-style', 'registered'));
        $this->assertFalse(\wp_style_is('my-style', 'enqueued'));
    }

    #[Test]
    public function itAddsTheInlineStylesAndTheData(): void
    {
        (new StyleLoader())->load(new Style(
            handle: new Handle('my-style'),
            source: self::URL,
            inlineStyles: ['.a{color:red}', '.b{color:blue}'],
            data: ['rtl' => 'replace'],
        ));

        $this->assertSame(['.a{color:red}', '.b{color:blue}'], \wp_styles()->get_data('my-style', 'after'));
        $this->assertSame('replace', \wp_styles()->get_data('my-style', 'rtl'));
    }

    #[Test]
    public function itPrintsOnlyTheInlineStylesWithoutSource(): void
    {
        (new StyleLoader())->load(new Style(new Handle('my-colors'), null, inlineStyles: ['.a{color:red}']));

        \ob_start();
        \wp_styles()->do_item('my-colors');
        $html = (string) \ob_get_clean();

        $this->assertFalse(\wp_styles()->registered['my-colors']->src);
        $this->assertStringContainsString('.a{color:red}', $html);
        $this->assertStringNotContainsString('<link', $html);
    }
}
