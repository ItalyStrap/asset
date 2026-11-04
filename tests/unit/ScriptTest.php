<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\Always;
use ItalyStrap\Asset\Dependencies;
use ItalyStrap\Asset\FixedVersion;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\InlineScript;
use ItalyStrap\Asset\Position;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\ScriptLoading;
use ItalyStrap\Asset\Strategy;
use ItalyStrap\Asset\Translations;
use ItalyStrap\Asset\When;
use ItalyStrap\Asset\CoreVersion;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;

final class ScriptTest extends Unit
{
    #[Test]
    public function itHasTheDefaults(): void
    {
        $script = new Script(new Handle('my-script'), 'https://example.com/my-script.js');

        $this->assertSame('my-script', $script->handle()->value());
        $this->assertSame('https://example.com/my-script.js', $script->source());
        $this->assertSame([], $script->dependencies()->toArray());
        $this->assertInstanceOf(CoreVersion::class, $script->version());
        $this->assertSame(['in_footer' => false], $script->loading()->toArray());
        $this->assertSame([], $script->inlineScripts());
        $this->assertSame([], $script->data());
        $this->assertNull($script->translations());
        $this->assertInstanceOf(Always::class, $script->condition());
    }

    #[Test]
    public function itHasAllTheParts(): void
    {
        $inline = new InlineScript('window.myData = {};', Position::Before);
        $translations = new Translations('my-domain', new SplFileInfo('/languages'));
        $condition = new When(static fn (): bool => false);

        $script = new Script(
            handle: new Handle('my-script'),
            source: 'https://example.com/my-script.js',
            dependencies: new Dependencies('wp-i18n'),
            version: new FixedVersion('abc123'),
            loading: new ScriptLoading(inFooter: true, strategy: Strategy::Defer),
            inlineScripts: [$inline],
            data: ['consent_category' => 'statistics'],
            translations: $translations,
            condition: $condition,
        );

        $this->assertSame(['wp-i18n'], $script->dependencies()->toArray());
        $this->assertSame('abc123', $script->version()->value());
        $this->assertSame(['in_footer' => true, 'strategy' => 'defer'], $script->loading()->toArray());
        $this->assertSame([$inline], $script->inlineScripts());
        $this->assertSame(['consent_category' => 'statistics'], $script->data());
        $this->assertSame($translations, $script->translations());
        $this->assertSame($condition, $script->condition());
    }

    #[Test]
    public function itCanHaveNoSourceToGroupItsDependencies(): void
    {
        $script = new Script(new Handle('my-group'), null, new Dependencies('jquery'));

        $this->assertNull($script->source());
    }

    #[Test]
    public function itRejectsAnEmptySource(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Script(new Handle('my-script'), '');
    }
}
