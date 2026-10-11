<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Vasil\Turshija\Exceptions\TemplateFileDoesNotExist;
use Vasil\Turshija\Exceptions\TemplateInvalidData;
use Vasil\Turshija\Helpers\PageData;
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

    public function testHomepagePhotos(): void
    {
        $html = Template::render('homepage.php', ['posts' => [], 'photos' => []]);
        $this->assertStringNotContainsString('class="photos"', $html);

        $html = Template::render('homepage.php', ['posts' => [], 'photos' => ['a b.jpg']]);
        $this->assertStringContainsString('<a href="/assets/photos/a%20b.jpg" target="_blank" rel="noopener"><img src="/assets/photos/thumbs/a%20b.jpg"', $html);
    }

    public function testPostPhotos(): void
    {
        $html = Template::render('post.php', ['post' => '', 'title' => 'T', 'photos' => []]);
        $this->assertStringNotContainsString('class="photos"', $html);

        $html = Template::render('post.php', ['post' => '', 'title' => 'T', 'photos' => ['/assets/photos/IMG_1.jpeg']]);
        $this->assertStringContainsString('<a href="/assets/photos/IMG_1.jpeg" target="_blank" rel="noopener"><img src="/assets/photos/IMG_1.jpeg"', $html);

        $post = new PageData(['title' => 'T', 'date' => '2026-01-01'], '', '/posts/hike.md');
        $post->setPhotos(['IMG 2.jpeg']);
        $html = Template::render('homepage.php', ['posts' => [$post], 'photos' => []]);
        $this->assertStringContainsString('<a class="post-photos" href="/hike">', $html);
        $this->assertStringContainsString('<img src="/assets/photos/thumbs/IMG%202.jpeg"', $html);
    }

    public function testTitleIsEscaped(): void
    {
        $post = Template::render('post.php', ['post' => '<p></p>', 'title' => 'Say "hi" & <b>x</b>']);
        $this->assertStringContainsString('Say &quot;hi&quot; &amp; &lt;b&gt;x&lt;/b&gt;', $post);

        $header = Template::render('header.php', ['title' => 'A "quoted" site']);
        $this->assertStringContainsString('data-glitch="A &quot;quoted&quot; site"', $header);
    }
}
