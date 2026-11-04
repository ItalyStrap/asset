<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\FolderUrls;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;

final class FolderUrlsTest extends Unit
{
    #[Test]
    public function itHasTheUrlFromTheFirstFolderWithTheFile(): void
    {
        $urls = new FolderUrls([
            $this->path('themes/child-theme') => 'https://example.com/child',
            $this->path('themes/parent-theme') => 'https://example.com/parent',
        ]);

        $this->assertSame(
            'https://example.com/child/build/child.js',
            $urls->url(new SplFileInfo($this->path('themes/child-theme/build/child.js')))
        );
        $this->assertSame(
            'https://example.com/parent/build/parent.js',
            $urls->url(new SplFileInfo($this->path('themes/parent-theme/build/parent.js')))
        );
    }

    #[Test]
    public function theFirstFolderWinsWhenTheFoldersAreNested(): void
    {
        $urls = new FolderUrls([
            $this->path('themes') => 'https://example.com/themes',
            $this->path('themes/child-theme') => 'https://example.com/child',
        ]);

        $this->assertSame(
            'https://example.com/themes/child-theme/build/child.js',
            $urls->url(new SplFileInfo($this->path('themes/child-theme/build/child.js')))
        );
    }

    #[Test]
    public function itThrowsWhenNoFolderHasTheFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#The file ".*build/index\.js" is not in the folders .*_data/themes$#');

        (new FolderUrls([$this->path('themes') => 'https://example.com/themes']))
            ->url(new SplFileInfo($this->path('build/index.js')));
    }

    private function path(string $name): string
    {
        return \rtrim((string) \codecept_data_dir(), '/') . '/' . $name;
    }
}
