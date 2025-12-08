<?php

namespace Vasil\Turshija\Helpers;

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
            return new PageData([], '');
        }

        $props = self::parseFrontMatter(trim(substr($text, $matches[0][0][1], $matches[0][1][1] + 3)));
        $html = self::parseMarkdown((trim(substr($text, $matches[0][1][1] + 3))));

        return new PageData($props, $html, $file);
    }

    private static function parseMarkdown(string $text): string
    {
        // Escape HTML to prevent XSS
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // Fenced code blocks ```code```
        $text = preg_replace_callback('/```(.*?)```/s', function ($matches) {
            $code = htmlspecialchars($matches[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            return "<pre><code>$code</code></pre>";
        }, $text);

        // Blockquotes > quote
        $text = preg_replace_callback('/^(> .+(?:\R> .+)*)/m', function ($matches) {
            $lines = preg_split("/\R/", $matches[0]);
            $lines = array_map(fn($line) => substr($line, 2), $lines); // remove "> "
            $content = implode("\n", $lines);
            return "<blockquote>" . nl2br($content) . "</blockquote>";
        }, $text);

        // Headers # to ######
        for ($i = 6; $i >= 1; $i--) {
            $text = preg_replace("/^" . str_repeat('#', $i) . " (.+)$/m", "<h$i>$1</h$i>", $text);
        }

        // Bold **text**
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);

        // Italic *text*
        $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);

        // Links [text](url)
        $text = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2">$1</a>', $text);

        // Images ![alt](url)
        $text = preg_replace('/!\[(.*?)\]\((.+?)\)/', '<img src="$2" alt="$1">', $text);

        // Ordered lists
        $text = preg_replace_callback('/(^\d+\. .+(?:\R\d+\. .+)*)/m', function ($matches) {
            $lines = preg_split("/\R/", $matches[0]);
            $html = "<ol>";
            foreach ($lines as $line) {
                $line = preg_replace('/^\d+\. /', '', $line);
                $html .= "<li>$line</li>";
            }
            $html .= "</ol>";
            return $html;
        }, $text);

        // Unordered lists
        $text = preg_replace_callback('/(^(\*|\-|\+) .+(?:\R\2 .+)*)/m', function ($matches) {
            $lines = preg_split("/\R/", $matches[0]);
            $html = "<ul>";
            foreach ($lines as $line) {
                $line = preg_replace('/^(\*|\-|\+) /', '', $line);
                $html .= "<li>$line</li>";
            }
            $html .= "</ul>";
            return $html;
        }, $text);

        // Inline code `code`
        $text = preg_replace('/`(.+?)`/', '<code>$1</code>', $text);

        // Paragraphs for lines not already wrapped in a tag
        $lines = preg_split("/\R{2,}/", $text);
        foreach ($lines as &$line) {
            if (!preg_match('/^<(h\d|ul|ol|p|pre|blockquote|img)/', $line)) {
                $line = '<p>' . $line . '</p>';
            }
        }
        $text = implode("\n", $lines);

        return $text;
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
