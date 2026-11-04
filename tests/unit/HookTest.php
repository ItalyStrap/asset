<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Hook;
use PHPUnit\Framework\Attributes\Test;

final class HookTest extends Unit
{
    #[Test]
    public function itHasTheNamesOfTheEnqueueHooks(): void
    {
        $this->assertSame('wp_enqueue_scripts', Hook::FRONT_END);
        $this->assertSame('admin_enqueue_scripts', Hook::ADMIN);
        $this->assertSame('enqueue_block_editor_assets', Hook::EDITOR);
        $this->assertSame('enqueue_block_assets', Hook::BLOCK);
        $this->assertSame('login_enqueue_scripts', Hook::LOGIN);
    }
}
