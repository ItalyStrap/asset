<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit\Domain;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\Domain\Translations;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;

final class TranslationsTest extends Unit
{
    #[Test]
    public function itHasTheDomainAndNoPathByDefault(): void
    {
        $translations = new Translations('my-domain');

        $this->assertSame('my-domain', $translations->domain());
        $this->assertNull($translations->path());
    }

    #[Test]
    public function itHasThePath(): void
    {
        $translations = new Translations('my-domain', new SplFileInfo('/plugin/languages'));

        $this->assertSame('/plugin/languages', $translations->path());
    }

    #[Test]
    public function itRejectsAnEmptyDomain(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Translations('');
    }
}
