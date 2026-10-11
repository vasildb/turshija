<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Vasil\Turshija\Helpers\Image;

final class ImageTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/turshija-image-' . uniqid();
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        exec('rm -rf ' . escapeshellarg($this->dir));
    }

    public function testWideImageIsResized(): void
    {
        imagejpeg(imagecreatetruecolor(2000, 1500), $this->dir . '/wide.jpg');
        Image::resize($this->dir . '/wide.jpg', $this->dir . '/out/wide.jpg', 1000);

        $this->assertSame([1000, 750], array_slice(getimagesize($this->dir . '/out/wide.jpg'), 0, 2));
    }

    public function testSmallImageIsCopiedUnchanged(): void
    {
        imagejpeg(imagecreatetruecolor(800, 600), $this->dir . '/small.jpg');
        Image::resize($this->dir . '/small.jpg', $this->dir . '/out/small.jpg', 1000);

        $this->assertFileEquals($this->dir . '/small.jpg', $this->dir . '/out/small.jpg');
    }

    public function testPngKeepsTransparency(): void
    {
        $png = imagecreatetruecolor(1200, 600);
        imagealphablending($png, false);
        imagesavealpha($png, true);
        imagefill($png, 0, 0, imagecolorallocatealpha($png, 0, 0, 0, 127));
        imagepng($png, $this->dir . '/alpha.png');

        Image::resize($this->dir . '/alpha.png', $this->dir . '/out/alpha.png', 1000);
        $out = imagecreatefrompng($this->dir . '/out/alpha.png');

        $this->assertSame(1000, imagesx($out));
        $this->assertSame(127, imagecolorsforindex($out, imagecolorat($out, 10, 10))['alpha']);
    }

    public function testExifOrientationIsApplied(): void
    {
        $img = imagecreatetruecolor(2000, 1000);
        imagefilledrectangle($img, 0, 0, 999, 999, imagecolorallocate($img, 255, 0, 0));
        ob_start();
        imagejpeg($img, null, 95);
        $jpeg = ob_get_clean();
        file_put_contents($this->dir . '/rotated.jpg', $this->withOrientation($jpeg, 6));

        Image::resize($this->dir . '/rotated.jpg', $this->dir . '/out/rotated.jpg', 1000);
        $out = imagecreatefromjpeg($this->dir . '/out/rotated.jpg');

        $this->assertSame([1000, 2000], [imagesx($out), imagesy($out)]);
        $this->assertGreaterThan(200, (imagecolorat($out, 500, 100) >> 16) & 0xFF);
        $this->assertLessThan(50, (imagecolorat($out, 500, 1900) >> 16) & 0xFF);
    }

    private function withOrientation(string $jpeg, int $orientation): string
    {
        $tiff = "MM\x00\x2A\x00\x00\x00\x08" . "\x00\x01" . "\x01\x12\x00\x03\x00\x00\x00\x01" . pack('n', $orientation) . "\x00\x00" . "\x00\x00\x00\x00";
        $app1 = "Exif\x00\x00" . $tiff;

        return "\xFF\xD8\xFF\xE1" . pack('n', strlen($app1) + 2) . $app1 . substr($jpeg, 2);
    }
}
