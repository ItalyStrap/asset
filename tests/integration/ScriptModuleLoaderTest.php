<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Integration;

use ItalyStrap\Asset\Dependencies;
use ItalyStrap\Asset\FixedVersion;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\RegisterOnly;
use ItalyStrap\Asset\ScriptModule;
use ItalyStrap\Asset\ScriptModuleLoader;
use ItalyStrap\Asset\Translations;
use lucatume\WPBrowser\TestCase\WPTestCase;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;

final class ScriptModuleLoaderTest extends WPTestCase
{
    private const ID = '@my-plugin/view';
    private const URL = 'https://example.com/view.js';

    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['wp_script_modules'] = null;
    }

    protected function tearDown(): void
    {
        $GLOBALS['wp_script_modules'] = null;
        parent::tearDown();
    }

    #[Test]
    public function itRegistersAndEnqueuesTheModule(): void
    {
        (new ScriptModuleLoader())->load(new ScriptModule(
            id: new Handle(self::ID),
            source: self::URL,
            dependencies: new Dependencies('@my-plugin/store'),
            version: new FixedVersion('abc123'),
        ));

        $registered = \wp_script_modules()->get_registered(self::ID);
        $this->assertIsArray($registered);
        $this->assertSame(self::URL, $registered['src']);
        $this->assertSame('abc123', $registered['version']);
        $this->assertSame([['id' => '@my-plugin/store', 'import' => 'static']], $registered['dependencies']);
        $this->assertSame([self::ID], \wp_script_modules()->get_queue());
    }

    #[Test]
    public function itOnlyRegistersTheModuleWhenTheConditionIsFalse(): void
    {
        (new ScriptModuleLoader())->load(new ScriptModule(
            new Handle(self::ID),
            self::URL,
            condition: new RegisterOnly(),
        ));

        $this->assertNotNull(\wp_script_modules()->get_registered(self::ID));
        $this->assertSame([], \wp_script_modules()->get_queue());
    }

    #[Test]
    public function itTranslatesTheModule(): void
    {
        (new ScriptModuleLoader())->load(new ScriptModule(
            id: new Handle(self::ID),
            source: self::URL,
            translations: new Translations('my-domain', new SplFileInfo('/plugin/languages')),
        ));

        $registered = \wp_script_modules()->get_registered(self::ID);
        $this->assertIsArray($registered);
        $this->assertSame('my-domain', $registered['textdomain'] ?? null);
        $this->assertSame('/plugin/languages', $registered['translations_path'] ?? null);
    }

    #[Test]
    public function itDequeuesAndDeregistersTheModule(): void
    {
        $loader = new ScriptModuleLoader();
        $loader->load(new ScriptModule(new Handle(self::ID), self::URL));

        $loader->dequeue(new Handle(self::ID));
        $this->assertSame([], \wp_script_modules()->get_queue());
        $this->assertNotNull(\wp_script_modules()->get_registered(self::ID));

        $loader->deregister(new Handle(self::ID));
        $this->assertNull(\wp_script_modules()->get_registered(self::ID));
    }
}
