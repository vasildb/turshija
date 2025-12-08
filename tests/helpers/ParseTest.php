<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Vasil\Turshija\Helpers\PageData;
use Vasil\Turshija\Helpers\Parse;

final class ParseTest extends TestCase
{
    public function testEmptyContent(): void
    {
        $empty = Parse::content('');
        $this->assertInstanceOf(PageData::class, $empty);
        $this->assertEmpty($empty->getHtml());
        $this->assertNull($empty->getProp('test-prop'));
    }

    public function testSamplePost(): void
    {
        $path = __DIR__ . '/../fixtures/test-post.md';
        $content = file_get_contents($path);
        $page = Parse::content($content);

        $this->assertInstanceOf(PageData::class, $page);
        $this->assertNotEmpty($page->getHtml());
        $this->assertNull($page->getProp('test-prop'));

        $this->assertStringStartsWith('<h1>', $page->getHtml());
    }
}
