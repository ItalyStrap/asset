<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\Always;
use ItalyStrap\Asset\Dependencies;
use ItalyStrap\Asset\FixedVersion;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\Style;
use ItalyStrap\Asset\When;
use ItalyStrap\Asset\CoreVersion;
use PHPUnit\Framework\Attributes\Test;

final class StyleTest extends Unit
{
    #[Test]
    public function itHasTheDefaults(): void
    {
        $style = new Style(new Handle('my-style'), 'https://example.com/my-style.css');

        $this->assertSame('my-style', $style->handle()->value());
        $this->assertSame('https://example.com/my-style.css', $style->source());
        $this->assertSame([], $style->dependencies()->toArray());
        $this->assertInstanceOf(CoreVersion::class, $style->version());
        $this->assertSame('all', $style->media());
        $this->assertSame([], $style->inlineStyles());
        $this->assertSame([], $style->data());
        $this->assertInstanceOf(Always::class, $style->condition());
    }

    #[Test]
    public function itHasAllTheParts(): void
    {
        $condition = new When(static fn (): bool => true);

        $style = new Style(
            handle: new Handle('my-style'),
            source: 'https://example.com/my-style.css',
            dependencies: new Dependencies('wp-components'),
            version: new FixedVersion('abc123'),
            media: 'print',
            inlineStyles: ['.a{color:red}'],
            data: ['rtl' => 'replace'],
            condition: $condition,
        );

        $this->assertSame(['wp-components'], $style->dependencies()->toArray());
        $this->assertSame('abc123', $style->version()->value());
        $this->assertSame('print', $style->media());
        $this->assertSame(['.a{color:red}'], $style->inlineStyles());
        $this->assertSame(['rtl' => 'replace'], $style->data());
        $this->assertSame($condition, $style->condition());
    }

    #[Test]
    public function itCanHaveOnlyInlineStyles(): void
    {
        $style = new Style(new Handle('my-custom-css'), null, inlineStyles: ['.a{color:red}']);

        $this->assertNull($style->source());
    }

    #[Test]
    public function itRejectsAnEmptySource(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Style(new Handle('my-style'), '');
    }

    #[Test]
    public function itRejectsAnEmptyInlineStyle(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Style(new Handle('my-style'), null, inlineStyles: ['.a{}', '  ']);
    }

    #[Test]
    public function itRejectsAnEmptyMedia(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Style(new Handle('my-style'), 'https://example.com/my-style.css', media: '');
    }
}
