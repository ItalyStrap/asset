<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Integration;

use ItalyStrap\Asset\AssetLoader;
use ItalyStrap\Asset\AssetsSubscriber;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\Hook;
use ItalyStrap\Asset\Script;
use ItalyStrap\Event\GlobalOrderedListenerProvider;
use ItalyStrap\Event\SubscriberRegister;
use lucatume\WPBrowser\TestCase\WPTestCase;
use PHPUnit\Framework\Attributes\Test;

final class AssetsSubscriberTest extends WPTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['wp_scripts'] = null;
    }

    #[Test]
    public function itLoadsTheAssetsOnTheHook(): void
    {
        $subscriber = new AssetsSubscriber(
            new AssetLoader(),
            [new Script(new Handle('my-script'), 'https://example.com/my-script.js')],
            'my_plugin_assets',
        );
        (new SubscriberRegister(new GlobalOrderedListenerProvider()))->addSubscriber($subscriber);

        \do_action(Hook::FRONT_END);
        $this->assertFalse(\wp_script_is('my-script', 'registered'));

        // Like the page of admin_enqueue_scripts, the arguments of the hook are not used
        \do_action('my_plugin_assets', 'settings_page_my-plugin');
        $this->assertTrue(\wp_script_is('my-script', 'enqueued'));
    }
}
