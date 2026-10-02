<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Vasil\Turshija\Exceptions\TemplateFileDoesNotExist;
use Vasil\Turshija\Helpers\PageData;
use Vasil\Turshija\Helpers\Parse;

final class ParseTest extends TestCase
{
    public function testMissingFile(): void
    {
        $this->expectException(TemplateFileDoesNotExist::class);
        Parse::content('');
    }

    public function testSamplePost(): void
    {
        $page = Parse::content(__DIR__ . '/../fixtures/test-post.md');

        $this->assertInstanceOf(PageData::class, $page);
        $this->assertNotEmpty($page->getHtml());
        $this->assertSame('sample title', $page->getProp('title'));
        $this->assertNull($page->getProp('test-prop'));
        $this->assertSame('/test-post', $page->getUrl());

        $this->assertStringStartsWith('<h1>', $page->getHtml());
    }

    public function testValidDate(): void
    {
        $page = new PageData(['date' => '13-09-2026'], '', 'post.md');

        $this->assertSame('2026-09-13', $page->getDate()->format('Y-m-d'));
    }

    public function testInvalidDateIsTreatedAsMissing(): void
    {
        $page = new PageData(['date' => 'someday'], '', 'post.md');

        $this->assertNull($page->getDate());
    }
}
