<?php

namespace Vasil\Turshija\Helpers;

use DateTime;

class PageData
{
    private array $props = [];
    private string $html = '';

    private ?DateTime $date = null;
    private string $url = '';

    public function __construct($props, $html, $file)
    {
        $this->props = $props;
        $this->html = $html;

        $date = $this->getProp('date');
        if (!empty($date))
            $this->date = new DateTime($date);

        $this->url = $this->fileToUrl($file);
    }

    public function getProp($propName)
    {
        if (isset($this->props[$propName])) {
            return $this->props[$propName];
        }

        return null;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getDate(): DateTime|null
    {
        return $this->date;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    private function fileToUrl(string $file): string
    {
        $path = substr($file, strlen(App::contentsDir()));
        $path = substr($path, 0, -3) . '.html';

        return $path;
    }
}
