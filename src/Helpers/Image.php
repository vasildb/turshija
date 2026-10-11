<?php

namespace Vasil\Turshija\Helpers;

class Image
{
    public static function resize(string $src, string $dest, int $maxWidth, int $maxHeight = PHP_INT_MAX): void
    {
        $info = @getimagesize($src);

        if ($info === false || ($info[2] === IMAGETYPE_GIF && self::isAnimatedGif($src))) {
            File::save($dest, file_get_contents($src));
            return;
        }

        $orientation = self::orientation($src, $info[2]);
        $rotated = $orientation >= 5;
        $width = $rotated ? $info[1] : $info[0];
        $height = $rotated ? $info[0] : $info[1];
        $scale = min(1, $maxWidth / $width, $maxHeight / $height);

        if ($scale >= 1 && $orientation === 1) {
            File::save($dest, file_get_contents($src));
            return;
        }

        $image = imagecreatefromstring(file_get_contents($src));
        $image = self::applyOrientation($image, $orientation);

        if ($scale < 1) {
            $image = imagescale($image, max(1, (int) round($width * $scale)), max(1, (int) round($height * $scale)), IMG_BICUBIC);
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        if (!is_dir(dirname($dest))) {
            mkdir(dirname($dest), 0755, true);
        }

        match ($info[2]) {
            IMAGETYPE_JPEG => imagejpeg($image, $dest, 82),
            IMAGETYPE_PNG => imagepng($image, $dest, 9),
            IMAGETYPE_GIF => imagegif($image, $dest),
            IMAGETYPE_WEBP => imagewebp($image, $dest, 82),
            IMAGETYPE_AVIF => imageavif($image, $dest, 60),
            default => File::save($dest, file_get_contents($src)),
        };
    }

    private static function orientation(string $src, int $type): int
    {
        if ($type !== IMAGETYPE_JPEG) {
            return 1;
        }

        $exif = @exif_read_data($src);
        $orientation = (int) ($exif['Orientation'] ?? 1);

        return $orientation >= 1 && $orientation <= 8 ? $orientation : 1;
    }

    private static function applyOrientation(\GdImage $image, int $orientation): \GdImage
    {
        if (in_array($orientation, [2, 4, 5, 7])) {
            imageflip($image, IMG_FLIP_HORIZONTAL);
        }

        return match ($orientation) {
            3, 4 => imagerotate($image, 180, 0),
            5, 6 => imagerotate($image, 270, 0),
            7, 8 => imagerotate($image, 90, 0),
            default => $image,
        };
    }

    private static function isAnimatedGif(string $src): bool
    {
        return substr_count(file_get_contents($src), "\x00\x21\xF9\x04") > 1;
    }
}
