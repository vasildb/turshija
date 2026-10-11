<?php

namespace Vasil\Turshija\Helpers;

use Parsedown;
use Vasil\Turshija\Exceptions\TemplateFileDoesNotExist;

class Parse
{
    public static function content(string $file): PageData
    {
        if (!file_exists($file))
            throw new TemplateFileDoesNotExist($file);

        $text = file_get_contents($file);

        if (!preg_match('/\A---[ \t]*\R(?:.*?\R)?---[ \t]*(?:\R|\z)/s', $text, $matches)) {
            return new PageData([], '', '');
        }

        $props = self::parseFrontMatter(trim($matches[0]));
        $html = self::parseMarkdown(trim(substr($text, strlen($matches[0]))));

        return new PageData($props, $html, $file);
    }

    private static function parseMarkdown(string $text): string
    {
        $Parsedown = new Parsedown();

        return $Parsedown->text($text);
    }

    private static function parseFrontMatter($text): array
    {
        $data = [];

        // Extract content between first and second '---' lines
        if (preg_match('/^---\R(.*?)\R---/s', $text, $matches)) {
            $frontMatter = trim($matches[1]);

            // Split into lines
            foreach (preg_split("/\R/", $frontMatter) as $line) {
                $line = trim($line);

                // Skip empty lines and comment lines
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }

                // Match key: value
                if (preg_match('/^([^:]+):\s*(.*)$/u', $line, $m)) {
                    $key = trim($m[1]);
                    $value = trim($m[2]);

                    $key = strtolower($key);

                    $data[$key] = $value;
                }
            }
        }

        return $data;
    }
}
