<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\Handle;
use PHPUnit\Framework\Attributes\Test;

final class HandleTest extends Unit
{
    #[Test]
    public function itKeepsTheHandle(): void
    {
        $this->assertSame('my-script', (new Handle('my-script'))->value());
    }

    #[Test]
    public function itTrimsTheHandle(): void
    {
        $this->assertSame('my-script', (new Handle(' my-script '))->value());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function emptyHandles(): iterable
    {
        yield 'empty' => [''];
        yield 'spaces' => ['   '];
    }

    #[Test]
    #[DataProvider('emptyHandles')]
    public function itRejectsAnEmptyHandle(string $handle): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Handle($handle);
    }
}
