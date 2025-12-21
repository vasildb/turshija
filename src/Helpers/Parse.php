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

        $regex = '/^---/m';
        preg_match_all($regex, $text, $matches, PREG_OFFSET_CAPTURE);

        if (count($matches[0]) !== 2) {
            return new PageData([], '', '');
        }

        $props = self::parseFrontMatter(trim(substr($text, $matches[0][0][1], $matches[0][1][1] + 3)));
        $html = self::parseMarkdown((trim(substr($text, $matches[0][1][1] + 3))));

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
