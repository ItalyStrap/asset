<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use ItalyStrap\Asset\EventName;
use ItalyStrap\Asset\Hook;
use PHPUnit\Framework\Attributes\Test;

final class EventNameTest extends Unit
{
    #[Test]
    public function itHasTheNamesOfTheEnqueueEvents(): void
    {
        $this->assertSame('wp_enqueue_scripts', EventName::FRONT_END);
        $this->assertSame('admin_enqueue_scripts', EventName::ADMIN);
        $this->assertSame('enqueue_block_editor_assets', EventName::EDITOR);
        $this->assertSame('enqueue_block_assets', EventName::BLOCK);
        $this->assertSame('login_enqueue_scripts', EventName::LOGIN);
    }

    #[Test]
    public function theDeprecatedHookIsAnAliasOfTheEventName(): void
    {
        $this->assertSame(EventName::class, (new \ReflectionClass(Hook::class))->getName());
        $this->assertSame(EventName::FRONT_END, Hook::FRONT_END);
    }
}
