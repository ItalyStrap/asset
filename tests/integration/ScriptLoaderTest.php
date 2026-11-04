<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Integration;

use ItalyStrap\Asset\Dependencies;
use ItalyStrap\Asset\FixedVersion;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\InlineScript;
use ItalyStrap\Asset\NoVersion;
use ItalyStrap\Asset\Position;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\ScriptLoader;
use ItalyStrap\Asset\ScriptLoading;
use ItalyStrap\Asset\Strategy;
use ItalyStrap\Asset\Translations;
use ItalyStrap\Asset\When;
use lucatume\WPBrowser\TestCase\WPTestCase;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;

final class ScriptLoaderTest extends WPTestCase
{
    private const URL = 'https://example.com/my-script.js';

    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['wp_scripts'] = null;
    }

    #[Test]
    public function itRegistersAndEnqueuesTheScript(): void
    {
        (new ScriptLoader())->load(new Script(
            handle: new Handle('my-script'),
            source: self::URL,
            dependencies: new Dependencies('jquery'),
            version: new FixedVersion('abc123'),
        ));

        $registered = \wp_scripts()->registered['my-script'];
        $this->assertSame(self::URL, $registered->src);
        $this->assertSame(['jquery'], $registered->deps);
        $this->assertSame('abc123', $registered->ver);
        $this->assertTrue(\wp_script_is('my-script', 'enqueued'));
    }

    #[Test]
    public function itOnlyRegistersTheScriptWhenTheConditionIsFalse(): void
    {
        (new ScriptLoader())->load(new Script(
            handle: new Handle('my-script'),
            source: self::URL,
            condition: new When(static fn (): bool => false),
        ));

        $this->assertTrue(\wp_script_is('my-script', 'registered'));
        $this->assertFalse(\wp_script_is('my-script', 'enqueued'));
    }

    #[Test]
    public function itHasTheVersionOfTheCoreOrNoVersion(): void
    {
        $loader = new ScriptLoader();
        $loader->load(new Script(new Handle('core-version'), self::URL));
        $loader->load(new Script(new Handle('no-version'), self::URL, version: new NoVersion()));

        $this->assertFalse(\wp_scripts()->registered['core-version']->ver);
        $this->assertNull(\wp_scripts()->registered['no-version']->ver);
    }

    #[Test]
    public function itLoadsInTheFooterWithTheStrategy(): void
    {
        (new ScriptLoader())->load(new Script(
            handle: new Handle('my-script'),
            source: self::URL,
            loading: new ScriptLoading(inFooter: true, strategy: Strategy::Defer),
        ));

        $this->assertSame(1, \wp_scripts()->get_data('my-script', 'group'));
        $this->assertSame('defer', \wp_scripts()->get_data('my-script', 'strategy'));
    }

    #[Test]
    public function itAddsTheInlineScriptsAndTheData(): void
    {
        (new ScriptLoader())->load(new Script(
            handle: new Handle('my-script'),
            source: self::URL,
            inlineScripts: [
                new InlineScript('var before;', Position::Before),
                new InlineScript('after();'),
                new InlineScript('afterAgain();'),
            ],
            data: ['consent_category' => 'statistics'],
        ));

        $this->assertSame(['var before;'], $this->inline('before'));
        $this->assertSame(['after();', 'afterAgain();'], $this->inline('after'));
        $this->assertSame('statistics', \wp_scripts()->get_data('my-script', 'consent_category'));
    }

    #[Test]
    public function itSetsTheTranslations(): void
    {
        $loader = new ScriptLoader();
        $loader->load(new Script(
            new Handle('with-folder'),
            self::URL,
            translations: new Translations('my-domain', new SplFileInfo('/plugin/languages')),
        ));
        $loader->load(new Script(new Handle('core-folder'), self::URL, translations: new Translations('my-domain')));

        $this->assertSame('my-domain', \wp_scripts()->registered['with-folder']->textdomain);
        $this->assertSame('/plugin/languages', \wp_scripts()->registered['with-folder']->translations_path);
        $this->assertSame('my-domain', \wp_scripts()->registered['core-folder']->textdomain);
        $this->assertSame('', \wp_scripts()->registered['core-folder']->translations_path);
    }

    #[Test]
    public function aScriptWithoutSourceGroupsItsDependencies(): void
    {
        (new ScriptLoader())->load(new Script(new Handle('my-group'), null, new Dependencies('jquery')));

        $this->assertFalse(\wp_scripts()->registered['my-group']->src);
        $this->assertTrue(\wp_script_is('my-group', 'enqueued'));
    }

    /**
     * The core keeps a false before the first inline script.
     *
     * @return list<string>
     */
    private function inline(string $position): array
    {
        $inline = \wp_scripts()->get_data('my-script', $position);

        return \array_values(\array_filter((array) $inline, \is_string(...)));
    }
}
