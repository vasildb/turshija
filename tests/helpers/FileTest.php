<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Vasil\Turshija\Helpers\File;

final class FileTest extends TestCase
{
    private string $tmp;

    protected function setUp(): void
    {
        $this->tmp = sys_get_temp_dir() . '/turshija test ' . uniqid();
        mkdir($this->tmp);
    }

    protected function tearDown(): void
    {
        exec('rm -rf ' . escapeshellarg($this->tmp));
    }

    public function testCopyDirWithSpacesInPath(): void
    {
        $src = $this->tmp . '/my assets';
        $dest = $this->tmp . '/out dir/assets';
        mkdir($src . '/css', 0755, true);
        file_put_contents($src . '/css/style.css', 'body {}');
        file_put_contents($src . '/top.txt', 'top');

        File::copyDir($src, $dest);

        $this->assertSame('body {}', file_get_contents($dest . '/css/style.css'));
        $this->assertSame('top', file_get_contents($dest . '/top.txt'));
    }

    public function testCopyDirMergesIntoExisting(): void
    {
        $a = $this->tmp . '/a';
        $b = $this->tmp . '/b';
        $dest = $this->tmp . '/dest';
        mkdir($a . '/images', 0755, true);
        mkdir($b . '/images', 0755, true);
        file_put_contents($a . '/images/one.txt', '1');
        file_put_contents($b . '/images/two.txt', '2');

        File::copyDir($a, $dest);
        File::copyDir($b, $dest);

        $this->assertFileExists($dest . '/images/one.txt');
        $this->assertFileExists($dest . '/images/two.txt');
    }

    public function testCopyDirIgnoresMissingSource(): void
    {
        File::copyDir($this->tmp . '/nope', $this->tmp . '/dest');

        $this->assertDirectoryDoesNotExist($this->tmp . '/dest');
    }
}
