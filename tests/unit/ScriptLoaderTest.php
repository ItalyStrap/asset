<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Dependencies;
use ItalyStrap\Asset\FixedVersion;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\InlineScript;
use ItalyStrap\Asset\Position;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\ScriptLoader;
use ItalyStrap\Asset\ScriptLoading;
use ItalyStrap\Asset\Strategy;
use ItalyStrap\Asset\Tests\Unit\Fake\ScriptRegistrySpy;
use ItalyStrap\Asset\Translations;
use ItalyStrap\Asset\When;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;

final class ScriptLoaderTest extends Unit
{
    private const URL = 'https://example.com/my-script.js';

    private ScriptRegistrySpy $registry;

    protected function _before(): void
    {
        $this->registry = new ScriptRegistrySpy();
    }

    #[Test]
    public function itRegistersAndEnqueuesTheScript(): void
    {
        $this->load(new Script(
            handle: new Handle('my-script'),
            source: self::URL,
            dependencies: new Dependencies('jquery'),
            version: new FixedVersion('abc123'),
            loading: new ScriptLoading(inFooter: true, strategy: Strategy::Defer),
        ));

        $this->assertSame([
            ['register', 'my-script', self::URL, ['jquery'], 'abc123', ['in_footer' => true, 'strategy' => 'defer']],
            ['enqueue', 'my-script'],
        ], $this->registry->calls);
    }

    #[Test]
    public function itRegistersAScriptWithoutSourceAsFalse(): void
    {
        $this->load(new Script(new Handle('my-group'), null));

        $this->assertSame(
            ['register', 'my-group', false, [], false, ['in_footer' => false]],
            $this->registry->calls[0]
        );
    }

    #[Test]
    public function itOnlyRegistersTheScriptWhenTheConditionIsFalse(): void
    {
        $this->load(new Script(new Handle('my-script'), self::URL, condition: new When(static fn (): bool => false)));

        $this->assertSame(['register'], \array_column($this->registry->calls, 0));
    }

    #[Test]
    public function itAddsTheInlineScriptsTheDataAndTheTranslationsBeforeTheEnqueue(): void
    {
        $this->load(new Script(
            handle: new Handle('my-script'),
            source: self::URL,
            inlineScripts: [new InlineScript('var a;', Position::Before), new InlineScript('init();')],
            data: ['consent_category' => 'statistics', 'conditional' => false],
            translations: new Translations('my-domain', new SplFileInfo('/plugin/languages')),
        ));

        $this->assertSame([
            ['addInline', 'my-script', 'var a;', 'before'],
            ['addInline', 'my-script', 'init();', 'after'],
            ['addData', 'my-script', 'consent_category', 'statistics'],
            ['addData', 'my-script', 'conditional', false],
            ['translate', 'my-script', 'my-domain', '/plugin/languages'],
            ['enqueue', 'my-script'],
        ], \array_slice($this->registry->calls, 1));
    }

    #[Test]
    public function itTranslatesFromTheLanguagesFolderOfTheCore(): void
    {
        $this->load(new Script(new Handle('my-script'), self::URL, translations: new Translations('my-domain')));

        $this->assertSame(['translate', 'my-script', 'my-domain', ''], $this->registry->calls[1]);
    }

    private function load(Script $script): void
    {
        (new ScriptLoader($this->registry))->load($script);
    }
}
