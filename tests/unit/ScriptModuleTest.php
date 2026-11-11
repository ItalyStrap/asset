<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\Always;
use ItalyStrap\Asset\CoreVersion;
use ItalyStrap\Asset\Dependencies;
use ItalyStrap\Asset\FixedVersion;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\RegisterOnly;
use ItalyStrap\Asset\ScriptModule;
use ItalyStrap\Asset\Translations;
use PHPUnit\Framework\Attributes\Test;

final class ScriptModuleTest extends Unit
{
    #[Test]
    public function itHasTheDefaultsOfAScript(): void
    {
        $module = new ScriptModule(new Handle('my-module'), 'https://example.com/view.js');

        $this->assertSame('my-module', $module->id()->value());
        $this->assertSame('https://example.com/view.js', $module->source());
        $this->assertSame([], $module->dependencies()->toArray());
        $this->assertInstanceOf(CoreVersion::class, $module->version());
        $this->assertNull($module->translations());
        $this->assertInstanceOf(Always::class, $module->condition());
    }

    #[Test]
    public function itKeepsItsParts(): void
    {
        $translations = new Translations('my-plugin');
        $condition = new RegisterOnly();

        $module = new ScriptModule(
            id: new Handle('my-module'),
            source: 'https://example.com/view.js',
            dependencies: new Dependencies('@wordpress/interactivity'),
            version: new FixedVersion('1.0.0'),
            translations: $translations,
            condition: $condition,
        );

        $this->assertSame(['@wordpress/interactivity'], $module->dependencies()->toArray());
        $this->assertSame('1.0.0', $module->version()->value());
        $this->assertSame($translations, $module->translations());
        $this->assertSame($condition, $module->condition());
    }

    #[Test]
    public function itNeedsASource(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The source of the script module "my-module" must not be empty');

        new ScriptModule(new Handle('my-module'), ' ');
    }
}
