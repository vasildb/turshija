<?php

namespace Vasil\Turshija\Helpers;

class PageData
{
    private $props = [];
    private $html = null;

    public function __construct($props, $html)
    {
        $this->props = $props;
        $this->html = $html;
    }

    public function getProp($propName)
    {
        if (isset($this->props[$propName])) {
            return $this->props[$propName];
        }
    }

    public function getHtml()
    {
        return $this->html;
    }
}
