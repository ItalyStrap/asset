<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit\Domain;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Domain\Always;
use ItalyStrap\Asset\Domain\When;
use PHPUnit\Framework\Attributes\Test;

final class ConditionTest extends Unit
{
    #[Test]
    public function alwaysIsSatisfied(): void
    {
        $this->assertTrue((new Always())->isSatisfied());
    }

    #[Test]
    public function whenAsksTheCallbackEachTime(): void
    {
        $calls = 0;
        $condition = new When(static function () use (&$calls): bool {
            $calls++;
            return $calls === 1;
        });

        $this->assertTrue($condition->isSatisfied());
        $this->assertFalse($condition->isSatisfied());
        $this->assertSame(2, $calls);
    }

    #[Test]
    public function whenAcceptsAnArrayCallable(): void
    {
        $this->assertFalse((new When($this->deniesTheConsent(...)))->isSatisfied());
    }

    #[Test]
    public function whenCastsTheResultToBool(): void
    {
        $this->assertFalse((new When(static fn (): int => 0))->isSatisfied());
        $this->assertTrue((new When(static fn (): string => 'yes'))->isSatisfied());
    }

    public function deniesTheConsent(): bool
    {
        return false;
    }
}
