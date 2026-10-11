<?php

namespace Vasil\Turshija\Helpers;

use DateTime;

class PageData
{
    private array $props = [];
    private string $html = '';

    private ?DateTime $date = null;
    private bool $hasDay = true;
    private string $url = '';
    private array $photos = [];

    public function __construct($props, $html, $file)
    {
        $this->props = $props;
        $this->html = $html;

        $date = $this->getProp('date');
        if (!empty($date)) {
            foreach (['!M Y', '!F Y', '!Y-m'] as $format) {
                $monthOnly = DateTime::createFromFormat($format, $date);

                if ($monthOnly !== false && !DateTime::getLastErrors()) {
                    $this->date = $monthOnly;
                    $this->hasDay = false;
                    break;
                }
            }

            if ($this->date === null) {
                try {
                    $this->date = new DateTime($date);
                } catch (\Exception) {
                    // Invalid dates are treated as missing
                }
            }
        }

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

    public function getDisplayDate(): string
    {
        return $this->date?->format($this->hasDay ? 'd M Y' : 'M Y') ?? '';
    }

    public function getW3cDate(): string
    {
        return $this->date?->format($this->hasDay ? 'Y-m-d' : 'Y-m') ?? '';
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPhotos(): array
    {
        return $this->photos;
    }

    public function setPhotos(array $photos): void
    {
        $this->photos = $photos;
    }

    private function fileToUrl(string $file): string
    {
        $path = '/' . substr(basename($file), 0, -3);

        return $path;
    }
}
