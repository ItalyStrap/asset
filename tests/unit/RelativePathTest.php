<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\RelativePath;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;

final class RelativePathTest extends Unit
{
    #[Test]
    public function itHasThePathOfTheFileFromTheFolder(): void
    {
        $this->assertSame('build/index.js', $this->fromPlugin()->of($this->file('plugin/build/index.js')));
    }

    #[Test]
    public function itResolvesTheDotsOfThePath(): void
    {
        $this->assertSame('build/index.js', $this->fromPlugin()->of($this->file('plugin/build/../build/index.js')));
    }

    #[Test]
    public function itIsNullForAFileOutsideTheFolder(): void
    {
        $this->assertNull($this->fromPlugin()->of($this->file('build/index.js')));
    }

    #[Test]
    public function itIsNullForAFolderWithTheSameBeginning(): void
    {
        $relativePath = new RelativePath($this->file('build'));

        $this->assertNull($relativePath->of($this->file('build-other/index.js')));
    }

    #[Test]
    public function itIsNullForAMissingFile(): void
    {
        $this->assertNull($this->fromPlugin()->of($this->file('plugin/build/missing.js')));
    }

    #[Test]
    public function itIsNullForAMissingFolder(): void
    {
        $relativePath = new RelativePath($this->file('missing'));

        $this->assertNull($relativePath->of($this->file('plugin/build/index.js')));
    }

    #[Test]
    public function itThrowsForAFileOutsideTheFolder(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#The file ".*build/index\.js" is not in ".*_data/plugin"#');

        $this->fromPlugin()->orFail($this->file('build/index.js'));
    }

    #[Test]
    public function itDoesNotThrowForAFileInTheFolder(): void
    {
        $this->assertSame('build/index.js', $this->fromPlugin()->orFail($this->file('plugin/build/index.js')));
    }

    private function fromPlugin(): RelativePath
    {
        return new RelativePath($this->file('plugin'));
    }

    private function file(string $name): SplFileInfo
    {
        return new SplFileInfo(\rtrim((string) \codecept_data_dir(), '/') . '/' . $name);
    }
}
