<?php

namespace Vasil\Turshija\Helpers;

use Vasil\Turshija\Exceptions\TemplateFileDoesNotExist;
use Vasil\Turshija\Exceptions\TemplateInvalidData;

class Template
{
    public static function render($view, $data = []): string
    {
        $templatePath = App::root() . '/templates/default/' . $view;

        if (!file_exists($templatePath)) {
            throw new TemplateFileDoesNotExist($templatePath);
        }

        if (!is_array($data)) {
            throw new TemplateInvalidData();
        }

        foreach ($data as $k => $v) {
            ${$k} = $v;
        }

        ob_start();
        include($templatePath);
        $templateContents = ob_get_clean();
        return $templateContents;
    }
}
