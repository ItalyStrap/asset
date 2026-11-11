<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Dependencies;
use ItalyStrap\Asset\FixedVersion;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\ScriptModule;
use ItalyStrap\Asset\ScriptModuleLoader;
use ItalyStrap\Asset\Tests\Unit\Fake\ScriptModuleRegistrySpy;
use ItalyStrap\Asset\Translations;
use ItalyStrap\Asset\When;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;

final class ScriptModuleLoaderTest extends Unit
{
    private const URL = 'https://example.com/view.js';

    private ScriptModuleRegistrySpy $registry;

    protected function _before(): void
    {
        $this->registry = new ScriptModuleRegistrySpy();
    }

    #[Test]
    public function itRegistersTranslatesAndEnqueuesTheModule(): void
    {
        $this->loader()->load(new ScriptModule(
            id: new Handle('my-module'),
            source: self::URL,
            dependencies: new Dependencies('@wordpress/interactivity'),
            version: new FixedVersion('abc123'),
            translations: new Translations('my-plugin', new SplFileInfo('/languages')),
        ));

        $this->assertSame([
            ['register', 'my-module', self::URL, ['@wordpress/interactivity'], 'abc123'],
            ['translate', 'my-module', 'my-plugin', '/languages'],
            ['enqueue', 'my-module'],
        ], $this->registry->calls);
    }

    #[Test]
    public function itOnlyRegistersTheModuleWhenTheConditionIsFalse(): void
    {
        $this->loader()->load(new ScriptModule(
            id: new Handle('my-module'),
            source: self::URL,
            condition: new When(static fn (): bool => false),
        ));

        $this->assertSame([['register', 'my-module', self::URL, [], false]], $this->registry->calls);
    }

    #[Test]
    public function itDequeuesAndDeregistersAModule(): void
    {
        $this->loader()->dequeue(new Handle('my-module'));
        $this->loader()->deregister(new Handle('my-module'));

        $this->assertSame([['dequeue', 'my-module'], ['deregister', 'my-module']], $this->registry->calls);
    }

    private function loader(): ScriptModuleLoader
    {
        return new ScriptModuleLoader($this->registry);
    }
}
