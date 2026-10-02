<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Vasil\Turshija\Exceptions\TemplateFileDoesNotExist;
use Vasil\Turshija\Exceptions\TemplateInvalidData;
use Vasil\Turshija\Helpers\Template;

final class TemplateTest extends TestCase
{
    public function testMissingTemplate(): void
    {
        $this->expectException(TemplateFileDoesNotExist::class);
        Template::render('not-existing');
    }

    public function testInvalidData(): void
    {
        $this->expectException(TemplateInvalidData::class);
        Template::render('index.php', null);
    }

    public function testRender(): void
    {
        $post = Template::render('post.php', ['post' => '<p></p>', 'title' => 'Title']);
        $this->assertIsString($post);

        $this->assertStringContainsString('<p></p>', $post);
        $this->assertStringContainsString('Title', $post);
    }

    public function testTitleIsEscaped(): void
    {
        $post = Template::render('post.php', ['post' => '<p></p>', 'title' => 'Say "hi" & <b>x</b>']);
        $this->assertStringContainsString('Say &quot;hi&quot; &amp; &lt;b&gt;x&lt;/b&gt;', $post);

        $header = Template::render('header.php', ['title' => 'A "quoted" site']);
        $this->assertStringContainsString('data-glitch="A &quot;quoted&quot; site"', $header);
    }
}
