<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit\Domain;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\Domain\InlineScript;
use ItalyStrap\Asset\Domain\Position;
use PHPUnit\Framework\Attributes\Test;

final class InlineScriptTest extends Unit
{
    #[Test]
    public function itPrintsAfterTheScriptByDefault(): void
    {
        $inline = new InlineScript('init();');

        $this->assertSame('init();', $inline->code());
        $this->assertSame('after', $inline->position()->value);
    }

    #[Test]
    public function itPrintsBeforeTheScript(): void
    {
        $this->assertSame('before', (new InlineScript('var a;', Position::Before))->position()->value);
    }

    #[Test]
    public function itRejectsEmptyCode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InlineScript(' ');
    }
}
