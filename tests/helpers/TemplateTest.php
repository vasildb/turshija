<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Vasil\Turshija\Exceptions\TemplateFileDoesNotExist;
use Vasil\Turshija\Exceptions\TemplateInvalidData;
use Vasil\Turshija\Helpers\Template;

final class TemplateTest extends TestCase
{
    public function testRenderExceptions(): void
    {
        $this->expectException(TemplateFileDoesNotExist::class);
        Template::render('not-existing');

        $this->expectException(TemplateInvalidData::class);
        Template::render('index.php', null);
    }

    public function testRender(): void
    {
        $post = Template::render('post.php', ['post' => '<p></p>']);
        $this->assertIsString($post);

        $this->assertStringContainsString('<p></p>', $post);
    }
}
