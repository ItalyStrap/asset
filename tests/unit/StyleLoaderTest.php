<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Dependencies;
use ItalyStrap\Asset\FixedVersion;
use ItalyStrap\Asset\AssetStatus;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\Style;
use ItalyStrap\Asset\StyleLoader;
use ItalyStrap\Asset\Tests\Unit\Fake\StyleRegistrySpy;
use ItalyStrap\Asset\When;
use PHPUnit\Framework\Attributes\Test;

final class StyleLoaderTest extends Unit
{
    private const URL = 'https://example.com/my-style.css';

    private StyleRegistrySpy $registry;

    protected function _before(): void
    {
        $this->registry = new StyleRegistrySpy();
    }

    #[Test]
    public function itRegistersTheInlineStylesTheDataAndEnqueuesTheStyle(): void
    {
        $this->load(new Style(
            handle: new Handle('my-style'),
            source: self::URL,
            dependencies: new Dependencies('dashicons'),
            version: new FixedVersion('abc123'),
            media: 'print',
            inlineStyles: ['.a{}', '.b{}'],
            data: ['rtl' => 'replace', 'alt' => true],
        ));

        $this->assertSame([
            ['register', 'my-style', self::URL, ['dashicons'], 'abc123', 'print'],
            ['addInline', 'my-style', '.a{}'],
            ['addInline', 'my-style', '.b{}'],
            ['addData', 'my-style', 'rtl', 'replace'],
            ['addData', 'my-style', 'alt', true],
            ['enqueue', 'my-style'],
        ], $this->registry->calls);
    }

    #[Test]
    public function itRegistersAStyleWithoutSourceAsFalse(): void
    {
        $this->load(new Style(new Handle('my-colors'), null, inlineStyles: ['.a{}']));

        $this->assertSame(['register', 'my-colors', false, [], false, 'all'], $this->registry->calls[0]);
    }

    #[Test]
    public function itOnlyRegistersTheStyleWhenTheConditionIsFalse(): void
    {
        $this->load(new Style(new Handle('my-style'), self::URL, condition: new When(static fn (): bool => false)));

        $this->assertSame(['register'], \array_column($this->registry->calls, 0));
    }

    #[Test]
    public function itReadsTheStatusOfAHandle(): void
    {
        $this->registry->statuses = ['my-style' => [AssetStatus::Registered, AssetStatus::Enqueued]];
        $loader = new StyleLoader($this->registry);

        $this->assertTrue($loader->is(new Handle('my-style'), AssetStatus::Enqueued));
        $this->assertFalse($loader->is(new Handle('my-style'), AssetStatus::Done));
    }

    #[Test]
    public function itPrintsARegisteredStyle(): void
    {
        $this->registry->statuses = ['my-style' => [AssetStatus::Registered]];

        (new StyleLoader($this->registry))->print(new Handle('other-style'), new Handle('my-style'));

        $this->assertSame([
            ['is', 'other-style', 'registered'],
            ['is', 'my-style', 'registered'],
            ['print', 'my-style'],
        ], $this->registry->calls);
    }

    #[Test]
    public function itDequeuesAndDeregistersAStyle(): void
    {
        $loader = new StyleLoader($this->registry);

        $loader->dequeue(new Handle('my-style'));
        $loader->deregister(new Handle('my-style'));

        $this->assertSame([['dequeue', 'my-style'], ['deregister', 'my-style']], $this->registry->calls);
    }

    private function load(Style $style): void
    {
        (new StyleLoader($this->registry))->load($style);
    }
}
